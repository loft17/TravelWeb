<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';


// Conectar a la base de datos
$conn = conectar_bd();

// Función para limpiar entradas de usuario
function limpiar_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Agregar un artículo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $nombre = limpiar_input($_POST['nombre']);
    $categoria = limpiar_input($_POST['categoria']);
    $cantidad = filter_var($_POST['cantidad'], FILTER_VALIDATE_INT);
    $importante = isset($_POST['importante']) ? 1 : 0;

    if ($nombre && $categoria && $cantidad !== false) {
        $stmt = $conn->prepare("INSERT INTO maleta (nombre, categoria, cantidad, importante) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $nombre, $categoria, $cantidad, $importante);
        $stmt->execute();
        $stmt->close();
    }
}

// Modificar un artículo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar'])) {
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    $nombre = limpiar_input($_POST['nombre']);
    $categoria = limpiar_input($_POST['categoria']);
    $cantidad = filter_var($_POST['cantidad'], FILTER_VALIDATE_INT);
    $importante = isset($_POST['importante']) ? 1 : 0;

    if ($id !== false && $nombre && $categoria && $cantidad !== false) {
        $stmt = $conn->prepare("UPDATE maleta SET nombre = ?, categoria = ?, cantidad = ?, importante = ? WHERE id = ?");
        $stmt->bind_param("ssiii", $nombre, $categoria, $cantidad, $importante, $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Eliminar un artículo
if (isset($_GET['delete'])) {
    $id = filter_var($_GET['delete'], FILTER_VALIDATE_INT);

    if ($id !== false) {
        $stmt = $conn->prepare("DELETE FROM maleta WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Obtener artículos ordenados por categoría
$result = $conn->query("SELECT * FROM maleta ORDER BY categoria ASC, nombre ASC");
$articulos = $result->fetch_all(MYSQLI_ASSOC);
$result->close();

// Categorías predefinidas
$categorias = ["Ropa", "Neceser", "Botiquin", "Electronica", "Documentacion", "Varios"];
$articulos_por_categoria = [];
foreach ($categorias as $categoria) {
    $articulos_por_categoria[$categoria] = array_filter($articulos, function ($articulo) use ($categoria) {
        return $articulo['categoria'] === $categoria;
    });
}

$conn->close();
?>