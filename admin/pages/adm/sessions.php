<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/session_manager.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/activity_log.php';

$userId = (int)$_SESSION['user_id'];
$flash  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'revocar') {
        $sid = intval($_POST['session_id'] ?? 0);
        if (destroy_session_by_id($sid, $userId)) {
            log_activity('session_revoked', "Sesión ID: $sid");
            $flash = 'Sesión cerrada correctamente.';
        }
    } elseif ($accion === 'revocar_otras') {
        $currentToken = $_SESSION['session_token'] ?? '';
        $conn = conectar_bd();
        $stmt = $conn->prepare("DELETE FROM user_sessions WHERE user_id = ? AND session_token != ?");
        $stmt->bind_param('is', $userId, $currentToken);
        $stmt->execute();
        $deleted = $stmt->affected_rows;
        $stmt->close();
        $conn->close();
        log_activity('sessions_revoked_others', "Sesiones cerradas: $deleted");
        $flash = "Se han cerrado $deleted sesión(es) adicional(es).";
    }
}

$sessions     = get_active_sessions($userId);
$currentToken = $_SESSION['session_token'] ?? '';

include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
?>
<!doctype html>
<html class="no-js" lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sesiones Activas</title>
</head>
<body>
<div class="page-container">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php'; ?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php'; ?>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="header-title mb-0">
                                    Sesiones Activas
                                    <span class="badge badge-primary"><?= count($sessions) ?></span>
                                </h4>
                                <?php if (count($sessions) > 1): ?>
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <input type="hidden" name="accion" value="revocar_otras">
                                    <button type="submit" class="btn btn-warning btn-sm"
                                        data-confirm="¿Cerrar todas las sesiones excepto la actual?">
                                        <i class="fa fa-sign-out"></i> Cerrar otras sesiones
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>

                            <?php if ($flash): ?>
                                <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
                            <?php endif; ?>

                            <?php if (empty($sessions)): ?>
                                <div class="alert alert-info">No hay sesiones activas registradas. Las sesiones se registran desde el próximo login.</div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="bg-dark text-white text-uppercase">
                                        <tr>
                                            <th>IP</th>
                                            <th>Navegador / Dispositivo</th>
                                            <th>Inicio</th>
                                            <th>Última actividad</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($sessions as $s): ?>
                                        <?php $isCurrent = ($s['session_token'] === $currentToken); ?>
                                        <tr class="<?= $isCurrent ? 'table-success' : '' ?>">
                                            <td><?= htmlspecialchars($s['ip'] ?? '—') ?></td>
                                            <td>
                                                <small><?= htmlspecialchars(substr($s['user_agent'] ?? '—', 0, 80)) ?></small>
                                                <?php if ($isCurrent): ?>
                                                    <span class="badge badge-success ml-1">Sesión actual</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><small><?= htmlspecialchars($s['created_at']) ?></small></td>
                                            <td><small><?= htmlspecialchars($s['last_activity']) ?></small></td>
                                            <td>
                                                <form method="post">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                                    <input type="hidden" name="accion" value="revocar">
                                                    <input type="hidden" name="session_id" value="<?= intval($s['id']) ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        data-confirm="<?= $isCurrent ? '¿Cerrar la sesión actual? Te desconectarás.' : '¿Cerrar esta sesión?' ?>">
                                                        <i class="fa fa-times"></i> <?= $isCurrent ? 'Cerrar sesión' : 'Revocar' ?>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
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
