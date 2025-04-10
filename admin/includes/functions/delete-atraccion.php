<?php
// Incluir la configuración de la base de datos
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once '../../../config.php';


// Comprobar si el parámetro 'id' está presente en la URL
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Conectar a la base de datos
    $conn = conectar_bd();

    // Consultar para asegurarse de que el usuario existe antes de eliminarlo
    $query = "SELECT * FROM atracciones WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Eliminar el usuario
        $delete_query = "DELETE FROM atracciones WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $user_id);
        
        if ($delete_stmt->execute()) {
            // Redirigir a la página de usuarios con un mensaje de éxito
            header("Location: /admin/pages/atracciones/show-atraccions.php?message=Atraccion eliminada correctamente.");
        } else {
            // Mostrar error si la eliminación falla
            echo "Error al eliminar la atraccion.";
        }
    } else {
        echo "Atraccion no encontrado.";
    }
} else {
    echo "ID de atraccion no especificado.";
}
?>


