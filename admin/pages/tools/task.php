<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/task.php';
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Tareas</title>
    <!-- Incluye los estilos de Bootstrap -->
    <link rel="stylesheet" href="/path/to/bootstrap.min.css">
    <!-- Otros estilos adicionales -->
</head>
<body>
<div class="page-container">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php'; ?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php'; ?>

    <div class="main-content">
        <div class="main-content-inner">
            <!-- Botón para abrir el modal de insertar tarea -->
            <button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#insertarTareaModal">
              Insertar Tarea
            </button>

            <div class="row mt-3">
                <!-- Gestión de tareas (tabla) -->
                <div class="col-12">
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
                                    <td>
                                        <?php 
                                        $info = $tarea['info'];
                                        if (strlen($info) > 80) {
                                            $short_info = substr($info, 0, 80) . '...';
                                            echo htmlspecialchars($short_info);
                                            echo ' <button type="button" class="btn btn-link p-0" data-toggle="modal" data-target="#modalInfo" data-full-info="' . htmlspecialchars($info) . '">Leer más</button>';
                                        } else {
                                            echo htmlspecialchars($info);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?= $tarea['url'] ? '<a href="' . htmlspecialchars($tarea['url']) . '" target="_blank">Enlace</a>' : '-' ?>
                                    </td>
                                    <td><?= $tarea['fecha_terminada'] ?: '-' ?></td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" onclick='editarTarea(<?= json_encode($tarea) ?>)'>
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <?php if (!$tarea['completado']): ?>
                                            <form method="post" style="display:inline">
                                                <input type="hidden" name="accion" value="completar">
                                                <input type="hidden" name="id" value="<?= $tarea['id'] ?>">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="ti-arrow-circle-down"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="post" style="display:inline">
                                            <input type="hidden" name="accion" value="borrar">
                                            <input type="hidden" name="id" value="<?= $tarea['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="ti-trash"></i>
                                            </button>
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

            <!-- Modal para insertar tarea -->
            <div class="modal fade" id="insertarTareaModal" tabindex="-1" role="dialog" aria-labelledby="insertarTareaModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="insertarTareaModalLabel">Insertar Tarea</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form method="post" id="formTarea">
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
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar Edición</button>
                    <button type="submit" form="formTarea" class="btn btn-primary">Guardar Tarea</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Fin del modal para insertar tarea -->

            <!-- Modal para mostrar info completa -->
            <div class="modal fade" id="modalInfo" tabindex="-1" role="dialog" aria-labelledby="modalInfoLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalInfoLabel">Información Completa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <!-- El contenido se carga dinámicamente mediante JavaScript -->
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Fin del modal para info completa -->

        </div>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php'; ?>
</div>

<!-- Incluye los scripts necesarios -->
<?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php'; ?>
<!-- Asegúrate de que se carguen jQuery y Bootstrap JS -->
<script src="/path/to/jquery.min.js"></script>
<script src="/path/to/bootstrap.bundle.min.js"></script>
<script defer src="/admin/assets/js/task.js"></script>

<!-- Script para cargar la info completa en el modal -->
<script>
    $('#modalInfo').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var fullInfo = button.data('full-info');
      var modal = $(this);
      modal.find('.modal-body').text(fullInfo);
    });
</script>

</body>
</html>
