<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/activity_log.php';

$conn = conectar_bd();

$viaje_id = (int)($_SESSION['viaje_id'] ?? 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $stmt = $conn->prepare("UPDATE atracciones SET visto = 0 WHERE visto = 1 AND viaje_id = ?");
    $stmt->bind_param("i", $viaje_id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();
    $conn->close();

    log_activity('reset_seen_attractions', "Marcadas no vistas: $affected atracción(es)");

    $_SESSION['flash'] = ['type' => 'success', 'msg' => "Se han marcado como no vistas $affected atracción(es)."];
    header('Location: /admin/pages/atracciones/show-atraccions.php');
    exit();
}

// GET: previsualización de afectados
$stmt2 = $conn->prepare("SELECT id, nombre FROM atracciones WHERE visto = 1 AND viaje_id = ?");
$stmt2->bind_param("i", $viaje_id);
$stmt2->execute();
$previewRows = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt2->close();
$conn->close();

include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
?>
<!doctype html>
<html class="no-js" lang="es">
<body>
<div class="page-container">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php'; ?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php'; ?>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="row">
                <div class="col-12 mt-5">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Marcar todas como NO vistas</h4>

                            <?php if (empty($previewRows)): ?>
                                <div class="alert alert-info">
                                    No hay atracciones marcadas como vistas. No se realizará ningún cambio.
                                </div>
                                <a href="show-atraccions.php" class="btn btn-secondary">Volver</a>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    Esta acción marcará como <strong>no vistas</strong> <?= count($previewRows) ?> atracción(es). ¿Confirmas?
                                </div>
                                <ul>
                                    <?php foreach ($previewRows as $row): ?>
                                        <li>ID <?= intval($row['id']) ?> — <?= htmlspecialchars($row['nombre']) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <button type="submit" class="btn btn-danger">Confirmar y resetear vistas</button>
                                    <a href="show-atraccions.php" class="btn btn-secondary ms-2">Cancelar</a>
                                </form>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php'; ?>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php'; ?>
</body>
</html>
