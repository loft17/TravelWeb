<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';

// Obtener el nombre del archivo actual sin extensión
$activePage = basename($_SERVER['SCRIPT_FILENAME'], '.php');

// Definir los arrays de páginas para cada grupo del menú
$atraccionesPages = ['add-atraccion', 'show-atraccion', 'reset-seen-attractions', 'reset-active-attractions'];
$platosPages      = ['add-food', 'show-foods', 'change-food'];
$utilidadesPages  = ['task', 'maleta', 'emojis'];
$ficherosPages    = ['show-imgs', 'upload-imgs'];
$bbddPages        = ['export-json', 'export-sql'];
$administracionPages = ['show-users', 'webconfig'];

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
                            <li class="<?php echo ($activePage == 'show-atraccion') ? 'active' : '';?>">
                                <a href="/admin/pages/atracciones/show-atraccion.php">Ver Atracciones</a>
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
                            <li class="<?php echo ($activePage == 'show-users') ? 'active' : '';?>">
                                <a href="/admin/pages/adm/webconfig.php">Configuración</a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </nav>
        </div>
    </div>
</div>
<!-- sidebar menu area end -->
