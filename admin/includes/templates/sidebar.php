<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/viajes.php';

$viajeActualNombre = get_viaje_activo()['nombre'] ?? 'Mi Viaje';
$todosLosViajes    = get_all_viajes();
$viajeActivoId     = get_viaje_activo_id();

// Obtener el nombre del archivo actual sin extensión
$activePage = basename($_SERVER['SCRIPT_FILENAME'], '.php');

// Definir los arrays de páginas para cada grupo del menú
$atraccionesPages    = ['add-atraccion', 'show-atraccions', 'planning', 'mapa', 'reset-seen-attractions', 'reset-active-attractions'];
$platosPages         = ['add-food', 'show-foods', 'change-food'];
$transportesPages    = ['show-transportes', 'show-aerolineas'];
$utilidadesPages     = ['task', 'maleta', 'emojis', 'gastos'];
$ficherosPages       = ['show-imgs', 'upload-imgs'];
$bbddPages           = ['export-json', 'export-sql'];
$administracionPages = ['show-users', 'webconfig', 'activity-log', 'sessions', 'viajes'];

// Determinar si cada grupo está activo
$atraccionesActive   = in_array($activePage, $atraccionesPages) ? 'active' : '';
$platosActive        = in_array($activePage, $platosPages) ? 'active' : '';
$transportesActive   = in_array($activePage, $transportesPages) ? 'active' : '';
$utilidadesActive    = in_array($activePage, $utilidadesPages) ? 'active' : '';
$ficherosActive      = in_array($activePage, $ficherosPages) ? 'active' : '';
$bbddActive          = in_array($activePage, $bbddPages) ? 'active' : '';
$administracionActive = in_array($activePage, $administracionPages) ? 'active' : '';
?>

