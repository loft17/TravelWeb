<?php
// Auto-selects the public viaje based on today's date.
// Priority: active trip (today in range) → next upcoming → most recent past → constant.
if (!isset($viaje_id)) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
    if (!isset($conn) || !$conn instanceof mysqli) {
        $conn = conectar_bd();
    }
    $today = date('Y-m-d');

    $_r = $conn->query(
        "SELECT id FROM viajes WHERE activo = 1
         AND fecha_inicio <= '$today' AND fecha_fin >= '$today'
         ORDER BY fecha_inicio DESC LIMIT 1"
    );
    if ($_r && $_row = $_r->fetch_assoc()) {
        $viaje_id = (int)$_row['id'];
    } else {
        $_r = $conn->query(
            "SELECT id FROM viajes WHERE activo = 1 AND fecha_inicio > '$today'
             ORDER BY fecha_inicio ASC LIMIT 1"
        );
        if ($_r && $_row = $_r->fetch_assoc()) {
            $viaje_id = (int)$_row['id'];
        } else {
            $_r = $conn->query(
                "SELECT id FROM viajes WHERE activo = 1
                 ORDER BY fecha_fin DESC LIMIT 1"
            );
            $viaje_id = ($_r && $_row = $_r->fetch_assoc()) ? (int)$_row['id'] : 1;
        }
    }
    unset($_r, $_row, $today);
}
