<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
?>

<!doctype html>
<html class="no-js" lang="en">
<body>
    <!-- page container area start -->
    <div class="page-container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php'; ?>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php'; ?>

        <!-- main content area start -->
        <div class="main-content">
            <!-- page title area end -->
            <div class="main-content-inner">
                <div class="row">
                    <!-- No gutters start -->
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="header-title">CAMBIAR ESTADO: Atracciones - activado</div>
                                
                                <?php
                                // Incluir la configuración para conectarse a la BBDD
                                include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
                                $conn = conectar_bd();
                                
                                // Primero se obtienen los registros que se van a actualizar (activo = 0)
                                $recordsToUpdate = [];
                                $result = $conn->query("SELECT id, nombre FROM atracciones WHERE activo = 0");
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $recordsToUpdate[] = $row;
                                    }
                                    $result->free();
                                }
                                
                                // Actualizar: cambiar activo de 0 (Inactivo) a 1 (Activo)
                                $sql = "UPDATE atracciones SET activo = 1 WHERE activo = 0";
                                if ($conn->query($sql) === TRUE) {
                                    $affected = $conn->affected_rows;
                                    echo "<p><strong>Log de cambios:</strong></p>";
                                    echo "<p>Updated <strong>$affected</strong> records: 'activo' status set to Active.</p>";
                                    
                                    // Mostrar listado de registros actualizados (solo los que se iban a actualizar)
                                    if (!empty($recordsToUpdate)) {
                                        echo "<p>Registros actualizados:</p><ul>";
                                        foreach ($recordsToUpdate as $row) {
                                            echo "<li>ID: " . $row['id'] . " - Name: " . htmlspecialchars($row['nombre']) . "</li>";
                                        }
                                        echo "</ul>";
                                    } else {
                                        echo "<p>No se encontraron registros para actualizar.</p>";
                                    }
                                } else {
                                    echo "<p>Error al actualizar registros: " . $conn->error . "</p>";
                                }
                                $conn->close();
                                ?>
                            </div>
                        </div>
                    </div>
                    <!-- No gutters end -->
                </div>
            </div>
            <!-- Notification area -->
            <div id="copyNotification" style="display: none;" class="alert"></div>
        </div>
        <!-- main content area end -->
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php'; ?>
    </div>
    <!-- page container area end -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php'; ?>
</body>
</html>
