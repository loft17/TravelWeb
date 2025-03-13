
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
?>

<!-- sidebar menu area start -->
<div class="sidebar-menu">
    <div class="sidebar-header">
        <div class="logo">
            <a href="index.html"><img src="/admin/assets/images/icon/logo.png" alt="logo"></a>
        </div>
    </div>
    <div class="main-menu">
        <div class="menu-inner">
            <nav>
                <ul class="metismenu" id="menu">
                    <li class="active">
                        <a href="javascript:void(0)" aria-expanded="true"><i class="ti-dashboard"></i><span>dashboard</span></a>
                        <ul class="collapse">
                            <li class="active"><a href="index.html">ICO dashboard</a></li>
                            <li><a href="index2.html">Ecommerce dashboard</a></li>
                            <li><a href="index3.html">SEO dashboard</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-cutlery"></i>
                            <span>Platos</span></a>
                        <ul class="collapse">
                        <li><a href="/admin/platos/new_plato.php">Nuevo plato</a></li>
                            <li><a href="/admin/platos/show_platos.php">Ver platos</a></li>
                            <li><a href="/admin/platos/xxx.php">Cambiar estado</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-wrench"></i>
                            <span>Utilidades</span></a>
                        <ul class="collapse">
                            <li><a href="/admin/utils/task.php">Tareas</a></li>
                            <li><a href="/admin/utils/maleta.php">Maleta</a></li>
                            <li><a href="/admin/utils/emojis.php">Emojis</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-folder"></i>
                            <span>Ficheros</span></a>
                        <ul class="collapse">
                            <li><a href="/admin/files/show_imgs.php">Imagenes</a></li>
                            <li><a href="/admin/files/upload_imgs.php">Subir</a></li>
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