<?php
include 'includes/protect.php';
include 'includes/header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$q   = trim($_GET['q'] ?? '');
$conn = conectar_bd();
include 'includes/viaje.php';
$atracciones = [];
$comidas     = [];
$traslados   = [];

if ($q !== '') {
    $like = '%' . $q . '%';

    $stmt = $conn->prepare(
        "SELECT id, nombre, fecha, ciudad FROM atracciones
         WHERE viaje_id = ? AND activo = 1 AND (nombre LIKE ? OR ciudad LIKE ?)
         ORDER BY fecha ASC, nombre ASC"
    );
    $stmt->bind_param('iss', $viaje_id, $like, $like);
    $stmt->execute();
    $atracciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $stmt = $conn->prepare(
        "SELECT id, nombre, puntuacion FROM comida
         WHERE viaje_id = ? AND nombre LIKE ?
         ORDER BY nombre ASC"
    );
    $stmt->bind_param('is', $viaje_id, $like);
    $stmt->execute();
    $comidas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $tbl_check = $conn->query("SHOW TABLES LIKE 'transportes'");
    if ($tbl_check && $tbl_check->num_rows > 0) {
        $stmt = $conn->prepare(
            "SELECT tipo, origen, destino, fecha, numero FROM transportes
             WHERE viaje_id = ? AND (origen LIKE ? OR destino LIKE ? OR numero LIKE ?)
             ORDER BY fecha ASC, hora_salida ASC"
        );
        $stmt->bind_param('isss', $viaje_id, $like, $like, $like);
        $stmt->execute();
        $traslados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }

    $conn->close();
}
?>

<body>
<div class="content">

    <form class="buscar-form" action="" method="get">
        <span class="material-icons buscar-icon">search</span>
        <input type="text"
               name="q"
               class="buscar-input"
               value="<?= htmlspecialchars($q) ?>"
               placeholder="Atracciones, restaurantes…"
               autofocus
               autocomplete="off">
        <?php if ($q): ?>
        <a href="buscar.php" class="buscar-clear material-icons">close</a>
        <?php endif; ?>
    </form>

    <?php if ($q !== ''): ?>
        <?php if (empty($atracciones) && empty($comidas) && empty($traslados)): ?>
            <p class="buscar-empty">Sin resultados para «<?= htmlspecialchars($q) ?>»</p>
        <?php endif; ?>

        <?php if (!empty($atracciones)): ?>
        <div class="buscar-section">
            <div class="buscar-section-title">
                <span class="material-icons">place</span>
                Atracciones (<?= count($atracciones) ?>)
            </div>
            <?php foreach ($atracciones as $a): ?>
            <a href="index.php?fecha=<?= $a['fecha'] ?>" class="buscar-item">
                <div class="buscar-item-info">
                    <strong><?= htmlspecialchars($a['nombre']) ?></strong>
                    <small>
                        <?= $a['ciudad'] ? htmlspecialchars($a['ciudad']) . ' · ' : '' ?>
                        <?= htmlspecialchars(date('d/m/Y', strtotime($a['fecha']))) ?>
                    </small>
                </div>
                <span class="material-icons buscar-arrow">chevron_right</span>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($comidas)): ?>
        <div class="buscar-section">
            <div class="buscar-section-title">
                <span class="material-icons">restaurant</span>
                Gastronomía (<?= count($comidas) ?>)
            </div>
            <?php foreach ($comidas as $c): ?>
            <a href="comida.php" class="buscar-item">
                <div class="buscar-item-info">
                    <strong><?= htmlspecialchars($c['nombre']) ?></strong>
                    <?php if ($c['puntuacion'] > 0): ?>
                    <small>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="material-icons" style="font-size:11px;color:<?= $i <= $c['puntuacion'] ? '#f59e0b' : '#ddd' ?>">star</span>
                        <?php endfor; ?>
                    </small>
                    <?php endif; ?>
                </div>
                <span class="material-icons buscar-arrow">chevron_right</span>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($traslados)): ?>
        <div class="buscar-section">
            <div class="buscar-section-title">
                <span class="material-icons">flight</span>
                Traslados (<?= count($traslados) ?>)
            </div>
            <?php foreach ($traslados as $tr): ?>
            <a href="transportes.php" class="buscar-item">
                <div class="buscar-item-info">
                    <strong><?= htmlspecialchars($tr['origen']) ?> → <?= htmlspecialchars($tr['destino']) ?></strong>
                    <small>
                        <?= htmlspecialchars(date('d/m/Y', strtotime($tr['fecha']))) ?>
                        <?= $tr['numero'] ? ' · ' . htmlspecialchars($tr['numero']) : '' ?>
                    </small>
                </div>
                <span class="material-icons buscar-arrow">chevron_right</span>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>
<script src="assets/scripts.js"></script>
</body>
</html>
