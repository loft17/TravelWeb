<?php
include 'includes/protect.php';
include 'includes/header.php';
include 'includes/functions.php';
?>
<script>
(function () {
    var p = new URLSearchParams(window.location.search);
    if (!p.has('fecha')) {
        var d = new Date();
        var fecha = d.getFullYear() + '-' +
            String(d.getMonth() + 1).padStart(2, '0') + '-' +
            String(d.getDate()).padStart(2, '0');
        p.set('fecha', fecha);
        window.location.replace('?' + p.toString());
    }
})();
</script>

<body>

    <!-- Sección principal con fecha y entradas -->
    <div class="content">
        <!-- Fecha visible antes de la primera entrada -->
        <div class="fecha"><?php echo date('d/m/Y', strtotime($fecha)); ?></div>

        <?php
        $iconos_t = [
            'avion'  => 'flight',
            'bus'    => 'directions_bus',
            'tren'   => 'train',
            'ferry'  => 'directions_boat',
            'taxi'   => 'local_taxi',
            'coche'  => 'directions_car',
            'otro'   => 'route',
        ];
        foreach ($transportes_dia as $t):
        ?>
        <div class="transp-card">
            <div class="transp-tipo">
                <span class="material-icons"><?= $iconos_t[$t['tipo']] ?? 'route' ?></span>
            </div>
            <div class="transp-info">
                <div class="transp-ruta">
                    <?= htmlspecialchars($t['origen']) ?>
                    <span class="material-icons transp-arrow">arrow_forward</span>
                    <?= htmlspecialchars($t['destino']) ?>
                </div>
                <div class="transp-meta">
                    <?php if ($t['hora_salida']): ?>
                    <span><?= substr($t['hora_salida'], 0, 5) ?></span>
                    <?php endif; ?>
                    <?php if ($t['hora_llegada']): ?>
                    <span>→ <?= substr($t['hora_llegada'], 0, 5) ?></span>
                    <?php endif; ?>
                    <?php if ($t['numero']): ?>
                    <span class="transp-num">
                        <?php if ($t['tipo'] === 'avion'): ?>
                        <a href="https://es.flightaware.com/live/flight/<?= urlencode(strtoupper(str_replace(' ', '', $t['numero']))) ?>" target="_blank" rel="noopener" class="transp-flight-link"><?= htmlspecialchars($t['numero']) ?></a>
                        <?php else: ?>
                        <?= htmlspecialchars($t['numero']) ?>
                        <?php endif; ?>
                    </span>
                    <?php endif; ?>
                </div>
                <?php if ($t['notas']): ?>
                <div class="transp-notas"><?= htmlspecialchars($t['notas']) ?></div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

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
            <p>No hay atracciones programadas para hoy.</p>
        <?php endif; ?>

        <?php $conn->close(); ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/scripts.js"></script>
</body>

</html>
