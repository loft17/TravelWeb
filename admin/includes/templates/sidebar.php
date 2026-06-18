<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/viajes.php';

$viajeActualNombre = get_viaje_activo()['nombre'] ?? 'Mi Viaje';
$todosLosViajes    = get_all_viajes();
$viajeActivoId     = get_viaje_activo_id();

// Obtener el nombre del archivo actual sin extensión
$activePage = basename($_SERVER['SCRIPT_FILENAME'], '.php');

// Definir los arrays de páginas para cada grupo del menú
$atraccionesPages = ['add-atraccion', 'show-atraccions', 'planning', 'mapa', 'reset-seen-attractions', 'reset-active-attractions'];
$platosPages      = ['add-food', 'show-foods', 'change-food'];
$utilidadesPages  = ['task', 'maleta', 'emojis', 'gastos'];
$ficherosPages    = ['show-imgs', 'upload-imgs'];
$bbddPages        = ['export-json', 'export-sql'];
$administracionPages = ['show-users', 'webconfig', 'activity-log', 'sessions', 'viajes'];

// Determinar si cada grupo está activo
$atraccionesActive   = in_array($activePage, $atraccionesPages) ? 'active' : '';
$platosActive        = in_array($activePage, $platosPages) ? 'active' : '';
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
    <div style="padding:10px 16px 8px; border-bottom:1px solid rgba(255,255,255,0.1);">
        <div style="font-size:10px; text-transform:uppercase; color:rgba(255,255,255,0.5); margin-bottom:3px;">Viaje activo</div>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-light w-100 text-left dropdown-toggle" type="button" data-toggle="dropdown" style="font-size:13px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                ✈ <?= htmlspecialchars($viajeActualNombre) ?>
            </button>
            <div class="dropdown-menu" style="min-width:200px;">
                <?php foreach ($todosLosViajes as $v): ?>
                <a class="dropdown-item <?= (int)$v['id'] === $viajeActivoId ? 'active' : '' ?>"
                   href="/admin/switch-viaje.php?id=<?= (int)$v['id'] ?>">
                    <?= htmlspecialchars($v['nombre']) ?>
                    <?php if ((int)$v['id'] === $viajeActivoId): ?> ✓<?php endif; ?>
                </a>
                <?php endforeach; ?>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="/admin/pages/adm/viajes.php">
                    <i class="fa fa-plus"></i> Gestionar viajes
                </a>
            </div>
        </div>
    </div>
    <div class="main-menu">
        <div class="menu-inner">
            <nav>
                <ul class="metismenu" id="menu">

                    <!-- Menú Atracciones -->
                    <li class="<?php echo $atraccionesActive; ?>">
                        <a href="javascript:void(0)" aria-expanded="true">
                            <i class="fa fa-cutlery"></i>
                            <span>Atracciones</span>
                        </a>
                        <ul class="<?php echo $atraccionesActive; ?>">
                            <li class="<?php echo ($activePage == 'add-atraccion') ? 'active' : '';?>">
                                <a href="/admin/pages/atracciones/add-atraccion.php">Nueva atracción</a>
                            </li>
                            <li class="<?php echo ($activePage == 'show-atraccions') ? 'active' : '';?>">
                                <a href="/admin/pages/atracciones/show-atraccions.php">Ver Atracciones</a>
                            </li>
                            <li class="<?php echo ($activePage == 'planning') ? 'active' : '';?>">
                                <a href="/admin/pages/atracciones/planning.php">Calendario</a>
                            </li>
                            <li class="<?php echo ($activePage == 'mapa') ? 'active' : '';?>">
                                <a href="/admin/pages/atracciones/mapa.php">Mapa</a>
                            </li>
                            <li class="<?php echo ($activePage == 'reset-seen-attractions') ? 'active' : '';?>">
                                <a href="/admin/pages/atracciones/reset-seen-attractions.php">
                                    Marcar atracciones:<br>No Visto
                                </a>
                            </li>
                            <li class="<?php echo ($activePage == 'reset-active-attractions') ? 'active' : '';?>">
                                <a href="/admin/pages/atracciones/reset-active-attractions.php">
                                    Marcar atracciones:<br>Activado
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Menú Platos -->
                    <li class="<?php echo $platosActive; ?>">
                        <a href="javascript:void(0)" aria-expanded="true">
                            <i class="fa fa-cutlery"></i>
                            <span>Platos</span>
                        </a>
                        <ul class="<?php echo $platosActive; ?>">
                            <li class="<?php echo ($activePage == 'add-food') ? 'active' : '';?>">
                                <a href="/admin/pages/comida/add-food.php">Nuevo plato</a>
                            </li>
                            <li class="<?php echo ($activePage == 'show-foods') ? 'active' : '';?>">
                                <a href="/admin/pages/comida/show-foods.php">Ver platos</a>
                            </li>
                            <li class="<?php echo ($activePage == 'change-food') ? 'active' : '';?>">
                                <a href="/admin/pages/comida/change-food.php">Cambiar estado</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Menú Utilidades -->
                    <li class="<?php echo $utilidadesActive; ?>">
                        <a href="javascript:void(0)" aria-expanded="true">
                            <i class="fa fa-wrench"></i>
                            <span>Utilidades</span>
                        </a>
                        <ul class="<?php echo $utilidadesActive; ?>">
                            <li class="<?php echo ($activePage == 'task') ? 'active' : '';?>">
                                <a href="/admin/pages/tools/task.php">Tareas</a>
                            </li>
                            <li class="<?php echo ($activePage == 'maleta') ? 'active' : '';?>">
                                <a href="/admin/pages/tools/maleta.php">Maleta</a>
                            </li>
                            <li class="<?php echo ($activePage == 'emojis') ? 'active' : '';?>">
                                <a href="/admin/pages/tools/emojis.php">Emojis</a>
                            </li>
                            <li class="<?php echo ($activePage == 'gastos') ? 'active' : '';?>">
                                <a href="/admin/pages/tools/gastos.php">Gastos</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Menú Ficheros -->
                    <li class="<?php echo $ficherosActive; ?>">
                        <a href="javascript:void(0)" aria-expanded="true">
                            <i class="fa fa-folder"></i>
                            <span>Ficheros</span>
                        </a>
                        <ul class="<?php echo $ficherosActive; ?>">
                            <li class="<?php echo ($activePage == 'show-imgs') ? 'active' : '';?>">
                                <a href="/admin/pages/files/show-imgs.php">Imagenes</a>
                            </li>
                            <li class="<?php echo ($activePage == 'upload-imgs') ? 'active' : '';?>">
                                <a href="/admin/pages/files/upload-imgs.php">Subir</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Menú Base de datos -->
                    <li class="<?php echo $bbddActive; ?>">
                        <a href="javascript:void(0)" aria-expanded="true">
                            <i class="fa fa-database"></i>
                            <span>Base de datos</span>
                        </a>
                        <ul class="<?php echo $bbddActive; ?>">
                            <li class="<?php echo ($activePage == 'export-json') ? 'active' : '';?>">
                                <a href="/admin/pages/bbdd/export-json.php">Export JSON</a>
                            </li>
                            <li class="<?php echo ($activePage == 'export-sql') ? 'active' : '';?>">
                                <a href="/admin/pages/bbdd/export-sql.php">Export SQL</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Menú Administración -->
                    <li class="<?php echo $administracionActive; ?>">
                        <a href="javascript:void(0)" aria-expanded="true">
                            <i class="fa fa-cogs"></i>
                            <span>Administración</span>
                        </a>
                        <ul class="<?php echo $administracionActive; ?>">
                            <li class="<?php echo ($activePage == 'show-users') ? 'active' : '';?>">
                                <a href="/admin/pages/adm/show-users.php">Usuarios</a>
                            </li>
                        </ul>
                        <ul class="<?php echo $administracionActive; ?>">
                            <li class="<?php echo ($activePage == 'webconfig') ? 'active' : '';?>">
                                <a href="/admin/pages/adm/webconfig.php">Configuración</a>
                            </li>
                        </ul>
                        <ul class="<?php echo $administracionActive; ?>">
                            <li class="<?php echo ($activePage == 'activity-log') ? 'active' : '';?>">
                                <a href="/admin/pages/adm/activity-log.php">Log de Actividad</a>
                            </li>
                        </ul>
                        <ul class="<?php echo $administracionActive; ?>">
                            <li class="<?php echo ($activePage == 'sessions') ? 'active' : '';?>">
                                <a href="/admin/pages/adm/sessions.php">Sesiones Activas</a>
                            </li>
                        </ul>
                        <ul class="<?php echo $administracionActive; ?>">
                            <li class="<?php echo ($activePage == 'viajes') ? 'active' : '';?>">
                                <a href="/admin/pages/adm/viajes.php">Gestionar Viajes</a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </nav>
        </div>
    </div>
</div>
<!-- sidebar menu area end -->
