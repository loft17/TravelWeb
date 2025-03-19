<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/task.php';
?>

<!doctype html>
<html lang="es">

<body>
<div class="page-container">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php';?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php';?>

    <div class="main-content">
        <!-- page title area end -->
        <div class="main-content-inner">
                <div class="row">
                    <!-- No gutters start -->
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="header-title">Insertar Tarea</div>

                                <form method="post" class="mb-4" id="formTarea">
                                <input type="hidden" name="accion" value="crear">
                                <input type="hidden" name="id">
                                <div class="form-group">
                                    <input type="text" name="titulo" class="form-control" placeholder="Título de la tarea" required>
                                </div>
                                <div class="form-row">
                                    <div class="col">
                                        <input type="date" name="fecha_inicio" class="form-control" value="<?= $hoy ?>">
                                    </div>
                                    <div class="col">
                                        <input type="date" name="fecha_fin" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group mt-2">
                                    <input type="text" name="info" class="form-control" placeholder="Información adicional">
                                </div>
                                <div class="form-group">
                                    <input type="url" name="url" class="form-control" placeholder="URL relacionada">
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar Tarea</button>
                                <button type="button" class="btn btn-secondary" onclick="limpiarForm()">Cancelar Edición</button>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>  
                <div class="col-12 mt-5">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Gestión de Tareas</h4>
                            <table class="table table-hover progress-table text-center">
                                <thead class="text-uppercase bg-dark text-white">
                                <tr>
                                    <th>Título</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
                                    <th>Info</th>
                                    <th>URL</th>
                                    <th>Terminada</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php while ($tarea = $tareas->fetch_assoc()): ?>
                                <tr class="<?= $tarea['completado'] ? 'table-success' : '' ?>">
                                    <td><?= htmlspecialchars($tarea['titulo']) ?></td>
                                    <td><?= htmlspecialchars($tarea['fecha_inicio']) ?></td>
                                    <td><?= htmlspecialchars($tarea['fecha_fin']) ?></td>
                                    <td><?= htmlspecialchars($tarea['info']) ?></td>
                                    <td><?= $tarea['url'] ? '<a href="'.htmlspecialchars($tarea['url']).'" target="_blank">Enlace</a>' : '-' ?></td>
                                    <td><?= $tarea['fecha_terminada'] ?: '-' ?></td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" onclick='editarTarea(<?= json_encode($tarea) ?>)'><i class="fa fa-edit"></i></button>
                                        <?php if (!$tarea['completado']): ?>
                                            <form method="post" style="display:inline">
                                                <input type="hidden" name="accion" value="completar">
                                                <input type="hidden" name="id" value="<?= $tarea['id'] ?>">
                                                <button type="submit" class="btn btn-success btn-sm"><i class="ti-arrow-circle-down"></i></button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="post" style="display:inline">
                                            <input type="hidden" name="accion" value="borrar">
                                            <input type="hidden" name="id" value="<?= $tarea['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="ti-trash"></i></button>
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
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php';?>
</div>


<?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php';?>
<script defer src="/admin/assets/js/task.js"></script>

</body>

</html>