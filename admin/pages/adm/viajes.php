<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/viajes.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/activity_log.php';

$error   = '';
$success = '';

// Datos para re-poblar el modal de crear en caso de error
$formBack = ['nombre' => '', 'destino' => '', 'fecha_inicio' => '', 'fecha_fin' => ''];
$reopenCreate = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        $nombre       = trim($_POST['nombre'] ?? '');
        $destino      = trim($_POST['destino'] ?? '');
        $fecha_inicio = $_POST['fecha_inicio'] ?: null;
        $fecha_fin    = $_POST['fecha_fin']    ?: null;

        $formBack = [
            'nombre'       => $nombre,
            'destino'      => $destino,
            'fecha_inicio' => $fecha_inicio ?? '',
            'fecha_fin'    => $fecha_fin    ?? '',
        ];

        if ($nombre === '') {
            $error = 'El nombre del viaje es obligatorio.';
        } elseif (!$fecha_inicio || !$fecha_fin) {
            $error = 'Las fechas de inicio y fin son obligatorias.';
        } elseif ($fecha_fin < $fecha_inicio) {
            $error = 'La fecha de fin debe ser posterior a la de inicio.';
        } else {
            $conn = conectar_bd();
            $stmt = $conn->prepare(
                "SELECT nombre FROM viajes WHERE activo = 1 AND fecha_inicio <= ? AND fecha_fin >= ? LIMIT 1"
            );
            $stmt->bind_param("ss", $fecha_fin, $fecha_inicio);
            $stmt->execute();
            $solapado = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($solapado) {
                $error = 'Las fechas se solapan con el viaje «' . htmlspecialchars($solapado['nombre']) . '».';
                $conn->close();
            } else {
                $stmt = $conn->prepare("INSERT INTO viajes (nombre, destino, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $nombre, $destino, $fecha_inicio, $fecha_fin);
                $stmt->execute();
                $newId = (int)$conn->insert_id;
                $stmt->close();
                $conn->close();
                log_activity('viaje_creado', $nombre);
                $_SESSION['viaje_id'] = $newId;
                $success = "Viaje \"$nombre\" creado y seleccionado.";
                $formBack = ['nombre' => '', 'destino' => '', 'fecha_inicio' => '', 'fecha_fin' => ''];
            }
        }

        if ($error) $reopenCreate = true;

    } elseif ($accion === 'editar') {
        $id           = intval($_POST['id'] ?? 0);
        $nombre       = trim($_POST['nombre'] ?? '');
        $destino      = trim($_POST['destino'] ?? '');
        $fecha_inicio = $_POST['fecha_inicio'] ?: null;
        $fecha_fin    = $_POST['fecha_fin']    ?: null;
        if ($id <= 0 || $nombre === '') {
            $error = 'Datos inválidos.';
        } elseif (!$fecha_inicio || !$fecha_fin) {
            $error = 'Las fechas de inicio y fin son obligatorias.';
        } elseif ($fecha_fin < $fecha_inicio) {
            $error = 'La fecha de fin debe ser posterior a la de inicio.';
        } else {
            $conn = conectar_bd();
            $stmt = $conn->prepare(
                "SELECT nombre FROM viajes WHERE activo = 1 AND id != ? AND fecha_inicio <= ? AND fecha_fin >= ? LIMIT 1"
            );
            $stmt->bind_param("iss", $id, $fecha_fin, $fecha_inicio);
            $stmt->execute();
            $solapado = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($solapado) {
                $error = 'Las fechas se solapan con el viaje «' . htmlspecialchars($solapado['nombre']) . '».';
                $conn->close();
            } else {
                $stmt = $conn->prepare("UPDATE viajes SET nombre=?, destino=?, fecha_inicio=?, fecha_fin=? WHERE id=?");
                $stmt->bind_param("ssssi", $nombre, $destino, $fecha_inicio, $fecha_fin, $id);
                $stmt->execute();
                $stmt->close();
                $conn->close();
                log_activity('viaje_editado', "ID $id: $nombre");
                $success = "Viaje actualizado.";
            }
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
            $success = 'Viaje seleccionado para edición.';
        }
    }
}

$viajes      = get_all_viajes();
$viajeActivo = get_viaje_activo_id();

