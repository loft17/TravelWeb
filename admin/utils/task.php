<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include '../includes/templates/head.php';  
include '../../config.php';

// Conectar a la base de datos
$conn = conectar_bd();

// Manejo de acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = filter_input(INPUT_POST, 'accion', FILTER_SANITIZE_STRING);

    if ($accion === 'crear') {
        $stmt = $conn->prepare("INSERT INTO tareas (titulo, fecha_inicio, fecha_fin, info, url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "sssss", 
            filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING),
            filter_input(INPUT_POST, 'fecha_inicio', FILTER_SANITIZE_STRING),
            filter_input(INPUT_POST, 'fecha_fin', FILTER_SANITIZE_STRING),
            filter_input(INPUT_POST, 'info', FILTER_SANITIZE_STRING),
            filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL)
        );
        $stmt->execute();
    } elseif ($accion === 'borrar') {
        $stmt = $conn->prepare("DELETE FROM tareas WHERE id=?");
        $stmt->bind_param("i", filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        $stmt->execute();
    } elseif ($accion === 'completar') {
        $stmt = $conn->prepare("UPDATE tareas SET completado=1, fecha_terminada=NOW() WHERE id=?");
        $stmt->bind_param("i", filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        $stmt->execute();
    } elseif ($accion === 'editar') {
        $stmt = $conn->prepare("UPDATE tareas SET titulo=?, fecha_inicio=?, fecha_fin=?, info=?, url=? WHERE id=?");
        $stmt->bind_param(
            "sssssi",
            filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING),
            filter_input(INPUT_POST, 'fecha_inicio', FILTER_SANITIZE_STRING),
            filter_input(INPUT_POST, 'fecha_fin', FILTER_SANITIZE_STRING),
            filter_input(INPUT_POST, 'info', FILTER_SANITIZE_STRING),
            filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL),
            filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT)
        );
        $stmt->execute();
    }
}

// Obtener las tareas ordenadas
$tareas = $conn->query("SELECT * FROM tareas ORDER BY completado ASC, fecha_creada DESC");

date_default_timezone_set('Europe/Madrid');
$hoy = date('Y-m-d');
?>

<!doctype html>
<html lang="en">

<body>
    <div class="page-container">
        <?php include '../includes/templates/sidebar.php'; ?>
        <?php include '../includes/templates/user-profile.php'; ?>
        <div class="main-content">

            <!-- page title area end -->
            <div class="main-content-inner">
                <div class="row">
                    <!-- data table start -->

                    <!-- Progress Table start -->
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title">Progress Table</h4>
                                <div class="single-table">
                                    <div class="table-responsive">
                                        <form method="post" class="mb-4">
                                            <input type="hidden" name="accion" value="crear">
                                            <div class="form-group">
                                                <input type="text" name="titulo" class="form-control"
                                                    placeholder="Título de la tarea" required>
                                            </div>
                                            <div class="form-row">
                                                <div class="col">
                                                    <input type="date" name="fecha_inicio" class="form-control"
                                                        value="<?= $hoy ?>">
                                                </div>
                                                <div class="col">
                                                    <input type="date" name="fecha_fin" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" name="info" class="form-control"
                                                    placeholder="Información adicional">
                                            </div>
                                            <div class="form-group">
                                                <input type="url" name="url" class="form-control"
                                                    placeholder="URL relacionada">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Crear Tarea</button>
                                        </form>
                                        <hr>
                                        <table class="table table-hover progress-table text-center">
                                            <thead class="text-uppercase bg-dark">
                                                <tr class="text-white">
                                                    <th scope="col">Título</th>
                                                    <th scope="col">Inicio</th>
                                                    <th scope="col">Fin</th>
                                                    <th scope="col">Info</th>
                                                    <th scope="col">URL</th>
                                                    <th scope="col">Terminada</th>
                                                    <th scope="col">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($tarea = $tareas->fetch_assoc()): ?>
                                                <tr class="<?= $tarea['completado'] ? 'table-success' : '' ?>">
                                                    <td><?= htmlspecialchars($tarea['titulo']) ?></td>
                                                    <td><?= htmlspecialchars($tarea['fecha_inicio']) ?></td>
                                                    <td><?= htmlspecialchars($tarea['fecha_fin']) ?></td>
                                                    <td><?= htmlspecialchars($tarea['info']) ?></td>
                                                    <td><a href="<?= htmlspecialchars($tarea['url']) ?>"
                                                            target="_blank">Enlace</a></td>
                                                    <td><?= $tarea['fecha_terminada'] ?></td>
                                                    <td>
                                                        <form method="post" style="display:inline">
                                                            <input type="hidden" name="accion" value="editar">
                                                            <input type="hidden" name="id" value="<?= $tarea['id'] ?>">
                                                            <button type="button" class="btn btn-warning btn-sm"
                                                                onclick="editarTarea(<?= $tarea['id'] ?>, '<?= addslashes($tarea['titulo']) ?>', '<?= $tarea['fecha_inicio'] ?>', '<?= $tarea['fecha_fin'] ?>', '<?= addslashes($tarea['info']) ?>', '<?= $tarea['url'] ?>')"><i
                                                                    class="fa fa-edit"></i></button>
                                                        </form>
                                                        <?php if (!$tarea['completado']): ?>
                                                        <form method="post" style="display:inline">
                                                            <input type="hidden" name="accion" value="completar">
                                                            <input type="hidden" name="id" value="<?= $tarea['id'] ?>">
                                                            <button type="submit" class="btn btn-success btn-sm"><i
                                                                    class="ti-arrow-circle-down"></i></button>
                                                        </form>
                                                        <?php endif; ?>
                                                        <form method="post" style="display:inline">
                                                            <input type="hidden" name="accion" value="borrar">
                                                            <input type="hidden" name="id" value="<?= $tarea['id'] ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm"><i
                                                                    class="ti-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
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
        <!-- main content area end -->
    </div>
    <?php include '../includes/templates/footer.php'; ?>
    <?php include '../includes/libraries/scripts.php'; ?>
    </div>
</body>

</html>
<script>
function editarTarea(id, titulo, inicio, fin, info, url) {
    document.querySelector('[name=accion]').value = 'editar';
    document.querySelector('[name=id]').value = id;
    document.querySelector('[name=titulo]').value = titulo;
    document.querySelector('[name=fecha_inicio]').value = inicio;
    document.querySelector('[name=fecha_fin]').value = fin;
    document.querySelector('[name=info]').value = info;
    document.querySelector('[name=url]').value = url;
}
</script>