<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';

include_once '../../config.php';  // Incluir la conexión a la base de datos
include_once '../includes/functions/export-sql.php';  // Incluir las funciones de exportación
include '../includes/templates/head.php';

// Establecer la conexión a la base de datos
$conn = conectar_bd();  // Crear la conexión a la base de datos

// Verificar si el formulario fue enviado
if (isset($_POST['exportar_sql'])) {
    // Llamamos a la función que está en export-sql.php para exportar los datos
    exportar_a_sql($conn);  // Pasamos la conexión como argumento a la función
}
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
            
        <!-- page title area end -->
            <div class="main-content-inner">
                <div class="row">
                    <!-- No gutters start -->
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="header-title">Exportar SQL</div>
                                
                                <!-- Aquí va el botón para exportar los datos -->
                                <form method="POST">
                                    <button type="submit" name="exportar_sql" class="btn btn-primary">EXPORTAR</button>
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
    <?php include '../includes/templates/footer.php'; ?>
    
    
    <?php include '../includes/libraries/scripts.php'; ?>
</body>

</html>
