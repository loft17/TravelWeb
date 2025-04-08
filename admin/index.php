<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include 'includes/templates/head.php';  // Asegúrate de que head.php tenga los elementos <head> y <meta> adecuados.
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
        <?php include 'includes/templates/sidebar.php'; ?>
        <?php include 'includes/templates/user-profile.php'; ?>


        <!-- main content area start -->
        <div class="main-content">
            
            EN BLANCO
            
        </div>
        <!-- main content area end -->

    </div>
    <!-- page container area end -->
    <?php include 'includes/templates/footer.php'; ?>
    
    
    <?php include 'includes/libraries/scripts.php'; ?>
</body>

</html>
