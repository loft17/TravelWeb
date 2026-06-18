<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/viajes.php';
$_viajeActivo = get_viaje_activo();
$_userName    = $_SESSION['user_name'] ?? 'Admin';
$_initials    = strtoupper(substr($_userName, 0, 2));
?>

<div class="page-title-area">
    <div class="row align-items-center" style="width:100%;margin:0;">

        <!-- Izquierda: nombre del viaje activo -->
        <div class="col-sm-6" style="padding:0;">
            <div style="display:flex;align-items:center;gap:10px;">
                <h4 class="page-title" style="margin:0;">
                    <?= htmlspecialchars($_viajeActivo['nombre']) ?>
                    <?php if (!empty($_viajeActivo['destino'])): ?>
                        <small style="font-weight:400;color:var(--muted-fg);font-size:13px;">
                            — <?= htmlspecialchars($_viajeActivo['destino']) ?>
                        </small>
                    <?php endif; ?>
                </h4>
            </div>
        </div>

        <!-- Derecha: usuario -->
        <div class="col-sm-6" style="padding:0;">
            <div class="user-profile pull-right" style="position:relative;">

                <!-- Botón avatar + nombre -->
                <div class="dropdown">
                    <button class="dropdown-toggle" data-toggle="dropdown"
                            style="display:flex;align-items:center;gap:8px;background:transparent;border:none;padding:4px 8px;border-radius:var(--radius);cursor:pointer;transition:background .15s;"
                            onmouseover="this.style.background='var(--muted)'"
                            onmouseout="this.style.background='transparent'">

                        <!-- Avatar con iniciales -->
                        <span style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;background:var(--primary);color:var(--primary-fg);font-size:12px;font-weight:600;flex-shrink:0;font-family:'Inter',sans-serif;">
                            <?= htmlspecialchars($_initials) ?>
                        </span>

                        <span style="font-size:13px;font-weight:500;color:var(--fg);">
                            <?= htmlspecialchars($_userName) ?>
                        </span>

                        <i class="fa fa-chevron-down" style="font-size:10px;color:var(--muted-fg);"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right"
                         style="min-width:180px;padding:4px;border:1px solid var(--border);border-radius:var(--radius);box-shadow:0 4px 20px rgba(0,0,0,.1);background:var(--card);">

                        <div style="padding:10px 12px 8px;border-bottom:1px solid var(--border);margin-bottom:4px;">
                            <div style="font-size:13px;font-weight:600;color:var(--fg);"><?= htmlspecialchars($_userName) ?></div>
                        </div>

                        <a class="dropdown-item" href="/admin/pages/adm/viajes.php"
                           style="font-size:13px;color:var(--fg);padding:7px 12px;border-radius:4px;display:block;text-decoration:none;">
                            ✈ Gestionar viajes
                        </a>
                        <a class="dropdown-item" href="/admin/pages/adm/webconfig.php"
                           style="font-size:13px;color:var(--fg);padding:7px 12px;border-radius:4px;display:block;text-decoration:none;">
                            ⚙ Configuración
                        </a>

                        <div style="border-top:1px solid var(--border);margin:4px 0;"></div>

                        <a class="dropdown-item" href="/admin/logout.php"
                           style="font-size:13px;color:var(--destructive);padding:7px 12px;border-radius:4px;display:block;text-decoration:none;">
                            Cerrar sesión
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
