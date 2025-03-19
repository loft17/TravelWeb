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
                                <div class="header-title">CAMBIAR ESTADO: Atracciones - No visto</div>
                                
                                <?php
                                // Incluir la configuración para conectarse a la BBDD
                                include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

                                // Conectar a la base de datos
                                $conn = conectar_bd();

                                // Primero obtenemos los registros que se van a actualizar (visto = 1)
                                $recordsToUpdate = [];
                                $result = $conn->query("SELECT id, nombre FROM atracciones WHERE visto = 1");
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $recordsToUpdate[] = $row;
                                    }
                                    $result->free();
                                }

                                // Actualizar: cambiar visto de 1 (Visto) a 0 (No visto)
                                $sql = "UPDATE atracciones SET visto = 0 WHERE visto = 1";
                                if ($conn->query($sql) === TRUE) {
                                    $affected = $conn->affected_rows;
                                    echo "<p><strong>Log de cambios:</strong></p>";
                                    echo "<p>Se actualizaron <strong>$affected</strong> registros: se cambió el estado 'visto' de SI a NO.</p>";

                                    if (!empty($recordsToUpdate)) {
                                        echo "<p>Registros actualizados:</p><ul>";
                                        foreach ($recordsToUpdate as $row) {
                                            echo "<li>ID: " . $row['id'] . " - Nombre: " . htmlspecialchars($row['nombre']) . "</li>";
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
            <!-- Notificación de copiado -->
            <div id="copyNotification" style="display: none;" class="alert"></div>
        </div>
        <!-- main content area end -->
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php'; ?>
    </div>
    <!-- page container area end -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php'; ?>
</body>
</html>
