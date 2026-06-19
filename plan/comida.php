<?php
include 'includes/protect.php';
include 'includes/header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = conectar_bd();
include 'includes/viaje.php';

$stmt = $conn->prepare(
    "SELECT * FROM comida WHERE viaje_id = ? ORDER BY comido ASC, nombre ASC"
);
$stmt->bind_param('i', $viaje_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<body>
<div class="content">
    <div class="fecha">Gastronomía</div>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <details class="entrada <?= $row['comido'] ? 'entrada-comida' : '' ?>">
            <summary>
                <span><?= htmlspecialchars($row['nombre']) ?></span>
                <?php if ($row['puntuacion'] > 0): ?>
                <span class="puntuacion-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span class="material-icons" style="font-size:13px;color:<?= $i <= $row['puntuacion'] ? '#f59e0b' : '#ddd' ?>">star</span>
                    <?php endfor; ?>
                </span>
                <?php endif; ?>
                <input type="checkbox"
                       id="comido_<?= $row['id'] ?>"
                       class="seen-checkbox comido-cb"
                       data-id="<?= $row['id'] ?>"
                       data-comido="<?= $row['comido'] ? 'true' : 'false' ?>"
                       <?= $row['comido'] ? 'checked' : '' ?>>
                <label for="comido_<?= $row['id'] ?>" class="seen-label">
                    <span class="check-icon material-icons <?= $row['comido'] ? 'checked' : '' ?>">
                        <?= $row['comido'] ? 'check_circle' : 'check_circle_outline' ?>
                    </span>
                </label>
            </summary>
            <?php if ($row['imagen_url']): ?>
                <img src="<?= htmlspecialchars($row['imagen_url']) ?>"
                     alt="<?= htmlspecialchars($row['nombre']) ?>">
            <?php endif; ?>
            <?php
                $plain = strip_tags($row['descripcion'] ?? '');
                $short = mb_substr($plain, 0, 200, 'UTF-8');
            ?>
            <?php if ($short): ?>
            <p>
                <?= htmlspecialchars($short) ?>
                <?= mb_strlen($plain, 'UTF-8') > 200 ? '…' : '' ?>
            </p>
            <?php endif; ?>
        </details>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center;color:#888;margin-top:30px;">No hay platos registrados.</p>
    <?php endif; ?>

    <?php $conn->close(); ?>
</div>

<?php include 'includes/footer.php'; ?>
<script src="assets/scripts.js"></script>
<script>
document.querySelectorAll('.comido-cb').forEach(function (cb) {
    cb.addEventListener('change', function () {
        var id     = this.dataset.id;
        var comido = this.dataset.comido;
        var icon   = this.nextElementSibling.querySelector('.check-icon');
        fetch('/plan/includes/update_comido.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({action: 'toggle_comido', id: id, comido: comido})
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                cb.dataset.comido = data.comido ? 'true' : 'false';
                if (data.comido) {
                    icon.classList.add('checked');
                    icon.textContent = 'check_circle';
                } else {
                    icon.classList.remove('checked');
                    icon.textContent = 'check_circle_outline';
                }
            }
        });
    });
});
</script>
</body>
</html>
