<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = conectar_bd();
$result = $conn->query(
    "SELECT id, user_name, action, detail, ip, created_at
     FROM activity_log
     ORDER BY created_at DESC
     LIMIT 500"
);
$logs = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$conn->close();

$actionLabels = [
    'login_success'            => 'Login correcto',
    'login_failed'             => 'Login fallido',
    'login_blocked'            => 'IP bloqueada',
    'delete_atraccion'         => 'Borrar atracción',
    'delete_user'              => 'Borrar usuario',
    'reset_active_attractions' => 'Reset activar atracciones',
    'reset_seen_attractions'   => 'Reset vistas atracciones',
];

$actionClasses = [
    'login_success'            => 'success',
    'login_failed'             => 'warning',
    'login_blocked'            => 'danger',
    'delete_atraccion'         => 'danger',
    'delete_user'              => 'danger',
    'reset_active_attractions' => 'info',
    'reset_seen_attractions'   => 'info',
];
?>

<!doctype html>
<html class="no-js" lang="es">
<head>
    <meta charset="UTF-8">
    <title>Log de Actividad</title>
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
                            <h4 class="header-title">Log de Actividad <small class="text-muted">(últimos 500 registros)</small></h4>
                            <?php if (empty($logs)): ?>
                                <div class="alert alert-info">No hay registros de actividad todavía.</div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm" id="activityTable">
                                    <thead class="text-uppercase bg-dark text-white">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Usuario</th>
                                            <th>Acción</th>
                                            <th>Detalle</th>
                                            <th>IP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($logs as $log): ?>
                                        <?php
                                        $action = $log['action'];
                                        $label  = $actionLabels[$action] ?? htmlspecialchars($action);
                                        $cls    = $actionClasses[$action] ?? 'secondary';
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($log['created_at']) ?></td>
                                            <td><?= htmlspecialchars($log['user_name'] ?? '—') ?></td>
                                            <td><span class="badge badge-<?= $cls ?>"><?= $label ?></span></td>
                                            <td><?= htmlspecialchars($log['detail'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($log['ip'] ?? '') ?></td>
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
<script>
$(document).ready(function() {
    $('#activityTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            search: 'Buscar:',
            lengthMenu: 'Mostrar _MENU_ registros',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
            paginate: { previous: 'Anterior', next: 'Siguiente' }
        }
    });
});
</script>
</body>
</html>
