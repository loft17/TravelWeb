<?php
include 'includes/header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Conectar a la base de datos
$conn = conectar_bd();

// Comprobar que se envía el parámetro id y que es numérico
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];
} else {
    die("ID inválido.");
}

// Preparar la consulta para obtener la entrada de la tabla 'atracciones'
$stmt = $conn->prepare("SELECT * FROM atracciones WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

?>
<body>

    <!-- Sección principal con fecha y entradas -->
    <div class="content">
        <?php
        // Comprobar si se encontró alguna entrada
        if ($result->num_rows > 0) {
            $entrada = $result->fetch_assoc();
            // Mostrar los datos de la entrada
            echo "<h1>" . htmlspecialchars($entrada['nombre']) . "</h1>";
            echo "<p><strong>Ciudad:</strong> " . htmlspecialchars($entrada['ciudad']) . "</p>";
            echo "<p><strong>Fecha:</strong> " . htmlspecialchars($entrada['fecha']) . "</p>";
            echo "<div>" . $entrada['descripcion'] . "</div>";

            // Si se dispone de una imagen, se puede mostrar:
            if (!empty($entrada['imagen_url'])) {
                echo "<img src='" . htmlspecialchars($entrada['imagen_url']) . "' alt='" . htmlspecialchars($entrada['nombre']) . "' />";
            }
        } else {
            echo "No se encontró la entrada solicitada.";
        }
        // Cerrar el statement
        $stmt->close();
        ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
