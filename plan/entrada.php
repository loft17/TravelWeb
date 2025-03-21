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
    <br><br><br>
    <div class="content">
        <?php
        if ($result->num_rows > 0) {
            $entrada = $result->fetch_assoc();
            echo '<div class="entrada-text">';
                // Mostrar la imagen principal si existe
                if (!empty($entrada['imagen_url'])) {
                    echo "<img src='" . htmlspecialchars($entrada['imagen_url']) . "' alt='" . htmlspecialchars($entrada['nombre']) . "' />";
                }
                echo "<h1>" . htmlspecialchars($entrada['nombre']) . "</h1>";
                echo "<p><strong>Ciudad:</strong> " . htmlspecialchars($entrada['ciudad']) . " | <strong>Fecha:</strong> " . htmlspecialchars($entrada['fecha']) ."</p>";

                // Mostrar los iconos de cada campo (solo si tienen datos)
                echo '<div class="iconos">';
                    if (!empty($entrada['mapa_url'])) {
                        echo '<a href="' . htmlspecialchars($entrada['mapa_url']) . '" target="_blank"><i class="material-icons social-icon">location_on</i></a>';
                    }
                    if (!empty($entrada['wikipedia_url'])) {
                        echo '<a href="' . htmlspecialchars($entrada['wikipedia_url']) . '" target="_blank"><i class="material-icons social-icon">public</i></a>';
                    }
                    if (!empty($entrada['instagram_url_1'])) {
                        echo '<a href="' . htmlspecialchars($entrada['instagram_url_1']) . '" target="_blank"><i class="material-icons social-icon">camera_alt</i></a>';
                    }
                    if (!empty($entrada['instagram_url_2'])) {
                        echo '<a href="' . htmlspecialchars($entrada['instagram_url_2']) . '" target="_blank"><i class="material-icons social-icon">camera_alt</i></a>';
                    }
                    if (!empty($entrada['instagram_url_3'])) {
                        echo '<a href="' . htmlspecialchars($entrada['instagram_url_3']) . '" target="_blank"><i class="material-icons social-icon">camera_alt</i></a>';
                    }
                echo '</div>';

                echo "<br>";

                // Mostrar el checkbox para el estado "visto"
                echo '<div class="visto-status">';
                    echo '<label for="check_' . htmlspecialchars($entrada['id']) . '">Visto: </label>';
                    echo '<input type="checkbox" class="seen-checkbox" id="check_' . htmlspecialchars($entrada['id']) . '" data-id="' . htmlspecialchars($entrada['id']) . '" ' . ($entrada['visto'] ? 'checked' : '') . '>';
                    // Label para mostrar el ícono asociado al checkbox
                    echo '<label for="check_' . htmlspecialchars($entrada['id']) . '"><i class="material-icons check-icon">';
                        echo $entrada['visto'] ? 'check_circle' : 'check_circle_outline';
                    echo '</i></label>';
                echo '</div>';

                echo "<br>";

                // Mostrar la descripción sin escapar para que se renderice el HTML (por ejemplo, de Quill)
                echo "<div>" . $entrada['descripcion'] . "</div>";                
            echo '</div>';
        } else {
            echo "No se encontró la entrada solicitada.";
        }
        $stmt->close();
        ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Script para actualizar el estado "visto" vía AJAX utilizando tu código -->
    <script src="assets/scripts.js"></script>
</body>
</html>
