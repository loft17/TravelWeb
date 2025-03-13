<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';

// Directorio de imágenes
$directory = $_SERVER['DOCUMENT_ROOT'] . '/content/uploads/';
$extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Buscar imágenes en la carpeta y subcarpetas
$images = [];
foreach ($extensions as $ext) {
    $images = array_merge($images, glob($directory . '*/*.' . $ext, GLOB_BRACE));
}

// Convertir rutas absolutas en rutas relativas para visualización en la web
$relative_images = [];
foreach ($images as $img) {
    $relative_images[] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $img);
}
?>

<!doctype html>
<html class="no-js" lang="es">

<body>

    <div class="page-container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php';?>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php';?>

        <div class="main-content">
            <div class="main-content-inner">
                <div class="row">
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="header-title">Imágenes</div>

                                <!-- Notificación dinámica -->
                                <div id="notification" class="notification"></div>

                                <div class="row">
                                    <?php if (!empty($relative_images)): ?>
                                        <?php foreach ($relative_images as $image): ?>
                                            <div class="col-md-3 mb-3">
                                                <div class="image-container">
                                                    <img src="<?= htmlspecialchars($image) ?>" 
                                                         alt="Imagen" 
                                                         class="image-copy" 
                                                         data-url="<?= htmlspecialchars($image) ?>">
                                                    <div class="delete-btn" 
                                                         onclick="deleteImage('<?= htmlspecialchars($image) ?>')">
                                                        <i class="fa fa-trash"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p>No se encontraron imágenes en la carpeta.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>

    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php';?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php';?>

    <script src="/admin/assets/js/show_images.js"></script>


</body>
</html>
