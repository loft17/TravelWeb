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
                        <!-- Usamos el id único de la atracción para el checkbox -->
                        <input type="checkbox" id="check_<?php echo $row['id']; ?>" class="seen-checkbox">
                        <label for="check_<?php echo $row['id']; ?>" class="seen-label">
                            <span class="material-icons unchecked">check_circle_outline</span>
                            <span class="material-icons checked">check_circle</span>
                        </label>
                    </summary>
                    <img src="<?php echo htmlspecialchars($row['imagen_url']); ?>" alt="<?php echo htmlspecialchars($row['nombre']); ?>">
                    <?php 
                        // Extraer el texto plano de la descripción (se elimina el HTML)
                        $plainText = strip_tags($row['descripcion']);
                        // Truncar a 160 caracteres usando mb_substr
                        $shortText = mb_substr($plainText, 0, 160, 'UTF-8');
                    ?>
                    <p>
                        <?php echo htmlspecialchars($shortText); ?>
                        <?php if (mb_strlen($plainText, 'UTF-8') > 160): ?>
                            ... <a href="entrada.php?id=<?php echo $row['id']; ?>">leer más</a>
                        <?php endif; ?>
                    </p>
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
