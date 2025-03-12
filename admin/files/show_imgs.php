<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include '../includes/templates/head.php';  // Asegúrate de que head.php tenga los elementos <head> y <meta> adecuados.
include '../includes/functions/get_images.php'; // Incluir el archivo para obtener las imágenes
include '../includes/functions/display_images.php'; // Incluir el archivo que contiene la función displayImages

// Obtener las imágenes
$images = getImagesFromDirectory($_SERVER['DOCUMENT_ROOT'] . '/content/uploads/');
?>

<!doctype html>
<html class="no-js" lang="en">

<body>

    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->

    <!-- page container area start -->
    <div class="page-container">
        <?php include '../includes/templates/sidebar.php'; ?>
        <?php include '../includes/templates/user-profile.php'; ?>

        <!-- main content area start -->
        <div class="main-content">
            <div class="main-content-inner">
                <div class="row">
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="header-title">Imagenes</div>

                                <?php displayImages($images); ?> <!-- Llamada a la función displayImages -->

                            </div>
                        </div>
                    </div>                    
                </div>

            </div>
            <!-- Notificación de copiado -->
            <div id="copyNotification" style="display: none;" class="alert"></div>
        </div>
        <!-- main content area end -->

    </div>
    <!-- page container area end -->

    <?php include '../includes/templates/footer.php'; ?>
    <?php include '../includes/libraries/scripts.php'; ?>
    <script src="../assets/js/copy_images.js"></script>
</body>

</html>
