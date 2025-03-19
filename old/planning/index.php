<?php
// Incluir la configuración de la base de datos
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Inicializar la conexión a la base de datos
$conn = conectar_bd();

// Obtener la fecha desde la URL (formato YYYY-MM-DD)
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;

// Consulta para obtener las atracciones activas con la fecha específica
if ($fecha) {
    $sql = "SELECT * FROM atracciones WHERE activo = TRUE AND fecha = ? ORDER BY ciudad, orden ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $fecha); // El parámetro 's' es para cadenas
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $sql = "SELECT * FROM atracciones WHERE activo = TRUE ORDER BY ciudad, orden ASC";
    $result = $conn->query($sql);
}

// Función para actualizar el estado "visto" en la base de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_visto') {
    $id = intval($_POST['id']);
    $currentVisto = $_POST['visto'] === 'true'; // Obtener el estado actual

    // Cambiar el estado contrario
    $newVisto = !$currentVisto;

    // Actualizar la base de datos
    $updateSql = "UPDATE atracciones SET visto = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ii", $newVisto, $id);
    $stmt->execute();
    $stmt->close();

    // Devolver una respuesta JSON
    echo json_encode(['success' => true, 'visto' => $newVisto]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tailandia 2025</title>
    <link rel="stylesheet" href="../content/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <!-- Sección para mostrar la fecha -->
        <section class="fecha-atracciones">
            <?php if ($fecha): ?>
                <h3>Planning: <strong><?php echo htmlspecialchars($fecha); ?></strong></h3>
            <?php else: ?>
                <h3>Lista completa de atracciones</h3>
            <?php endif; ?>
        </section>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="atraccion" id="atraccion-<?php echo $row['id']; ?>">
                    <div class="titulo-atraccion">
                        <h2><?php echo htmlspecialchars($row['nombre']); ?></h2>
                        <span 
                            class="check-icon material-icons <?php echo $row['visto'] ? 'checked' : ''; ?>" 
                            data-id="<?php echo $row['id']; ?>"
                            onclick="toggleVisto(<?php echo $row['id']; ?>, <?php echo $row['visto'] ? 'true' : 'false'; ?>)">
                            check_circle
                        </span>
                    </div>
                    <div class="info" id="info-<?php echo $row['id']; ?>">
                        <img src="<?php echo htmlspecialchars($row['imagen_url']); ?>" alt="<?php echo htmlspecialchars($row['nombre']); ?>">
                        <div class="descripcion">
                            <?php echo $row['descripcion']; ?>
                        </div>
                        <div class="enlaces">
                            <?php if (!empty($row['mapa_url'])): ?>
                                <a href="<?php echo htmlspecialchars($row['mapa_url']); ?>" class="icon-link" target="_blank">
                                    <span class="material-icons">location_on</span>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($row['wikipedia_url'])): ?>
                                <a href="<?php echo htmlspecialchars($row['wikipedia_url']); ?>" class="icon-link" target="_blank">
                                    <span class="material-icons">public</span>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($row['instagram_url_1'])): ?>
                                <a href="<?php echo htmlspecialchars($row['instagram_url_1']); ?>" class="icon-link" target="_blank">
                                    <span class="material-icons">camera_alt</span>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($row['instagram_url_2'])): ?>
                                <a href="<?php echo htmlspecialchars($row['instagram_url_2']); ?>" class="icon-link" target="_blank">
                                    <span class="material-icons">camera_alt</span>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($row['instagram_url_3'])): ?>
                                <a href="<?php echo htmlspecialchars($row['instagram_url_3']); ?>" class="icon-link" target="_blank">
                                    <span class="material-icons">camera_alt</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No hay atracciones disponibles para la fecha seleccionada.</p>
        <?php endif; ?>

        <?php $conn->close(); ?>
    </main>

    <?php include 'footer.php'; ?>
    <script src="../content/assets/js/script.js"></script>
</body>
</html>
