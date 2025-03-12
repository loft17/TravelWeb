<?php
session_start();

include('../../../config.php');  // Subir dos niveles de directorio para acceder a config.php

// Verificar si el formulario se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Verificar si el token CSRF es válido
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        // Si no es válido, denegar la solicitud
        die("Error de validación CSRF. Solicitud no válida.");
    }

    // Recoger los datos del formulario
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Conectar a la base de datos
    $conn = conectar_bd();  // Función definida en db.php para la conexión

    // Preparar la consulta para buscar al usuario en la base de datos
    $stmt = $conn->prepare("SELECT id, name, password, rol FROM users WHERE email = ? AND active = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Si el usuario existe, procedemos a verificar la contraseña
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password, $rol);
        $stmt->fetch();

        // Verificar la contraseña con password_verify()
        if (password_verify($password, $hashed_password)) {

            // Verificar que el usuario tenga el rol "admin"
            if ($rol !== 'admin') {
                // Si el rol no es admin, redirigir a la página de login con un error
                $_SESSION['login_error'] = "No tienes permisos de administrador.";
                header("Location: ../../login.php");
                exit();
            }

            // Regenerar el ID de sesión para prevenir ataques de fijación de sesión
            session_regenerate_id(true);  // Genera un nuevo ID de sesión y destruye el viejo

            // Actualizar last_conection y ip_conection
            $last_conection = date('Y-m-d H:i:s');  // Fecha y hora actual
            $ip_conection = $_SERVER['REMOTE_ADDR']; // IP del cliente

            // Actualizamos la información de la conexión
            $update_stmt = $conn->prepare("UPDATE users SET last_conection = ?, ip_conection = ? WHERE id = ?");
            $update_stmt->bind_param("ssi", $last_conection, $ip_conection, $id);
            $update_stmt->execute();
            $update_stmt->close();

            // Establecer variables de sesión
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_role'] = $rol;

            // Redirigir al dashboard de admin
            header("Location: ../../dashboard.php");
            exit();

        } else {
            // Contraseña incorrecta
            $_SESSION['login_error'] = "Contraseña incorrecta.";
            header("Location: ../../login.php");
            exit();
        }
    } else {
        // Usuario no encontrado
        $_SESSION['login_error'] = "Usuario no encontrado.";
        header("Location: ../../login.php");
        exit();
    }

    // Cerrar la sentencia
    $stmt->close();
    
    // Eliminar el token CSRF después de su uso
    unset($_SESSION['csrf_token']);
}
?>
