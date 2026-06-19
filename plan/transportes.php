<?php
include 'includes/protect.php';
include 'includes/header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = conectar_bd();
include 'includes/viaje.php';

$transportes = [];
$tbl_check = $conn->query("SHOW TABLES LIKE 'transportes'");
if ($tbl_check && $tbl_check->num_rows > 0) {
    $stmt = $conn->prepare(
        "SELECT * FROM transportes WHERE viaje_id = ? ORDER BY fecha ASC, hora_salida ASC"
    );
    $stmt->bind_param('i', $viaje_id);
    $stmt->execute();
    $transportes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
$conn->close();

$iconos = [
    'avion'  => 'flight',
    'bus'    => 'directions_bus',
    'tren'   => 'train',
    'ferry'  => 'directions_boat',
    'taxi'   => 'local_taxi',
    'coche'  => 'directions_car',
    'otro'   => 'route',
];

$labels = [
    'avion'  => 'Vuelo',
    'bus'    => 'Bus',
    'tren'   => 'Tren',
    'ferry'  => 'Ferry',
    'taxi'   => 'Taxi',
    'coche'  => 'Coche',
    'otro'   => 'Traslado',
];

// Agrupar por fecha
$por_fecha = [];
foreach ($transportes as $t) {
    $por_fecha[$t['fecha']][] = $t;
}
?>

<body>
<div class="content">
    <div class="fecha">Traslados</div>

    <?php if (empty($transportes)): ?>
        <p style="text-align:center;color:#888;margin-top:30px;">No hay traslados registrados.</p>
    <?php else: ?>
        <?php foreach ($por_fecha as $fecha => $items): ?>
        <div class="transp-fecha-grupo">
            <div class="transp-fecha-hdr">
                <?= date('l, d M Y', strtotime($fecha)) ?>
            </div>
            <?php foreach ($items as $t): ?>
            <div class="transp-card transp-card-full">
                <div class="transp-tipo">
                    <span class="material-icons"><?= $iconos[$t['tipo']] ?? 'route' ?></span>
                    <span class="transp-label"><?= $labels[$t['tipo']] ?? 'Traslado' ?></span>
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
                        <span class="transp-num"><?= htmlspecialchars($t['numero']) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($t['notas']): ?>
                    <div class="transp-notas"><?= htmlspecialchars($t['notas']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
<script src="assets/scripts.js"></script>
</body>
</html>
