<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/emojis.php';

// Leer el archivo JSON usando la función
$jsonFile = $_SERVER['DOCUMENT_ROOT'] . '/admin/assets/json/emojis.json';
$categorias = obtenerCategoriasEmojis($jsonFile);
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
                                <div class="header-title">EMOJIS</div>
                                <div class="row">

                                    <!-- Bootstrap Grid start -->
                                    <?php if (!empty($categorias)) : ?>
                                    <?php foreach ($categorias as $categoria => $emojis) : ?>

                                    <div class="col-lg-6 mt-5">
                                        <div class="card">
                                            <div class="card-body">
                                                <h4 class="header-title"><?= htmlspecialchars($categoria) ?></h4>
                                                <div class="single-table">
                                                    <?php foreach ($emojis as $emoji) : ?>
                                                    <span class="emoji"
                                                        title="<?= htmlspecialchars($emoji['nombre']) ?>"
                                                        onclick="copiarEmoji('<?= htmlspecialchars($emoji['emoji']) ?>')">
                                                        <?= htmlspecialchars($emoji['emoji']) ?>
                                                    </span>

                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php endforeach; ?>
                                    <?php else : ?>
                                    <p>No hay categorías de emojis disponibles.</p>
                                    <?php endif; ?>
                                    <!-- Bootstrap Grid end -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- No gutters end -->
                </div>
                <!-- Notificación de copiado -->
                <br><div id="copyNotification" style="display: none;" class="alert"></div>
            </div>

        </div>
        <!-- main content area end -->

    </div>
    <!-- page container area end -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php';?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php';?>
    <script defer src="/admin/assets/js/copy-emojis.js"></script>
</body>

</html>