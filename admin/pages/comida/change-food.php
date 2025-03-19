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
                                <div class="header-title">CAMBIAR ESTADO</div>
                                
                                <!-- AQUI VA EL CODIGO: Actualización de registros -->
                                <?php
                                // Incluir la configuración para conectarse a la BBDD
                                include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

                                // Conectar a la base de datos
                                $conn = conectar_bd();

                                // Actualizar: cambiar comido de 1 (SI) a 0 (NO)
                                $sql = "UPDATE comida SET comido = 0 WHERE comido = 1";
                                if ($conn->query($sql) === TRUE) {
                                    $affected = $conn->affected_rows;
                                    echo "<p><strong>Log de cambios:</strong></p>";
                                    echo "<p>Se actualizaron <strong>$affected</strong> registros: se cambió el estado 'comido' de SI a NO.</p>";
                                    
                                    // Mostrar listado de registros actualizados (solo como ejemplo)
                                    $result = $conn->query("SELECT id, nombre FROM comida WHERE comido = 0");
                                    if ($result && $result->num_rows > 0) {
                                        echo "<p>Registros actualizados:</p><ul>";
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<li>ID: " . $row['id'] . " - Nombre: " . htmlspecialchars($row['nombre']) . "</li>";
                                        }
                                        echo "</ul>";
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
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php'; ?>
    </div>
    <!-- page container area end -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php'; ?>
</body>
</html>
