<?php
// Incluir la configuración de la base de datos
include 'includes/header.php';
include 'includes/functions.php';
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
                        <!-- Checkbox con id único y estado según la BD -->
                    <input type="checkbox" id="check_<?php echo $row['id']; ?>" class="seen-checkbox" <?php echo $row['visto'] ? 'checked' : ''; ?>>
                    <label for="check_<?php echo $row['id']; ?>" class="seen-label">
                        <!-- Único ícono que usará los estilos definidos -->
                        <span class="check-icon material-icons <?php echo $row['visto'] ? 'checked' : ''; ?>">
                            <?php echo $row['visto'] ? 'check_circle' : 'check_circle_outline'; ?>
                        </span>
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

    <?php include 'includes/footer.php'; ?>
    <script src="assets/scripts.js"></script>
</body>

</html>
