<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = conectar_bd();

function limpiar_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

$viaje_id = (int)($_SESSION['viaje_id'] ?? 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();

    if (isset($_POST['agregar'])) {
        $nombre    = limpiar_input($_POST['nombre']);
        $categoria = limpiar_input($_POST['categoria']);
        $cantidad  = filter_var($_POST['cantidad'], FILTER_VALIDATE_INT);
        $importante = isset($_POST['importante']) ? 1 : 0;

        if ($nombre && $categoria && $cantidad !== false) {
            $stmt = $conn->prepare("INSERT INTO maleta (nombre, categoria, cantidad, importante, viaje_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiii", $nombre, $categoria, $cantidad, $importante, $viaje_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    if (isset($_POST['editar'])) {
        $id        = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        $nombre    = limpiar_input($_POST['nombre']);
        $categoria = limpiar_input($_POST['categoria']);
        $cantidad  = filter_var($_POST['cantidad'], FILTER_VALIDATE_INT);
        $importante = isset($_POST['importante']) ? 1 : 0;

        if ($id !== false && $nombre && $categoria && $cantidad !== false) {
            $stmt = $conn->prepare("UPDATE maleta SET nombre = ?, categoria = ?, cantidad = ?, importante = ? WHERE id = ? AND viaje_id = ?");
            $stmt->bind_param("ssiiii", $nombre, $categoria, $cantidad, $importante, $id, $viaje_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    if (isset($_POST['delete_id'])) {
        $id = filter_var($_POST['delete_id'], FILTER_VALIDATE_INT);
        if ($id !== false) {
            $stmt = $conn->prepare("DELETE FROM maleta WHERE id = ? AND viaje_id = ?");
            $stmt->bind_param("ii", $id, $viaje_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

$stmt_m = $conn->prepare("SELECT * FROM maleta WHERE viaje_id = ? ORDER BY categoria ASC, nombre ASC");
$stmt_m->bind_param("i", $viaje_id);
$stmt_m->execute();
$articulos = $stmt_m->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_m->close();

$categorias = ["Ropa", "Neceser", "Botiquin", "Electronica", "Documentacion", "Varios"];
$articulos_por_categoria = [];
foreach ($categorias as $categoria) {
    $articulos_por_categoria[$categoria] = array_filter($articulos, function ($articulo) use ($categoria) {
        return $articulo['categoria'] === $categoria;
    });
}

$conn->close();
?>
