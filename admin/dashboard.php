<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include 'includes/templates/head.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/site_config.php';

$conn     = conectar_bd();
$viaje_id = (int)($_SESSION['viaje_id'] ?? 1);

// Atracciones
$stmt = $conn->prepare("SELECT COUNT(*) AS total, SUM(visto) AS vistas, SUM(activo) AS activas FROM atracciones WHERE viaje_id = ?");
$stmt->bind_param("i", $viaje_id); $stmt->execute();
$row = $stmt->get_result()->fetch_assoc(); $stmt->close();
$totalAtracciones = (int)$row['total'];
$vistas           = (int)$row['vistas'];
$activas          = (int)$row['activas'];
$noVistas         = $totalAtracciones - $vistas;

// Comida
$stmt = $conn->prepare("SELECT COUNT(*) AS total, SUM(comido) AS comidas FROM comida WHERE viaje_id = ?");
$stmt->bind_param("i", $viaje_id); $stmt->execute();
$row = $stmt->get_result()->fetch_assoc(); $stmt->close();
$totalComida   = (int)$row['total'];
$comidasHechas = (int)$row['comidas'];

// Tareas
$stmt = $conn->prepare("SELECT COUNT(*) AS total, SUM(completado) AS completadas FROM tareas WHERE viaje_id = ?");
$stmt->bind_param("i", $viaje_id); $stmt->execute();
$row = $stmt->get_result()->fetch_assoc(); $stmt->close();
$totalTareas      = (int)$row['total'];
$tareasCompletas  = (int)$row['completadas'];
$tareasPendientes = $totalTareas - $tareasCompletas;

// Maleta
$stmt = $conn->prepare("SELECT COUNT(*) AS items, COALESCE(SUM(cantidad),0) AS total_cantidad FROM maleta WHERE viaje_id = ?");
$stmt->bind_param("i", $viaje_id); $stmt->execute();
$row = $stmt->get_result()->fetch_assoc(); $stmt->close();
$maletaItems    = (int)$row['items'];
$maletaCantidad = (int)$row['total_cantidad'];

// Último login
$row = $conn->query("SELECT last_conection FROM users WHERE id = " . intval($_SESSION['user_id']))->fetch_assoc();
$ultimoLogin = $row['last_conection'] ?? null;

$conn->close();

// Fechas del viaje desde caché de configuración
$dateStart  = get_config('date_start');
$dateFinish = get_config('date_finish');
$destination = get_config('destination', 'Destino');
$titleWeb    = get_config('title_web', 'Panel Admin');

$today     = new DateTime();
$daysUntil = null;
$tripDays  = null;
$progress  = 0;

if ($dateStart && $dateFinish) {
    $start  = new DateTime($dateStart);
    $finish = new DateTime($dateFinish);
    $tripDays  = (int)$start->diff($finish)->days;
    if ($today < $start) {
        $daysUntil = (int)$today->diff($start)->days;
    } elseif ($today <= $finish) {
        $elapsed  = (int)$start->diff($today)->days;
        $progress = $tripDays > 0 ? round(($elapsed / $tripDays) * 100) : 0;
    }
}
?>

<!doctype html>
<html class="no-js" lang="es">
<body>
<div id="preloader"><div class="loader"></div></div>

