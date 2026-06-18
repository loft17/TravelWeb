<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/activity_log.php';

$conn = conectar_bd();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $conn->query("UPDATE atracciones SET activo = 1 WHERE activo = 0");
    $affected = $conn->affected_rows;
    $conn->close();

    log_activity('reset_active_attractions', "Activadas: $affected atracción(es)");

    $_SESSION['flash'] = ['type' => 'success', 'msg' => "Se han activado $affected atracción(es)."];
    header('Location: /admin/pages/atracciones/show-atraccions.php');
    exit();
}

// GET: previsualización de afectados
$preview     = $conn->query("SELECT id, nombre FROM atracciones WHERE activo = 0");
$previewRows = $preview ? $preview->fetch_all(MYSQLI_ASSOC) : [];
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
                            <h4 class="header-title">Activar todas las atracciones</h4>

                            <?php if (empty($previewRows)): ?>
                                <div class="alert alert-info">
                                    No hay atracciones inactivas. No se realizará ningún cambio.
                                </div>
                                <a href="show-atraccions.php" class="btn btn-secondary">Volver</a>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    Esta acción activará <strong><?= count($previewRows) ?></strong> atracción(es) inactiva(s). ¿Confirmas?
                                </div>
                                <ul>
                                    <?php foreach ($previewRows as $row): ?>
                                        <li>ID <?= intval($row['id']) ?> — <?= htmlspecialchars($row['nombre']) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <button type="submit" class="btn btn-danger">Confirmar y activar todas</button>
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
