<?php
include 'includes/protect.php';
include 'includes/header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = conectar_bd();
include 'includes/viaje.php';

// Rango del viaje
$stmt = $conn->prepare("SELECT fecha_inicio, fecha_fin, nombre FROM viajes WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $viaje_id);
$stmt->execute();
$viaje = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fechas con atracciones activas
$stmt = $conn->prepare("SELECT DISTINCT fecha FROM atracciones WHERE viaje_id = ? AND activo = 1");
$stmt->bind_param('i', $viaje_id);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fechas con transportes
$stmt = $conn->prepare("SELECT DISTINCT fecha FROM transportes WHERE viaje_id = ?");
$stmt->bind_param('i', $viaje_id);
$stmt->execute();
$rowsT = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

$diasConPlan       = array_column($rows,  'fecha');
$diasConTransporte = array_column($rowsT, 'fecha');
$hoy               = date('Y-m-d');

$meses = ['', 'Enero','Febrero','Marzo','Abril','Mayo','Junio',
               'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
?>

<body>
<div class="content">
    <div class="fecha">Calendario del viaje</div>

    <?php if (empty($viaje['fecha_inicio']) || empty($viaje['fecha_fin'])): ?>
        <p style="text-align:center;color:#888;margin-top:20px;">
            El viaje no tiene fechas configuradas.
            <a href="/admin/pages/adm/viajes.php">Configurar</a>
        </p>
    <?php else:
        $inicio     = new DateTime($viaje['fecha_inicio']);
        $fin        = new DateTime($viaje['fecha_fin']);
        $mesActual  = new DateTime($inicio->format('Y-m-01'));
        $mesFin     = $fin->format('Y-m-01');

        while ($mesActual->format('Y-m-01') <= $mesFin):
            $year        = (int)$mesActual->format('Y');
            $month       = (int)$mesActual->format('n');
            $daysInMonth = (int)$mesActual->format('t');
            $firstDow    = (int)(new DateTime("$year-$month-01"))->format('N') - 1;
    ?>
        <div class="cal-month">
            <div class="cal-month-title"><?= $meses[$month] . ' ' . $year ?></div>
            <div class="cal-grid">
                <?php foreach (['L','M','X','J','V','S','D'] as $h): ?>
                <div class="cal-day-hdr"><?= $h ?></div>
                <?php endforeach; ?>
                <?php for ($i = 0; $i < $firstDow; $i++): ?>
                <div class="cal-cell cal-empty"></div>
                <?php endfor; ?>
                <?php for ($d = 1; $d <= $daysInMonth; $d++):
                    $dateStr  = sprintf('%04d-%02d-%02d', $year, $month, $d);
                    $inRange  = ($dateStr >= $viaje['fecha_inicio'] && $dateStr <= $viaje['fecha_fin']);
                    $hasPlan  = in_array($dateStr, $diasConPlan);
                    $hasTransp = in_array($dateStr, $diasConTransporte);
                    $isHoy    = ($dateStr === $hoy);

                    $cls = 'cal-cell';
                    if (!$inRange) $cls .= ' cal-off';
                    if ($hasPlan)  $cls .= ' cal-has-plan';
                    if ($isHoy)    $cls .= ' cal-today';
                ?>
                <?php if ($hasPlan && $inRange): ?>
                <a href="index.php?fecha=<?= $dateStr ?>" class="<?= $cls ?>">
                    <?= $d ?>
                    <?php if ($hasTransp): ?><span class="cal-dot"></span><?php endif; ?>
                </a>
                <?php else: ?>
                <div class="<?= $cls ?>">
                    <?= $inRange ? $d : '' ?>
                    <?php if ($hasTransp && $inRange): ?><span class="cal-dot"></span><?php endif; ?>
                </div>
                <?php endif; ?>
                <?php endfor; ?>
            </div>
        </div>
    <?php
            $mesActual->modify('+1 month');
        endwhile;
    endif;
    ?>

    <div class="cal-leyenda">
        <span class="cal-leyenda-item"><span class="cal-sample cal-has-plan"></span> Con plan</span>
        <span class="cal-leyenda-item"><span class="cal-dot"></span> Transporte</span>
        <span class="cal-leyenda-item"><span class="cal-sample cal-today-sample"></span> Hoy</span>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="assets/scripts.js"></script>
</body>
</html>