// Viaje mostrado públicamente (misma lógica que plan/includes/viaje.php)
$today = date('Y-m-d');
$viajePublicoId = 0;
foreach ($viajes as $v) {
    if ($v['activo'] && $v['fecha_inicio'] <= $today && $v['fecha_fin'] >= $today) {
        $viajePublicoId = (int)$v['id'];
        break;
    }
}
if (!$viajePublicoId) {
    $proximos = array_filter($viajes, fn($v) => $v['activo'] && $v['fecha_inicio'] > $today);
    usort($proximos, fn($a, $b) => strcmp($a['fecha_inicio'], $b['fecha_inicio']));
    $first = reset($proximos);
    if ($first) {
        $viajePublicoId = (int)$first['id'];
    } else {
        $recientes = array_filter($viajes, fn($v) => $v['activo']);
        usort($recientes, fn($a, $b) => strcmp($b['fecha_fin'], $a['fecha_fin']));
        $first = reset($recientes);
        if ($first) $viajePublicoId = (int)$first['id'];
    }
}

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

                    <?php if ($error && !$reopenCreate): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <div class="alert alert-info" style="font-size:0.9em;">
                        <strong>Cambio automático:</strong> La web pública muestra el viaje cuyas fechas incluyan el día de hoy.
                        Si no hay ninguno activo, se muestra el próximo viaje programado.
                    </div>

                    <!-- Lista de viajes -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="header-title mb-0">Mis Viajes</h4>
                                <button type="button" class="btn btn-success btn-sm"
                                        data-toggle="modal" data-target="#modalCrear">
                                    + Nuevo Viaje
                                </button>
                            </div>
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
                                                    <span class="badge badge-success ml-1">Editando</span>
                                                <?php endif; ?>
                                                <?php if ((int)$v['id'] === $viajePublicoId): ?>
                                                    <span class="badge badge-primary ml-1">En web</span>
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
                                                    <button class="btn btn-sm btn-secondary">Editar éste</button>
                                                </form>
                                                <?php endif; ?>
                                                <button class="btn btn-sm btn-warning"
                                                    onclick="abrirEditar(<?= (int)$v['id'] ?>, '<?= htmlspecialchars(addslashes($v['nombre'])) ?>', '<?= htmlspecialchars(addslashes($v['destino'])) ?>', '<?= $v['fecha_inicio'] ?>', '<?= $v['fecha_fin'] ?>')">
                                                    Fechas
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

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Crear viaje -->
<div class="modal fade" id="modalCrear" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Viaje</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="post" id="formCrear">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="accion" value="crear">
                <div class="modal-body">
                    <?php if ($reopenCreate && $error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <div class="form-row">
                        <div class="col-sm-7 form-group">
                            <label>Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre"
                                   placeholder="ej: Tokio 2026" required
                                   value="<?= htmlspecialchars($formBack['nombre']) ?>">
                        </div>
                        <div class="col-sm-5 form-group">
                            <label>Destino</label>
                            <input type="text" class="form-control" name="destino"
                                   placeholder="ej: Japón"
                                   value="<?= htmlspecialchars($formBack['destino']) ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col form-group">
                            <label>Fecha inicio <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fecha_inicio" required
                                   value="<?= htmlspecialchars($formBack['fecha_inicio']) ?>">
                        </div>
                        <div class="col form-group">
                            <label>Fecha fin <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fecha_fin" required
                                   value="<?= htmlspecialchars($formBack['fecha_fin']) ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Crear viaje</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Editar viaje -->
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
                    <div class="form-row">
                        <div class="col-sm-7 form-group">
                            <label>Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="edit_nombre" required>
                        </div>
                        <div class="col-sm-5 form-group">
                            <label>Destino</label>
                            <input type="text" class="form-control" name="destino" id="edit_destino">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col form-group">
                            <label>Fecha inicio <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fecha_inicio" id="edit_fecha_inicio" required>
                        </div>
                        <div class="col form-group">
                            <label>Fecha fin <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fecha_fin" id="edit_fecha_fin" required>
                        </div>
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

<?php if ($reopenCreate): ?>
$('#modalCrear').modal('show');
<?php endif; ?>
</script>
</body>
</html>
