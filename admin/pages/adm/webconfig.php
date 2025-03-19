<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';

// Incluimos el fichero de funciones que contiene toda la lógica de configuración.
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/config_web.php';

// Ejecutamos la función que procesa la actualización (si es que se envía el formulario)
// y obtiene los valores actuales.
$configData = process_config_web();
$fields = $configData['fields'];
$currentValues = $configData['currentValues'];
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="UTF-8">
    <title>Editar Configuraciones</title>
    <!-- Incluye tus hojas de estilo necesarias -->
</head>
<body>
    <!-- Contenedor principal -->
    <div class="page-container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php'; ?>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php'; ?>

        <!-- Área principal de contenido -->
        <div class="main-content">
            <div class="main-content-inner">
                <div class="row">
                    <!-- Input Grid start -->
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title">Modificar Configuraciones</h4>
                                <form class="form-horizontal" method="post">
                                    <?php foreach ($fields as $key => $label): ?>
                                        <div class="form-group">
                                            <label for="<?php echo $key; ?>" class="col-form-label"><?php echo $label; ?>:</label>
                                            <input class="form-control" type="text" id="<?php echo $key; ?>" name="<?php echo $key; ?>" value="<?php echo isset($currentValues[$key]) ? htmlspecialchars($currentValues[$key]) : ''; ?>" required>
                                        </div>
                                    <?php endforeach; ?>
                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Fin del contenedor -->
                </div>
            </div>
            <!-- Notificación de copiado -->
            <div id="copyNotification" style="display: none;" class="alert"></div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php'; ?>
    </div>
    <!-- Fin del contenedor principal -->

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php'; ?>
</body>
</html>
