<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/planning.php';

?>

<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <title>Calendario - <?php echo TITLE_WEB; ?></title>
    <!-- Incluir CSS de Bootstrap y Quill -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>
<body>
    <!-- page container area start -->
    <div class="page-container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php'; ?>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php'; ?>

        <!-- main content area start -->
        <div class="main-content">
            <div class="main-content-inner">
                <div class="row">
                    <!-- Calendario -->
                    <div class="col-lg-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title">Calendario</h4>
                                <div class="single-table">
                                    <div class="table-responsive">
                                        <?php echo $calendar; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Fin Calendario -->
                </div>
            </div>
            <div id="copyNotification" style="display: none;" class="alert"></div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php'; ?>
    </div>
    <!-- page container area end -->

    <!-- Modal para insertar/editar información con Quill -->
    <div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
       <div class="modal-dialog modal-dialog-centered" role="document">
           <div class="modal-content">
               <div class="modal-header">
                   <h5 class="modal-title" id="eventModalLabel">Editar Evento</h5>
                   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
               </div>
               <!-- Formulario para insertar/editar datos -->
               <form id="eventForm" method="post" action="/admin/includes/functions/add_planning.php">
                   <div class="modal-body">
                       <input type="hidden" name="fecha" id="eventDate">
                       
                       <div class="form-group">
                           <label for="ciudadEditor">Ciudad</label>
                           <div id="ciudadEditor" style="height: 100px;"></div>
                           <input type="hidden" name="ciudad" id="ciudadInput">
                       </div>
                       <div class="form-group">
                           <label for="visitaMananaEditor">Visita Mañana</label>
                           <div id="visitaMananaEditor" style="height: 100px;"></div>
                           <input type="hidden" name="visita_manana" id="visitaMananaInput">
                       </div>
                       <div class="form-group">
                           <label for="visitaTardeEditor">Visita Tarde</label>
                           <div id="visitaTardeEditor" style="height: 100px;"></div>
                           <input type="hidden" name="visita_tarde" id="visitaTardeInput">
                       </div>
                       <div class="form-group">
                           <label for="visitaNocheEditor">Visita Noche</label>
                           <div id="visitaNocheEditor" style="height: 100px;"></div>
                           <input type="hidden" name="visita_noche" id="visitaNocheInput">
                       </div>
                   </div>
                   <div class="modal-footer">
                       <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                       <button type="submit" class="btn btn-primary">Guardar cambios</button>
                   </div>
               </form>
           </div>
       </div>
    </div>

    <!-- Incluir librerías JavaScript: jQuery, Bootstrap JS y Quill -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php'; ?>
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="/admin/assets/js/planning.js"></script>
    
</body>
</html>
