
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
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
l>
                    </li>

                    <li class="active">
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-cutlery"></i>
                            <span>Atracciones</span></a>
                        <ul class="active">
                            <li class="active"><a href="/admin/pages/atracciones/add-atraccion.php">Nueva atraccion</a></li>
                            <li><a href="/admin/pages/atracciones/show-atraccion.php">Ver Atracciones</a></li>
                            <li><a href="/admin/pages/atracciones/reset-seen-attractions.php">Marcar atracciones:<br>No Visto</a></li>
                            <li><a href="/admin/pages/atracciones/reset-active-attractions.php">Marcar atracciones:<br>Activado</a></li>
                        </ul>
                    </li>
                    
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-cutlery"></i>
                            <span>Platos</span></a>
                        <ul class="collapse">
                            <li><a href="/admin/pages/comida/add-food.php">Nuevo plato</a></li>
                            <li><a href="/admin/pages/comida/show-foods.php">Ver platos</a></li>
                            <li><a href="/admin/pages/comida/change-food.php">Cambiar estado</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-wrench"></i>
                            <span>Utilidades</span></a>
                        <ul class="collapse">
                            <li><a href="/admin/pages/tools/task.php">Tareas</a></li>
                            <li><a href="/admin/pages/tools/maleta.php">Maleta</a></li>
                            <li><a href="/admin/pages/tools/emojis.php">Emojis</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-folder"></i>
                            <span>Ficheros</span></a>
                        <ul class="collapse">
                            <li><a href="/admin/pages/files/show-imgs.php">Imagenes</a></li>
                            <li><a href="/admin/pages/files/upload-imgs.php">Subir</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-database"></i>
                            <span>Base de datos</span></a>
                        <ul class="collapse">
                            <li><a href="/admin/pages/bbdd/export-json.php">Export JSON</a></li>
                            <li><a href="/admin/pages/bbdd/export-sql.php">Export SQL</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-cogs"></i>
                            <span>Administración</span></a>
                        <ul class="collapse">
                            <li><a href="/admin/pages/adm/show-users.php">Usuarios</a></li>
                        </ul>
                    </li>
                    
                </ul>
            </nav>
        </div>
    </div>
</div>
<!-- sidebar menu area end -->