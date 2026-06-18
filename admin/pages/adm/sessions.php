<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/session_manager.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/activity_log.php';

$myUserId     = (int)$_SESSION['user_id'];
$flash        = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'revocar') {
        $sid = intval($_POST['session_id'] ?? 0);
        if (destroy_session_by_id_any($sid)) {
            log_activity('session_revoked', "Sesión ID: $sid");
            $flash = 'Sesión cerrada correctamente.';
        }
    } elseif ($accion === 'revocar_todas_excepto') {
        $currentToken = $_SESSION['session_token'] ?? '';
        $conn = conectar_bd();
        $stmt = $conn->prepare("DELETE FROM user_sessions WHERE session_token != ?");
        $stmt->bind_param('s', $currentToken);
        $stmt->execute();
        $deleted = $stmt->affected_rows;
        $stmt->close();
        $conn->close();
        log_activity('sessions_revoked_all', "Sesiones cerradas: $deleted");
        $flash = "Se han cerrado $deleted sesión(es).";
    }
}

$sessions     = get_all_active_sessions();
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
                                    <input type="hidden" name="accion" value="revocar_todas_excepto">
                                    <button type="submit" class="btn btn-warning btn-sm"
                                        data-confirm="¿Cerrar todas las sesiones excepto la tuya?">
                                        <i class="fa fa-sign-out-alt"></i> Cerrar todas excepto la mía
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
                                    <thead class="text-uppercase">
                                        <tr>
                                            <th>Usuario</th>
                                            <th>Rol</th>
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
                                            <td>
                                                <div style="font-weight:500;font-size:13px;"><?= htmlspecialchars($s['user_name'] ?? '—') ?></div>
                                                <small style="color:var(--muted-fg);"><?= htmlspecialchars($s['user_email'] ?? '') ?></small>
                                            </td>
                                            <td>
                                                <?php $rol = $s['user_rol'] ?? '—'; ?>
                                                <span class="badge <?= $rol === 'admin' ? 'badge-danger' : 'badge-secondary' ?>">
                                                    <?= htmlspecialchars($rol) ?>
                                                </span>
                                            </td>
                                            <td><small><?= htmlspecialchars($s['ip'] ?? '—') ?></small></td>
                                            <td>
                                                <small><?= htmlspecialchars(substr($s['user_agent'] ?? '—', 0, 70)) ?></small>
                                                <?php if ($isCurrent): ?>
                                                    <span class="badge badge-success ml-1">Tu sesión</span>
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
                                                        data-confirm="<?= $isCurrent ? '¿Cerrar tu sesión? Te desconectarás.' : '¿Cerrar esta sesión?' ?>">
                                                        <i class="fa fa-times"></i>
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