<div class="page-container">
    <?php include 'includes/templates/sidebar.php'; ?>
    <?php include 'includes/templates/user-profile.php'; ?>

    <div class="main-content">
        <div class="main-content-inner">

            <!-- Cabecera del viaje -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card" style="background: linear-gradient(135deg,#667eea,#764ba2); color:#fff;">
                        <div class="card-body py-4">
                            <h3 class="mb-1"><?= htmlspecialchars($titleWeb) ?></h3>
                            <p class="mb-2"><i class="fa fa-map-marker"></i> <?= htmlspecialchars($destination) ?></p>
                            <?php if ($dateStart && $dateFinish): ?>
                                <p class="mb-1">
                                    <i class="fa fa-calendar"></i>
                                    <?= htmlspecialchars($dateStart) ?> &rarr; <?= htmlspecialchars($dateFinish) ?>
                                    &nbsp;&bull;&nbsp; <?= $tripDays ?> días
                                </p>
                                <?php if ($daysUntil !== null): ?>
                                    <p class="mb-0"><strong>Faltan <?= $daysUntil ?> días para el viaje</strong></p>
                                <?php elseif ($progress > 0): ?>
                                    <p class="mb-1">Viaje en curso — <?= $progress ?>% completado</p>
                                    <div class="progress" style="height:8px;">
                                        <div class="progress-bar bg-warning" style="width:<?= $progress ?>%"></div>
                                    </div>
                                <?php else: ?>
                                    <p class="mb-0">Viaje completado</p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjetas de estadísticas -->
            <div class="row mt-3">

                <!-- Atracciones -->
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-left-primary h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Atracciones</div>
                                    <div class="h5 mb-0 font-weight-bold"><?= $totalAtracciones ?> total</div>
                                    <small class="text-muted">
                                        <i class="fa fa-eye"></i> <?= $vistas ?> vistas &bull;
                                        <i class="fa fa-eye-slash"></i> <?= $noVistas ?> pendientes &bull;
                                        <?= $activas ?> activas
                                    </small>
                                    <?php if ($totalAtracciones > 0): ?>
                                        <div class="progress mt-2" style="height:5px;">
                                            <div class="progress-bar bg-primary" style="width:<?= round(($vistas/$totalAtracciones)*100) ?>%"></div>
                                        </div>
                                        <small class="text-muted"><?= round(($vistas/$totalAtracciones)*100) ?>% vistas</small>
                                    <?php endif; ?>
                                </div>
                                <div class="col-auto"><i class="fa fa-map-signs fa-2x text-primary opacity-25"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comida -->
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-left-success h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Comida</div>
                                    <div class="h5 mb-0 font-weight-bold"><?= $totalComida ?> platos</div>
                                    <small class="text-muted">
                                        <i class="fa fa-check"></i> <?= $comidasHechas ?> probados &bull;
                                        <?= $totalComida - $comidasHechas ?> pendientes
                                    </small>
                                    <?php if ($totalComida > 0): ?>
                                        <div class="progress mt-2" style="height:5px;">
                                            <div class="progress-bar bg-success" style="width:<?= round(($comidasHechas/$totalComida)*100) ?>%"></div>
                                        </div>
                                        <small class="text-muted"><?= round(($comidasHechas/$totalComida)*100) ?>% probados</small>
                                    <?php endif; ?>
                                </div>
                                <div class="col-auto"><i class="fa fa-cutlery fa-2x text-success opacity-25"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tareas -->
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-left-warning h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tareas</div>
                                    <div class="h5 mb-0 font-weight-bold"><?= $totalTareas ?> total</div>
                                    <small class="text-muted">
                                        <i class="fa fa-check-circle"></i> <?= $tareasCompletas ?> completadas &bull;
                                        <?= $tareasPendientes ?> pendientes
                                    </small>
                                    <?php if ($totalTareas > 0): ?>
                                        <div class="progress mt-2" style="height:5px;">
                                            <div class="progress-bar bg-warning" style="width:<?= round(($tareasCompletas/$totalTareas)*100) ?>%"></div>
                                        </div>
                                        <small class="text-muted"><?= round(($tareasCompletas/$totalTareas)*100) ?>% completadas</small>
                                    <?php endif; ?>
                                </div>
                                <div class="col-auto"><i class="fa fa-tasks fa-2x text-warning opacity-25"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Maleta -->
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-left-info h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Maleta</div>
                                    <div class="h5 mb-0 font-weight-bold"><?= $maletaItems ?> artículos</div>
                                    <small class="text-muted"><?= $maletaCantidad ?> unidades en total</small>
                                </div>
                                <div class="col-auto"><i class="fa fa-suitcase fa-2x text-info opacity-25"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Accesos rápidos -->
            <div class="row mt-2">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="header-title">Accesos Rápidos</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="/admin/pages/atracciones/add-atraccion.php" class="btn btn-outline-primary btn-sm"><i class="fa fa-plus"></i> Nueva atracción</a>
                                <a href="/admin/pages/comida/add-food.php" class="btn btn-outline-success btn-sm"><i class="fa fa-plus"></i> Nuevo plato</a>
                                <a href="/admin/pages/tools/task.php" class="btn btn-outline-warning btn-sm"><i class="fa fa-tasks"></i> Tareas</a>
                                <a href="/admin/pages/tools/maleta.php" class="btn btn-outline-info btn-sm"><i class="fa fa-suitcase"></i> Maleta</a>
                                <a href="/admin/pages/atracciones/planning.php" class="btn btn-outline-secondary btn-sm"><i class="fa fa-calendar"></i> Calendario</a>
                                <a href="/admin/pages/adm/webconfig.php" class="btn btn-outline-dark btn-sm"><i class="fa fa-cogs"></i> Configuración</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($ultimoLogin): ?>
            <p class="text-muted small mt-2 text-right">Último acceso: <?= htmlspecialchars($ultimoLogin) ?></p>
            <?php endif; ?>

        </div>
    </div>

    <?php include 'includes/templates/footer.php'; ?>
</div>

<?php include 'includes/libraries/scripts.php'; ?>
</body>
</html>
