<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/add_food.php'; 
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
                    <!-- Formulario para agregar un nuevo registro -->
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="header-title">Agregar Comida</div>
                                
                                <?php if ($error): ?>
                                    <div class="alert alert-danger"><?php echo $error; ?></div>
                                <?php endif; ?>
                                
                                <!-- Formulario con enctype para permitir subir archivos -->
                                <form action="add-food.php" method="post" enctype="multipart/form-data" onsubmit="updateDescription()">

                                    <!-- NOMBRE -->
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <label for="nombre">Nombre:</label>
                                            <input type="text" class="form-control" name="nombre" id="nombre" required>
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

                                    <div class="form-group row">
                                        <!-- Campo de texto para URL de imagen (opcional) -->
                                        <div class="col-sm-6">
                                            <label for="imagen_url">URL de la Imagen (opcional):</label>
                                            <input type="text" class="form-control" name="imagen_url" id="imagen_url">
                                        </div>

                                        <!-- Campo para subir imagen y mostrar miniatura -->
                                        <div class="col-sm-6">
                                            <label for="imagen_file">Subir Imagen:</label>
                                            <input type="file" class="form-control" name="imagen_file" id="imagen_file" accept="image/jpeg, image/png, image/webp">
                                        </div>
                                    </div>

                                    <!-- Miniatura de la imagen -->
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <img id="imagen_preview" src="" style="max-width: 200px; margin-top: 10px;">
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Agregar</button>
                                    <a href="show-foods.php" class="btn btn-secondary">Cancelar</a>
                                </form>
                            </div><!-- card-body -->
                        </div><!-- card -->
                    </div>
                    <!-- Fin del formulario -->
                </div>
            </div>
            <!-- Notificación de copiado -->
            <div id="copyNotification" style="display: none;" class="alert"></div>
        </div>
        <!-- main content area end -->
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php'; ?>
    </div>
    <!-- page container area end -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php'; ?>

    <!-- Incluir JS de Quill -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <!-- Incluir el archivo JS externo para agregar comida -->
    <script src="/admin/assets/js/add_food.js"></script>
</body>
</html>
