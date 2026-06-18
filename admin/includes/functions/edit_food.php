<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = conectar_bd();

// -----------------------------
// Subida asíncrona de imagen (para llamadas AJAX)
// -----------------------------
if (isset($_GET['action']) && $_GET['action'] === 'upload_image') {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/helpers.php';
    header('Content-Type: application/json');
    echo json_encode(uploadImageFile('imagen_file'));
    exit;
}

// -----------------------------
// Lógica para editar el registro
// -----------------------------
if (!isset($_GET['id'])) {
    echo "ID no especificado.";
    exit;
}
$id = intval($_GET['id']);

// Procesar el formulario al enviarse (método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion']; // HTML generado por Quill
    $puntuacion = isset($_POST['puntuacion']) ? intval($_POST['puntuacion']) : 0;
    // Limitar la puntuación entre 0 y 5
    if ($puntuacion < 0) $puntuacion = 0;
    if ($puntuacion > 5) $puntuacion = 5;
    
    // Se utiliza la URL que venga en el campo, que puede ser actual o la actualizada por AJAX
    $imagen_url = $_POST['imagen_url'];
    $comido = isset($_POST['comido']) ? 1 : 0;
    
    $stmt = $conn->prepare("UPDATE comida SET nombre = ?, descripcion = ?, puntuacion = ?, imagen_url = ?, comido = ? WHERE id = ?");
    $stmt->bind_param("ssisii", $nombre, $descripcion, $puntuacion, $imagen_url, $comido, $id);
    
    if ($stmt->execute()) {
        header("Location: show-foods.php");
        exit;
    } else {
        $error = "Error al actualizar el registro: " . $stmt->error;
    }
}

// Consultar el registro a editar
$stmt = $conn->prepare("SELECT * FROM comida WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Registro no encontrado.";
    exit;
}

$comida = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
