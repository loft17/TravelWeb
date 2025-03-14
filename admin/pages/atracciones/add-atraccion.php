<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/add_atraccion.php'; 
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
    <!-- Incluir CSS de Quill -->
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
                    <!-- Formulario para agregar un nuevo registro a atracciones -->
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="header-title">Agregar Atracción</div>
                                
                                <?php if ($error): ?>
                                    <div class="alert alert-danger"><?php echo $error; ?></div>
                                <?php endif; ?>
                                
                                <form action="add-atraccion.php" method="post" enctype="multipart/form-data" onsubmit="updateDescription()">
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" placeholder="Nombre" name="nombre" id="nombre" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" placeholder="Ciudad" name="ciudad" id="ciudad">
                                        </div>

                                        <div class="col-sm-3">
                                            <input type="date" class="form-control" name="fecha" id="fecha" required>
                                        </div>

                                        <div class="col-sm-3">
                                            <input type="text" class="form-control" placeholder="Orden" name="orden" id="orden">
                                        </div>
                                    </div>

                                    <!-- Editor Quill para la descripción -->
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                        <label for="descripcion">Descripción:</label>
                                            <div id="editor-container" style="height: 200px;"></div>
                                            <input type="hidden" name="descripcion" id="descripcion">                                       
                                        </div>
                                    </div>

                                    <!-- Imagenes -->
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label for="imagen_url">URL de la Imagen (opcional):</label>
                                            <input type="text" class="form-control" name="imagen_url" id="imagen_url">
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="imagen_file">Subir Imagen:</label>
                                            <input type="file" class="form-control" name="imagen_file" id="imagen_file" accept="image/jpeg, image/png, image/webp">
                                        </div>
                                    </div>

                                    <!-- URLS -->
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" name="mapa_url" id="mapa_url" placeholder="URL del Mapa (opcional):">
                                        </div>

                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" name="wikipedia_url" id="wikipedia_url" placeholder="URL de Info (opcional):">
                                        </div>
                                    </div>

                                    <!-- INSTAGRAM -->
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control" name="instagram_url_1" id="instagram_url_1" placeholder="Instagram URL 1">
                                        </div>

                                        <div class="col-sm-3">
                                            <input type="text" class="form-control" name="instagram_url_2" id="instagram_url_2" placeholder="Instagram URL 2">
                                        </div>

                                        <div class="col-sm-3">
                                            <input type="text" class="form-control" name="instagram_url_3" id="instagram_url_3" placeholder="Instagram URL 3">
                                        </div>
                                    
                                        <div class="form-group form-check">
                                            <input type="checkbox" class="form-check-input" name="visto" id="visto">
                                            <label class="form-check-label" for="visto">Visto</label>

                                            <br>

                                            <input type="checkbox" class="form-check-input" name="activo" id="activo" checked>
                                            <label class="form-check-label" for="activo">Activo</label>
                                        </div>
                                    </div>

                                    <!-- Miniatura -->
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <img id="imagen_preview" src="" style="max-width: 200px; margin-top: 10px;">
                                        </div>
                                    </div>
                                    
                                </form>
                            </div><!-- card-body -->
                        </div><!-- card -->
                    </div>
                    <!-- Fin del formulario -->
                </div>
            </div>
            <div id="copyNotification" style="display: none;" class="alert"></div>
        </div>
        <!-- main content area end -->
    </div>
    <!-- page container area end -->
    
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php'; ?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php'; ?>
    
    <!-- Incluir JS de Quill -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <!-- Incluir el archivo JS externo para agregar atracción -->
    <script src="/admin/assets/js/add_atraccion.js"></script>
</body>
</html>
