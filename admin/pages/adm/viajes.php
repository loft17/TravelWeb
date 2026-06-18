<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/viajes.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/activity_log.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        $nombre      = trim($_POST['nombre'] ?? '');
        $destino     = trim($_POST['destino'] ?? '');
        $fecha_inicio = $_POST['fecha_inicio'] ?: null;
        $fecha_fin    = $_POST['fecha_fin']    ?: null;
        if ($nombre === '') {
            $error = 'El nombre del viaje es obligatorio.';
        } else {
            $conn = conectar_bd();
            $stmt = $conn->prepare("INSERT INTO viajes (nombre, destino, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre, $destino, $fecha_inicio, $fecha_fin);
            $stmt->execute();
            $newId = (int)$conn->insert_id;
            $stmt->close();
            $conn->close();
            log_activity('viaje_creado', $nombre);
            $_SESSION['viaje_id'] = $newId;
            $success = "Viaje \"$nombre\" creado y seleccionado.";
        }
    } elseif ($accion === 'editar') {
        $id          = intval($_POST['id'] ?? 0);
        $nombre      = trim($_POST['nombre'] ?? '');
        $destino     = trim($_POST['destino'] ?? '');
        $fecha_inicio = $_POST['fecha_inicio'] ?: null;
        $fecha_fin    = $_POST['fecha_fin']    ?: null;
        if ($id > 0 && $nombre !== '') {
            $conn = conectar_bd();
            $stmt = $conn->prepare("UPDATE viajes SET nombre=?, destino=?, fecha_inicio=?, fecha_fin=? WHERE id=?");
            $stmt->bind_param("ssssi", $nombre, $destino, $fecha_inicio, $fecha_fin, $id);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            log_activity('viaje_editado', "ID $id: $nombre");
            $success = "Viaje actualizado.";
        }
    } elseif ($accion === 'borrar') {
        $id = intval($_POST['id'] ?? 0);
        $conn = conectar_bd();
        $total = (int)$conn->query("SELECT COUNT(*) AS c FROM viajes")->fetch_assoc()['c'];
        if ($total <= 1) {
            $error = 'No puedes eliminar el único viaje existente.';
        } elseif ($id > 0) {
            $conn->query("DELETE FROM viajes WHERE id = $id");
            log_activity('viaje_borrado', "ID $id");
            if ((int)$_SESSION['viaje_id'] === $id) {
                $row = $conn->query("SELECT id FROM viajes ORDER BY id ASC LIMIT 1")->fetch_assoc();
                $_SESSION['viaje_id'] = $row ? (int)$row['id'] : 1;
            }
            $success = 'Viaje eliminado.';
        }
        $conn->close();
    } elseif ($accion === 'seleccionar') {
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $_SESSION['viaje_id'] = $id;
            $success = 'Viaje seleccionado.';
        }
    }
}

$viajes       = get_all_viajes();
$viajeActivo  = get_viaje_activo_id();

include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
?>

<body>
<div class="page-container">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php'; ?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php'; ?>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="row">
                <div class="col-12 mt-5">

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <!-- Lista de viajes -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="header-title">Mis Viajes</h4>
                            <div class="table-responsive">
                                <table class="table text-center">
                                    <thead class="bg-dark text-white text-uppercase">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Destino</th>
                                            <th>Inicio</th>
                                            <th>Fin</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($viajes as $v): ?>
                                        <tr class="<?= (int)$v['id'] === $viajeActivo ? 'table-success' : '' ?>">
                                            <td>
                                                <strong><?= htmlspecialchars($v['nombre']) ?></strong>
                                                <?php if ((int)$v['id'] === $viajeActivo): ?>
                                                    <span class="badge badge-success ml-1">Activo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($v['destino']) ?></td>
                                            <td><?= $v['fecha_inicio'] ? date('d/m/Y', strtotime($v['fecha_inicio'])) : '—' ?></td>
                                            <td><?= $v['fecha_fin']    ? date('d/m/Y', strtotime($v['fecha_fin']))    : '—' ?></td>
                                            <td>
                                                <?php if ((int)$v['id'] !== $viajeActivo): ?>
                                                <form method="post" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                                    <input type="hidden" name="accion" value="seleccionar">
                                                    <input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
                                                    <button class="btn btn-sm btn-primary">Seleccionar</button>
                                                </form>
                                                <?php endif; ?>
                                                <button class="btn btn-sm btn-warning"
                                                    onclick="abrirEditar(<?= (int)$v['id'] ?>, '<?= htmlspecialchars(addslashes($v['nombre'])) ?>', '<?= htmlspecialchars(addslashes($v['destino'])) ?>', '<?= $v['fecha_inicio'] ?>', '<?= $v['fecha_fin'] ?>')">
                                                    Editar
                                                </button>
                                                <form method="post" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                                    <input type="hidden" name="accion" value="borrar">
                                                    <input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
                                                    <button class="btn btn-sm btn-danger"
                                                        data-confirm="¿Eliminar el viaje «<?= htmlspecialchars($v['nombre']) ?>»? Se eliminará el viaje pero NO sus datos.">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Crear nuevo viaje -->
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Nuevo Viaje</h4>
                            <form method="post">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="accion" value="crear">
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" name="nombre" placeholder="Nombre (ej: Tokio 2026)" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" name="destino" placeholder="Destino (ej: Japón)">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Fecha inicio</label>
                                        <input type="date" class="form-control" name="fecha_inicio">
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Fecha fin</label>
                                        <input type="date" class="form-control" name="fecha_fin">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">Crear y seleccionar</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal editar -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Viaje</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" class="form-control" name="nombre" id="edit_nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Destino</label>
                        <input type="text" class="form-control" name="destino" id="edit_destino">
                    </div>
                    <div class="form-group">
                        <label>Fecha inicio</label>
                        <input type="date" class="form-control" name="fecha_inicio" id="edit_fecha_inicio">
                    </div>
                    <div class="form-group">
                        <label>Fecha fin</label>
                        <input type="date" class="form-control" name="fecha_fin" id="edit_fecha_fin">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php'; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php'; ?>
<script>
function abrirEditar(id, nombre, destino, fi, ff) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('edit_destino').value = destino;
    document.getElementById('edit_fecha_inicio').value = fi || '';
    document.getElementById('edit_fecha_fin').value = ff || '';
    $('#modalEditar').modal('show');
}
</script>
</body>
</html>
