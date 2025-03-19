<?php
// Incluir la configuración de la base de datos
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include 'header.php';

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

<body>

    <!-- Sección principal con fecha y entradas -->
    <div class="content">
        <!-- Fecha visible antes de la primera entrada -->
        <div class="fecha"><?php echo htmlspecialchars($fecha); ?></div>

        <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <details class="entrada">
            <summary>
                <span><?php echo htmlspecialchars($row['nombre']); ?></span>
                <input type="checkbox" id="check3" class="seen-checkbox">
                <label for="check3" class="seen-label">
                    <span class="material-icons unchecked">check_circle_outline</span>
                    <span class="material-icons checked">check_circle</span>
                </label>
            </summary>
            <img src="<?php echo htmlspecialchars($row['imagen_url']); ?>" alt="<?php echo htmlspecialchars($row['nombre']); ?>">
            <p><?php echo $row['descripcion']; ?></p>
            <div class="divider"></div>
            <p>
                <?php if (!empty($row['mapa_url'])): ?>
                    <span class="material-icons social-icon">location_on</span>
                <?php endif; ?>
                <?php if (!empty($row['wikipedia_url'])): ?>
                    <span class="material-icons social-icon">public</span>
                <?php endif; ?>
                <?php if (!empty($row['instagram_url_1'])): ?>
                    <span class="material-icons social-icon">camera_alt</span>
                <?php endif; ?>
                <?php if (!empty($row['instagram_url_2'])): ?>
                    <span class="material-icons social-icon">camera_alt</span>
                <?php endif; ?>
                <?php if (!empty($row['instagram_url_3'])): ?>
                    <span class="material-icons social-icon">camera_alt</span>
                <?php endif; ?>
            </p>

        </details>

        <?php endwhile; ?>
        <?php else: ?>
        <p>No hay atracciones disponibles para la fecha seleccionada.</p>
        <?php endif; ?>

        <?php $conn->close(); ?>
    </div>

    <?php include 'footer.php'; ?>
    <script src="assets/scripts.js"></script>
</body>

</html>