<!-- sidebar menu area start -->
<div class="sidebar-menu">
    <div class="sidebar-header">
        <div class="logo">
            <a href="/index.html"><img src="/admin/assets/images/icon/logo.png" alt="logo"></a>
        </div>
    </div>
    <!-- Selector de viaje -->
    <div class="sidebar-viaje-selector">
        <span class="sidebar-viaje-label">Viaje activo</span>
        <div class="svs-wrap" id="svs-wrap">
            <button class="svs-trigger" id="svs-trigger" type="button">
                <i class="fa fa-map-marked-alt svs-icon"></i>
                <span class="svs-current"><?= htmlspecialchars($viajeActualNombre) ?></span>
                <i class="fa fa-chevron-down svs-chevron"></i>
            </button>
            <ul class="svs-menu" id="svs-menu" role="listbox">
                <?php foreach ($todosLosViajes as $v): ?>
                <li class="svs-item <?= (int)$v['id'] === $viajeActivoId ? 'svs-item--active' : '' ?>"
                    role="option"
                    data-href="/admin/switch-viaje.php?id=<?= (int)$v['id'] ?>">
                    <?= htmlspecialchars($v['nombre']) ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <a href="/admin/pages/adm/viajes.php" class="sidebar-viaje-manage">
            <i class="fa fa-plus"></i> Gestionar viajes
        </a>
    </div>
    <script>
    (function () {
        var wrap    = document.getElementById('svs-wrap');
        var trigger = document.getElementById('svs-trigger');
        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            wrap.classList.toggle('open');
        });
        document.addEventListener('click', function () {
            wrap.classList.remove('open');
        });
        document.querySelectorAll('.svs-item').forEach(function (item) {
            item.addEventListener('click', function () {
                window.location = this.dataset.href;
            });
        });
    })();
    </script>
    <div class="main-menu">
        <div class="menu-inner">
            <nav>
                <ul class="metismenu" id="menu">

                    <!-- VIAJE -->
                    <div class="nav-label">Viaje</div>

                    <li class="<?= $atraccionesActive ?>">
                        <a href="javascript:void(0)">
                            <i class="fa fa-map-marker-alt"></i>
                            <span>Atracciones</span>
                        </a>
                        <ul class="<?= $atraccionesActive ?>">
                            <li class="<?= $activePage=='add-atraccion'?'active':'' ?>">
                                <a href="/admin/pages/atracciones/add-atraccion.php">+ Nueva atracción</a>
                            </li>
                            <li class="<?= $activePage=='show-atraccions'?'active':'' ?>">
                                <a href="/admin/pages/atracciones/show-atraccions.php">Ver todas</a>
                            </li>
                            <li class="<?= $activePage=='mapa'?'active':'' ?>">
                                <a href="/admin/pages/atracciones/mapa.php">Mapa</a>
                            </li>
                            <li class="<?= $activePage=='planning'?'active':'' ?>">
                                <a href="/admin/pages/atracciones/planning.php">Calendario</a>
                            </li>
                            <li class="<?= $activePage=='reset-seen-attractions'?'active':'' ?>">
                                <a href="/admin/pages/atracciones/reset-seen-attractions.php">Reset vistas</a>
                            </li>
                            <li class="<?= $activePage=='reset-active-attractions'?'active':'' ?>">
                                <a href="/admin/pages/atracciones/reset-active-attractions.php">Reset activas</a>
                            </li>
                        </ul>
                    </li>

                    <li class="<?= $platosActive ?>">
                        <a href="javascript:void(0)">
                            <i class="fa fa-utensils"></i>
                            <span>Comida</span>
                        </a>
                        <ul class="<?= $platosActive ?>">
                            <li class="<?= $activePage=='add-food'?'active':'' ?>">
                                <a href="/admin/pages/comida/add-food.php">+ Nuevo plato</a>
                            </li>
                            <li class="<?= $activePage=='show-foods'?'active':'' ?>">
                                <a href="/admin/pages/comida/show-foods.php">Ver platos</a>
                            </li>
                            <li class="<?= $activePage=='change-food'?'active':'' ?>">
                                <a href="/admin/pages/comida/change-food.php">Cambiar estado</a>
                            </li>
                        </ul>
                    </li>

                    <li class="<?= $transportesActive ?>">
                        <a href="javascript:void(0)">
                            <i class="fa fa-route"></i>
                            <span>Transportes</span>
                        </a>
                        <ul class="<?= $transportesActive ?>">
                            <li class="<?= $activePage=='show-transportes'?'active':'' ?>">
                                <a href="/admin/pages/transportes/show-transportes.php">Ver traslados</a>
                            </li>
                            <li class="<?= $activePage=='show-aerolineas'?'active':'' ?>">
                                <a href="/admin/pages/aerolineas/show-aerolineas.php">Aerolíneas</a>
                            </li>
                        </ul>
                    </li>

                    <!-- PLANNING -->
                    <div class="nav-label">Planificación</div>

                    <li class="<?= $utilidadesActive ?>">
                        <a href="javascript:void(0)">
                            <i class="fa fa-toolbox"></i>
                            <span>Utilidades</span>
                        </a>
                        <ul class="<?= $utilidadesActive ?>">
                            <li class="<?= $activePage=='task'?'active':'' ?>">
                                <a href="/admin/pages/tools/task.php">Tareas</a>
                            </li>
                            <li class="<?= $activePage=='maleta'?'active':'' ?>">
                                <a href="/admin/pages/tools/maleta.php">Maleta</a>
                            </li>
                            <li class="<?= $activePage=='gastos'?'active':'' ?>">
                                <a href="/admin/pages/tools/gastos.php">Gastos</a>
                            </li>
                            <li class="<?= $activePage=='emojis'?'active':'' ?>">
                                <a href="/admin/pages/tools/emojis.php">Emojis</a>
                            </li>
                        </ul>
                    </li>

                    <!-- SISTEMA -->
                    <div class="nav-label">Sistema</div>

                    <li class="<?= $ficherosActive ?>">
                        <a href="javascript:void(0)">
                            <i class="fa fa-images"></i>
                            <span>Ficheros</span>
                        </a>
                        <ul class="<?= $ficherosActive ?>">
                            <li class="<?= $activePage=='show-imgs'?'active':'' ?>">
                                <a href="/admin/pages/files/show-imgs.php">Galería</a>
                            </li>
                            <li class="<?= $activePage=='upload-imgs'?'active':'' ?>">
                                <a href="/admin/pages/files/upload-imgs.php">Subir imagen</a>
                            </li>
                        </ul>
                    </li>

                    <li class="<?= $bbddActive ?>">
                        <a href="javascript:void(0)">
                            <i class="fa fa-database"></i>
                            <span>Base de datos</span>
                        </a>
                        <ul class="<?= $bbddActive ?>">
                            <li class="<?= $activePage=='export-json'?'active':'' ?>">
                                <a href="/admin/pages/bbdd/export-json.php">Export JSON</a>
                            </li>
                            <li class="<?= $activePage=='export-sql'?'active':'' ?>">
                                <a href="/admin/pages/bbdd/export-sql.php">Export SQL</a>
                            </li>
                        </ul>
                    </li>

                    <li class="<?= $administracionActive ?>">
                        <a href="javascript:void(0)">
                            <i class="fa fa-sliders-h"></i>
                            <span>Administración</span>
                        </a>
                        <ul class="<?= $administracionActive ?>">
                            <li class="<?= $activePage=='viajes'?'active':'' ?>">
                                <a href="/admin/pages/adm/viajes.php">Gestionar Viajes</a>
                            </li>
                            <li class="<?= $activePage=='webconfig'?'active':'' ?>">
                                <a href="/admin/pages/adm/webconfig.php">Configuración</a>
                            </li>
                            <li class="<?= $activePage=='show-users'?'active':'' ?>">
                                <a href="/admin/pages/adm/show-users.php">Usuarios</a>
                            </li>
                            <li class="<?= $activePage=='activity-log'?'active':'' ?>">
                                <a href="/admin/pages/adm/activity-log.php">Log actividad</a>
                            </li>
                            <li class="<?= $activePage=='sessions'?'active':'' ?>">
                                <a href="/admin/pages/adm/sessions.php">Sesiones activas</a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </nav>
        </div>
    </div>
</div>
<!-- sidebar menu area end -->
