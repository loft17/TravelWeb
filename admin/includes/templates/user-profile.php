<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/viajes.php';
$_viajeActivo = get_viaje_activo();
?>

<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">
                    ✈ <?= htmlspecialchars($_viajeActivo['nombre']) ?>
                    <?php if (!empty($_viajeActivo['destino'])): ?>
                        <small style="font-size:13px; font-weight:400; color:#999;"> — <?= htmlspecialchars($_viajeActivo['destino']) ?></small>
                    <?php endif; ?>
                </h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="/admin/dashboard.php">Inicio</a></li>
                    <li><a href="/admin/pages/adm/viajes.php">Cambiar viaje</a></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            <div class="user-profile pull-right">
                <img class="avatar user-thumb" src="/admin/assets/images/author/avatar.png" alt="avatar">
                <h4 class="user-name dropdown-toggle" data-toggle="dropdown">
                    <?= htmlspecialchars($_SESSION['user_name']) ?>
                    <i class="fa fa-angle-down"></i>
                </h4>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="/admin/logout.php">Log Out</a>
                </div>
            </div>
        </div>
    </div>
</div>
