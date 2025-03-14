<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';

// include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
// include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/export_sql.php';
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
                                <div class="header-title">No gutters</div>
                                
                                AQUI VA EL CODIGO
                            </div>
                        </div>
                    </div>
                    <!-- No gutters end -->
                    
                    
                </div>
            </div>
            <!-- Notificación de copiado -->
            <div id="copyNotification" style="display: none;" class="alert"></div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php';?>
        <!-- main content area end -->

    </div>
    <!-- page container area end -->
    
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php';?>
    
</body>

</html>
