<?php
// Asegúrate de usar include_once para evitar duplicados
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/export_json.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';


if (isset($_POST['exportar_json'])) {
    // Llamamos a la función que está en export-json.php para exportar los datos
    exportar_a_json($conn);  // Llamamos a la función exportar_a_json
}
?>

<!doctype html>
<html class="no-js" lang="en">

<body>
    <!-- page container area start -->
    <div class="page-container">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php';?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php';?>


        <!-- main content area start -->
        <div class="main-content">
            
        <!-- page title area end -->
            <div class="main-content-inner">
                <div class="row">
                    <!-- No gutters start -->
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="header-title">Exportar JSON</div>

                                <!-- Aquí va el botón para exportar los datos -->
                                <form method="POST">
                                    <button type="submit" name="exportar_json" class="btn btn-primary">EXPORTAR</button>
                                </form>

                            </div>
                        </div>
                    </div>
                    <!-- No gutters end -->
                    
                    
                </div>
            </div>
            
        </div>
        <!-- main content area end -->

    </div>
    <!-- page container area end -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php';?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php';?>
</body>

</html>
