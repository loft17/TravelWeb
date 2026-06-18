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
$dateStart   = get_config('date_start');
$dateFinish  = get_config('date_finish');
$destination = get_config('destination', 'Destino');
$titleWeb    = get_config('title_web', 'Panel Admin');

// Preferir fechas del viaje activo si las tiene definidas
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/viajes.php';
$viajeActivo = get_viaje_activo();
if (!empty($viajeActivo['fecha_inicio'])) $dateStart  = $viajeActivo['fecha_inicio'];
if (!empty($viajeActivo['fecha_fin']))    $dateFinish = $viajeActivo['fecha_fin'];

$today     = new DateTime('today');
$daysUntil = null;
$tripDays  = null;
$progress  = 0;
$tripState = 'none'; // 'future' | 'ongoing' | 'past' | 'none'
$countdownTarget = null;

if ($dateStart && $dateFinish) {
    $start  = new DateTime($dateStart);
    $finish = new DateTime($dateFinish);
    $finish->setTime(23, 59, 59);
    $tripDays = (int)$start->diff($finish)->days;

    if ($today < $start) {
        $tripState = 'future';
        $daysUntil = (int)(new DateTime())->diff($start)->days;
        $countdownTarget = $start->format('Y-m-d') . 'T00:00:00';
    } elseif ($today <= $finish) {
        $tripState = 'ongoing';
        $elapsed   = (int)$start->diff($today)->days;
        $progress  = $tripDays > 0 ? round(($elapsed / $tripDays) * 100) : 0;
        $countdownTarget = $finish->format('Y-m-d\TH:i:s');
    } else {
        $tripState = 'past';
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
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <h3 class="mb-1"><?= htmlspecialchars($titleWeb) ?></h3>
                                    <?php if (!empty($viajeActivo['nombre'])): ?>
                                        <p class="mb-1" style="opacity:.85;font-size:15px;">
                                            ✈ <?= htmlspecialchars($viajeActivo['nombre']) ?>
                                            <?php if (!empty($viajeActivo['destino'])): ?>
                                                &mdash; <?= htmlspecialchars($viajeActivo['destino']) ?>
                                            <?php endif; ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($dateStart && $dateFinish): ?>
                                        <p class="mb-2" style="opacity:.75;font-size:13px;">
                                            <i class="fa fa-calendar"></i>
                                            <?= date('d/m/Y', strtotime($dateStart)) ?> &rarr; <?= date('d/m/Y', strtotime($dateFinish)) ?>
                                            &bull; <?= $tripDays ?> días
                                        </p>
                                        <?php if ($tripState === 'ongoing'): ?>
                                            <p class="mb-1" style="font-size:13px;">Viaje en curso — <?= $progress ?>% completado</p>
                                            <div class="progress" style="height:6px; max-width:300px; background:rgba(255,255,255,.3);">
                                                <div class="progress-bar bg-warning" style="width:<?= $progress ?>%"></div>
                                            </div>
                                        <?php elseif ($tripState === 'past'): ?>
                                            <p class="mb-0">🎉 Viaje completado</p>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="mb-0" style="opacity:.7;font-size:13px;">
                                            <a href="/admin/pages/adm/viajes.php" style="color:#fff;text-decoration:underline;">
                                                Añade las fechas del viaje para ver el contador
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <?php if ($countdownTarget): ?>
                                <div class="col-md-5 text-center mt-3 mt-md-0">
                                    <p class="mb-2" style="font-size:12px; opacity:.8; text-transform:uppercase; letter-spacing:1px;">
                                        <?= $tripState === 'future' ? 'Faltan para el viaje' : 'Tiempo restante' ?>
                                    </p>
                                    <div id="countdown" style="display:flex; justify-content:center; gap:10px;">
                                        <?php foreach (['days' => 'Días', 'hours' => 'Horas', 'mins' => 'Min', 'secs' => 'Seg'] as $id => $label): ?>
                                        <div style="background:rgba(0,0,0,.25); border-radius:10px; padding:10px 14px; min-width:64px;">
                                            <div id="cd-<?= $id ?>" style="font-size:28px; font-weight:700; line-height:1; font-variant-numeric:tabular-nums;">00</div>
                                            <div style="font-size:10px; opacity:.75; text-transform:uppercase; margin-top:3px;"><?= $label ?></div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php if ($tripState === 'past'): ?>
                                        <p class="mt-2 mb-0" style="font-size:12px; opacity:.8;">El viaje ha terminado</p>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                            </div>
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

<?php if ($countdownTarget): ?>
<style>
#cd-days, #cd-hours, #cd-mins, #cd-secs {
    transition: transform .15s ease, opacity .15s ease;
}
.cd-flip {
    transform: translateY(-4px) scale(1.12);
    opacity: .6;
}
</style>
<script>
(function () {
    var target = new Date("<?= $countdownTarget ?>").getTime();
    var ids    = ['days','hours','mins','secs'];
    var prev   = {};

    function pad(n) { return n < 10 ? '0' + n : '' + n; }

    function tick() {
        var now  = Date.now();
        var diff = target - now;

        if (diff <= 0) {
            ids.forEach(function(id) {
                document.getElementById('cd-' + id).textContent = '00';
            });
            return;
        }

        var days  = Math.floor(diff / 86400000);
        var hours = Math.floor((diff % 86400000) / 3600000);
        var mins  = Math.floor((diff % 3600000)  / 60000);
        var secs  = Math.floor((diff % 60000)    / 1000);
        var vals  = { days: days, hours: hours, mins: mins, secs: secs };

        ids.forEach(function(id) {
            var el  = document.getElementById('cd-' + id);
            var val = pad(vals[id]);
            if (val !== prev[id]) {
                el.classList.add('cd-flip');
                setTimeout(function() {
                    el.textContent = val;
                    el.classList.remove('cd-flip');
                }, 150);
                prev[id] = val;
            }
        });

        setTimeout(tick, 1000);
    }

    tick();
})();
</script>
<?php endif; ?>

</body>
</html>
