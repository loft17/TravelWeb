<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include '../includes/templates/head.php';
include '../includes/functions/emojis.php';

// Leer el archivo JSON usando la función
$jsonFile = '../assets/json/emojis.json';
$categorias = obtenerCategoriasEmojis($jsonFile);
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

        <div class="main-content-inner">
                <div class="row">
                    <!-- Bootstrap Grid start -->
                    <?php if (!empty($categorias)) : ?>
                        <?php foreach ($categorias as $categoria => $emojis) : ?>
                            <div class="col-12 mt-1">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="header-title"><?= ucfirst($categoria) ?></div>
                                        <!-- Start 12 column grid system -->
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="grid-col">
                                                    <?php foreach ($emojis as $emoji) : ?>
                                                        <span class="emoji" title="<?= htmlspecialchars($emoji['nombre']) ?>" onclick="copiarEmoji('<?= htmlspecialchars($emoji['emoji']) ?>')">
                                                        <?= htmlspecialchars($emoji['emoji']) ?>
                                                        </span>

                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p>No hay categorías de emojis disponibles.</p>
                    <?php endif; ?> 
                    <!-- Bootstrap Grid end -->

                    <!-- Notificación de copiado -->
                    <div id="copyNotification" class="alert alert-items" style="display:none;">¡Emoji copiado!</div>
                </div>
            </div>

    </div>
    <!-- page container area end -->
    <?php include '../includes/templates/footer.php'; ?>
    
    
    <?php include '../includes/libraries/scripts.php'; ?>
    <script src="../assets/js/copy-emojis.js"></script>
</body>

</html>

