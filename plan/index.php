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
            $escalas   = !empty($t['escalas']) ? json_decode($t['escalas'], true) : [];
            $n_escalas = count($escalas);
            $es_vuelo  = ($t['tipo'] === 'avion');

            if ($n_escalas > 0) {
                $ultimo          = $escalas[$n_escalas - 1];
                $destino_final   = !empty($ultimo['destino_sig'])       ? $ultimo['destino_sig']       : $t['destino'];
                $hora_llegada_f  = !empty($ultimo['hora_llegada_sig'])  ? $ultimo['hora_llegada_sig']  : $t['hora_llegada'];
                $fecha_llegada_f = !empty($ultimo['fecha_llegada_sig']) ? $ultimo['fecha_llegada_sig'] : ($t['fecha_llegada'] ?: $t['fecha']);
            } else {
                $destino_final   = $t['destino'];
                $hora_llegada_f  = $t['hora_llegada'];
                $fecha_llegada_f = $t['fecha_llegada'] ?: $t['fecha'];
            }

            $dia_dist   = $fecha_llegada_f !== $t['fecha'];
            $dias_extra = $dia_dist ? (int)((strtotime($fecha_llegada_f) - strtotime($t['fecha'])) / 86400) : 0;
        ?>
        <details class="transp-card transp-expandable">
            <summary>
                <div class="transp-info">
                    <div class="transp-ruta">
                        <span class="material-icons transp-tipo-icon"><?= $iconos_t[$t['tipo']] ?? 'route' ?></span>
                        <span><?= htmlspecialchars($t['origen']) ?></span>
                        <span class="material-icons transp-arrow">arrow_forward</span>
                        <span><?= htmlspecialchars($destino_final) ?></span>
                        <?php if ($n_escalas > 0): ?>
                        <span class="transp-escala-badge"><?= $n_escalas ?> esc.</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($t['hora_salida'] || $hora_llegada_f): ?>
                    <div class="transp-horario">
                        <span class="transp-h-orig"><?= $t['hora_salida'] ? substr($t['hora_salida'], 0, 5) : '–' ?></span>
                        <span class="transp-h-sep">→</span>
                        <span class="transp-h-dest">
                            <?= $hora_llegada_f ? substr($hora_llegada_f, 0, 5) : '–' ?>
                            <?php if ($dia_dist): ?>
                            <span class="transp-day-badge">+<?= $dias_extra ?></span>
                            <span class="transp-h-fecha"><?= date('d M', strtotime($fecha_llegada_f)) ?></span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
                <span class="material-icons transp-chevron">expand_more</span>
            </summary>

            <div class="transp-detail">
                <div class="transp-tl">
                <?php
                    // destino del primer tramo y sus datos de llegada
                    $d1_code  = $n_escalas > 0 ? $t['destino'] : $destino_final;
                    $d1_hora  = $t['hora_llegada'];
                    $d1_fecha = $t['fecha_llegada'] ?: $t['fecha'];
                    $d1_dot   = $n_escalas > 0 ? 'transp-tl-dot-stop' : 'transp-tl-dot-dest';
                    $d1_plus  = $n_escalas === 0 && $dia_dist;
                ?>

                    <?php
                    // Buscar carrier: primero en JSON por código (campo compania), luego en BD por id
                    $al_main = null;
                    if (!empty($t['compania'])) {
                        $al_main = $carriers_map[strtoupper($t['compania'])] ?? null;
                    }
                    if (!$al_main && !empty($t['aerolinea_id'])) {
                        $al_main = $aerolineas_map[$t['aerolinea_id']] ?? null;
                    }
                    ?>

                    <!-- TRAMO 1: origen → primer destino -->
                    <div class="transp-tl-group">
                        <div class="transp-tl-row transp-tl-dot-orig">
                            <?php if ($es_vuelo): ?><span class="material-icons transp-tl-flight-icon">flight_takeoff</span><?php endif; ?>
                            <?php if (!empty($t['ciudad_origen'])): ?>
                            <span class="transp-tl-city"><?= htmlspecialchars(strtoupper($t['ciudad_origen'])) ?></span>
                            <span class="transp-tl-sep">·</span>
                            <?php endif; ?>
                            <span class="transp-tl-date"><?= date('d M', strtotime($t['fecha'])) ?></span>
                            <?php if ($t['hora_salida']): ?><span class="transp-tl-time"><?= substr($t['hora_salida'],0,5) ?></span><?php endif; ?>
                        </div>
                        <?php if (!empty($t['aeropuerto_origen'])): ?>
                        <div class="transp-tl-sub"><?= $es_vuelo ? '(' . htmlspecialchars($t['origen']) . ') ' : '' ?><?= htmlspecialchars($t['aeropuerto_origen']) ?></div>
                        <?php elseif (!empty($t['ciudad_origen'])): ?>
                        <div class="transp-tl-sub"><?= htmlspecialchars($t['ciudad_origen']) ?></div>
                        <?php endif; ?>
                        <?php if ($t['numero'] || $t['duracion'] || $al_main): ?>
                        <div class="transp-tl-leg">
                            <?php if ($al_main && !empty($al_main['icono'])): ?>
                            <img src="<?= htmlspecialchars($al_main['icono']) ?>" alt="<?= htmlspecialchars($al_main['nombre']) ?>" class="transp-tl-al-logo">
                            <?php endif; ?>
                            <?php if ($t['numero']): ?>
                            <span class="material-icons" style="font-size:12px">flight_takeoff</span>
                            <?php if ($es_vuelo): ?>
                            <a href="https://es.flightaware.com/live/flight/<?= urlencode(strtoupper(str_replace(' ','',$t['numero']))) ?>" target="_blank" rel="noopener" class="transp-flight-link"><?= htmlspecialchars($t['numero']) ?></a>
                            <?php else: ?><?= htmlspecialchars($t['numero']) ?><?php endif; ?>
                            <?php endif; ?>
                            <?php if ($al_main): ?><span class="transp-tl-al-name"><?= htmlspecialchars($al_main['nombre']) ?></span><?php endif; ?>
                            <?php if ($t['duracion']): ?><span class="transp-tl-dur"><?= htmlspecialchars($t['duracion']) ?></span><?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <div class="transp-tl-row <?= $d1_dot ?>">
                            <?php if ($es_vuelo): ?><span class="material-icons transp-tl-flight-icon">flight_land</span><?php endif; ?>
                            <?php if ($n_escalas === 0 && !empty($t['ciudad_destino'])): ?>
                            <span class="transp-tl-city"><?= htmlspecialchars(strtoupper($t['ciudad_destino'])) ?></span>
                            <span class="transp-tl-sep">·</span>
                            <?php elseif ($n_escalas > 0 && !empty($escalas[0]['ciudad'])): ?>
                            <span class="transp-tl-city"><?= htmlspecialchars(strtoupper($escalas[0]['ciudad'])) ?></span>
                            <span class="transp-tl-sep">·</span>
                            <?php endif; ?>
                            <span class="transp-tl-date"><?= date('d M', strtotime($d1_fecha)) ?></span>
                            <?php if ($d1_hora): ?><span class="transp-tl-time"><?= substr($d1_hora,0,5) ?></span><?php endif; ?>
                            <?php if ($d1_plus): ?><span class="transp-day-badge">+<?= $dias_extra ?></span><?php endif; ?>
                        </div>
                        <?php if ($n_escalas === 0 && !empty($t['aeropuerto_destino'])): ?>
                        <div class="transp-tl-sub"><?= $es_vuelo ? '(' . htmlspecialchars($t['destino']) . ') ' : '' ?><?= htmlspecialchars($t['aeropuerto_destino']) ?></div>
                        <?php elseif ($n_escalas === 0 && !empty($t['ciudad_destino'])): ?>
                        <div class="transp-tl-sub"><?= htmlspecialchars($t['ciudad_destino']) ?></div>
                        <?php elseif ($n_escalas > 0 && !empty($escalas[0]['aeropuerto_nombre'])): ?>
                        <div class="transp-tl-sub"><?= $es_vuelo ? '(' . htmlspecialchars($t['destino']) . ') ' : '' ?><?= htmlspecialchars($escalas[0]['aeropuerto_nombre']) ?></div>
                        <?php elseif ($n_escalas > 0 && !empty($escalas[0]['ciudad'])): ?>
                        <div class="transp-tl-sub"><?= htmlspecialchars($escalas[0]['ciudad']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- ESCALAS -->
                    <?php foreach ($escalas as $ei => $e):
                        $es_ultimo = ($ei === $n_escalas - 1); ?>

                    <!-- Badge de escala centrado -->
                    <div class="transp-tl-escala">
                        <span class="transp-tl-layover">
                            <span class="material-icons" style="font-size:12px">schedule</span>
                            <?= !empty($e['duracion_escala']) ? htmlspecialchars($e['duracion_escala']) : 'Escala' ?>
                        </span>
                    </div>

                    <!-- TRAMO desde esta escala -->
                    <?php
                    $al_e = null;
                    if (!empty($e['compania'])) {
                        $al_e = $carriers_map[strtoupper($e['compania'])] ?? null;
                    }
                    if (!$al_e && !empty($e['aerolinea_id'])) {
                        $al_e = $aerolineas_map[$e['aerolinea_id']] ?? null;
                    }
                    ?>
                    <div class="transp-tl-group">
                        <div class="transp-tl-row transp-tl-dot-depart">
                            <span class="material-icons transp-tl-flight-icon">flight_takeoff</span>
                            <?php if (!empty($e['ciudad'])): ?>
                            <span class="transp-tl-city"><?= htmlspecialchars(strtoupper($e['ciudad'])) ?></span>
                            <span class="transp-tl-sep">·</span>
                            <?php endif; ?>
                            <?php if (!empty($e['fecha_salida'])): ?><span class="transp-tl-date"><?= date('d M', strtotime($e['fecha_salida'])) ?></span><?php endif; ?>
                            <?php if (!empty($e['hora_salida'])): ?><span class="transp-tl-time"><?= substr($e['hora_salida'],0,5) ?></span><?php endif; ?>
                        </div>
                        <?php if (!empty($e['aeropuerto_nombre'])): ?>
                        <div class="transp-tl-sub"><?= $es_vuelo ? '(' . htmlspecialchars($e['aeropuerto']) . ') ' : '' ?><?= htmlspecialchars($e['aeropuerto_nombre']) ?></div>
                        <?php elseif (!empty($e['ciudad'])): ?>
                        <div class="transp-tl-sub"><?= htmlspecialchars($e['ciudad']) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($e['numero']) || !empty($e['duracion']) || $al_e): ?>
                        <div class="transp-tl-leg">
                            <?php if ($al_e && !empty($al_e['icono'])): ?>
                            <img src="<?= htmlspecialchars($al_e['icono']) ?>" alt="<?= htmlspecialchars($al_e['nombre']) ?>" class="transp-tl-al-logo">
                            <?php endif; ?>
                            <?php if (!empty($e['numero'])): ?>
                            <span class="material-icons" style="font-size:12px">flight_takeoff</span>
                            <?php if ($es_vuelo): ?>
                            <a href="https://es.flightaware.com/live/flight/<?= urlencode(strtoupper(str_replace(' ','',$e['numero']))) ?>" target="_blank" rel="noopener" class="transp-flight-link"><?= htmlspecialchars($e['numero']) ?></a>
                            <?php else: ?><?= htmlspecialchars($e['numero']) ?><?php endif; ?>
                            <?php endif; ?>
                            <?php if ($al_e): ?><span class="transp-tl-al-name"><?= htmlspecialchars($al_e['nombre']) ?></span><?php endif; ?>
                            <?php if (!empty($e['duracion'])): ?><span class="transp-tl-dur"><?= htmlspecialchars($e['duracion']) ?></span><?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($e['destino_sig'])): ?>
                        <div class="transp-tl-row <?= $es_ultimo ? 'transp-tl-dot-dest' : 'transp-tl-dot-stop' ?>">
                            <span class="material-icons transp-tl-flight-icon">flight_land</span>
                            <?php if (!empty($e['ciudad_sig'])): ?>
                            <span class="transp-tl-city"><?= htmlspecialchars(strtoupper($e['ciudad_sig'])) ?></span>
                            <span class="transp-tl-sep">·</span>
                            <?php elseif ($es_ultimo && !empty($t['ciudad_destino'])): ?>
                            <span class="transp-tl-city"><?= htmlspecialchars(strtoupper($t['ciudad_destino'])) ?></span>
                            <span class="transp-tl-sep">·</span>
                            <?php endif; ?>
                            <?php if (!empty($e['fecha_llegada_sig'])): ?><span class="transp-tl-date"><?= date('d M', strtotime($e['fecha_llegada_sig'])) ?></span><?php endif; ?>
                            <?php if (!empty($e['hora_llegada_sig'])): ?><span class="transp-tl-time"><?= substr($e['hora_llegada_sig'],0,5) ?></span><?php endif; ?>
                            <?php if ($es_ultimo && $dia_dist): ?><span class="transp-day-badge">+<?= $dias_extra ?></span><?php endif; ?>
                        </div>
                        <?php if (!empty($e['aeropuerto_nombre_sig'])): ?>
                        <div class="transp-tl-sub"><?= $es_vuelo ? '(' . htmlspecialchars($e['destino_sig']) . ') ' : '' ?><?= htmlspecialchars($e['aeropuerto_nombre_sig']) ?></div>
                        <?php elseif ($es_ultimo && !empty($t['aeropuerto_destino'])): ?>
                        <div class="transp-tl-sub"><?= $es_vuelo ? '(' . htmlspecialchars($e['destino_sig']) . ') ' : '' ?><?= htmlspecialchars($t['aeropuerto_destino']) ?></div>
                        <?php elseif (!empty($e['ciudad_sig'])): ?>
                        <div class="transp-tl-sub"><?= htmlspecialchars($e['ciudad_sig']) ?></div>
                        <?php elseif ($es_ultimo && !empty($t['ciudad_destino'])): ?>
                        <div class="transp-tl-sub"><?= htmlspecialchars($t['ciudad_destino']) ?></div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <?php endforeach; ?>

                </div>

                <?php if ($t['notas']): ?>
                <div class="transp-notas" style="margin-top:8px"><?= htmlspecialchars($t['notas']) ?></div>
                <?php endif; ?>
            </div>
        </details>
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
