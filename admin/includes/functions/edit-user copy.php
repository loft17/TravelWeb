<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once '../../../config.php';  // Asegúrate de incluir la configuración de la base de datos

// Verificar que el parámetro 'id' está presente en la URL
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Conectar a la base de datos
    $conn = conectar_bd();

    // Consultar los datos del usuario con el ID proporcionado
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si el usuario existe
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "Usuario no encontrado.";
        exit();
    }
} else {
    echo "ID de usuario no especificado.";
    exit();
}

// Procesar el formulario cuando se envíe
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger los valores del formulario
    $name = $_POST['name'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];
    $active = isset($_POST['active']) ? 1 : 0;  // Verificar si 'active' está marcado
    $password = $_POST['password'];  // Nueva contraseña

    // Si el campo de la contraseña no está vacío, lo actualizamos
    if (!empty($password)) {
        // Cifrar la contraseña
        $password = password_hash($password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET name = ?, email = ?, rol = ?, active = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssssi", $name, $email, $rol, $active, $password, $user_id);
    } else {
        // Si no se proporciona una nueva contraseña, solo actualizamos los otros campos
        $update_query = "UPDATE users SET name = ?, email = ?, rol = ?, active = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssii", $name, $email, $rol, $active, $user_id);
    }

    // Ejecutar la consulta
    if ($stmt->execute()) {
        header("Location: ../../adm/show_users.php?message=Usuario%20actualizado%20correctamente.");
        exit();
    } else {
        echo "Error al actualizar el usuario.";
    }
}
?>
