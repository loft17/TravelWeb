<?php
require_once __DIR__ . '/../config.php';

// Bloquear acceso si la instalación ya está completada
if (file_exists(__DIR__ . '/.installed')) {
    header("Location: /admin/login.php");
    exit();
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

// Crear la base de datos si no existe
$conn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

// Seleccionar la base de datos
$conn->select_db(DB_NAME);
if ($conn->error) {
    die("❌ Error seleccionando la base de datos: " . $conn->error);
}

// **Crear las tablas antes de verificar instalación**
$sql = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,                  
    name VARCHAR(100) NOT NULL,                         
    email VARCHAR(100) UNIQUE NOT NULL,                 
    password VARCHAR(255) NOT NULL,
    image_profile VARCHAR(255) DEFAULT NULL,
    date_reg TIMESTAMP DEFAULT CURRENT_TIMESTAMP,       
    active BOOLEAN DEFAULT TRUE,                        
    rol ENUM('admin', 'usuario', 'moderador') NOT NULL DEFAULT 'usuario',
    last_conection DATETIME DEFAULT NULL,
    ip_conection VARCHAR(45) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS configurations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) UNIQUE NOT NULL,
    config_value TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS viajes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(255) NOT NULL,
    destino     VARCHAR(255) DEFAULT '',
    fecha_inicio DATE DEFAULT NULL,
    fecha_fin    DATE DEFAULT NULL,
    activo      TINYINT(1) NOT NULL DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS atracciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ciudad VARCHAR(255),
    orden INT NOT NULL,
    fecha DATE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    imagen_url VARCHAR(255),
    mapa_url VARCHAR(255),
    wikipedia_url VARCHAR(255),
    instagram_url_1 VARCHAR(255),
    instagram_url_2 VARCHAR(255),
    instagram_url_3 VARCHAR(255),
    visto BOOLEAN DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE,
    lat DECIMAL(10,8) DEFAULT NULL,
    lng DECIMAL(11,8) DEFAULT NULL,
    viaje_id INT NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS comida (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    puntuacion TINYINT(4) NULL,
    imagen_url VARCHAR(255) NULL,
    comido TINYINT(1) NULL DEFAULT 0,
    viaje_id INT NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS maleta (
    id int(11) NOT NULL AUTO_INCREMENT,
    nombre varchar(100) NOT NULL,
    categoria varchar(50) DEFAULT NULL,
    cantidad int(11) DEFAULT 1,
    peso decimal(5,2) DEFAULT 0.00,
    importante tinyint(1) DEFAULT 0,
    fecha_agregado timestamp DEFAULT CURRENT_TIMESTAMP,
    viaje_id INT NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS tareas (
    id int(11) NOT NULL AUTO_INCREMENT,
    titulo varchar(255) DEFAULT NULL,
    fecha_inicio date DEFAULT NULL,
    fecha_fin date DEFAULT NULL,
    completado tinyint(1) DEFAULT 0,
    info text DEFAULT NULL,
    url varchar(255) DEFAULT NULL,
    fecha_creada timestamp DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizada timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fecha_terminada timestamp DEFAULT NULL,
    viaje_id INT NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS calendar_events (
    id int(11) NOT NULL AUTO_INCREMENT,
    fecha date NOT NULL,
    ciudad varchar(100) DEFAULT NULL,
    visita_manana TEXT,
    visita_tarde TEXT,
    visita_noche TEXT,
    viaje_id INT NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    user_name VARCHAR(100) DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    detail TEXT DEFAULT NULL,
    ip VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS gastos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    importe DECIMAL(10,2) NOT NULL,
    divisa VARCHAR(10) NOT NULL DEFAULT 'EUR',
    fecha DATE NOT NULL,
    viaje_id INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(64) NOT NULL,
    ip VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_token (session_token)
);

CREATE TABLE IF NOT EXISTS transportes (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    tipo         VARCHAR(50)  NOT NULL DEFAULT 'avion',
    origen       VARCHAR(255) NOT NULL,
    destino      VARCHAR(255) NOT NULL,
    fecha        DATE         NOT NULL,
    hora_salida  TIME         DEFAULT NULL,
    hora_llegada TIME         DEFAULT NULL,
    numero       VARCHAR(100) DEFAULT NULL,
    notas        TEXT         DEFAULT NULL,
    viaje_id     INT          NOT NULL DEFAULT 1,
    created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if (!$conn->multi_query($sql)) {
    die("❌ Error creando tablas: " . $conn->error);
}

// Limpiar resultados de multi_query
while ($conn->more_results() && $conn->next_result()) {;}

// **Verificar si la instalación ya fue realizada**
$check_install_query = "SELECT config_value FROM configurations WHERE config_key = 'installed'";
$result = $conn->query($check_install_query);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['config_value'] == '1') {
        header("Location: ../admin/login.php");
        exit();
    }
}

// Variable para mensajes
$mensaje = "";

// **Ejecutar instalación solo si el usuario presiona "Instalar"**
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password']; // Recibimos la contraseña sin procesar
    $title_web = $conn->real_escape_string($_POST['title_web']);

    // Verificar si el usuario ya existe
    $user_check_query = "SELECT id FROM users WHERE email = '$email'";
    $user_check = $conn->query($user_check_query);

    if ($user_check && $user_check->num_rows == 0) {
        // Crear el hash de la contraseña usando password_hash()
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insertar usuario administrador con la contraseña hasheada
        $conn->query("INSERT INTO users (name, email, password, rol) VALUES ('Admin', '$email', '$hashed_password', 'admin')");
    } else {
        $mensaje = "<div class='message error'>⚠️ El usuario ya existe.</div>";
    }

    // Crear viaje por defecto si no existe (con fechas para que la selección automática funcione)
    $conn->query("INSERT INTO viajes (nombre, destino, fecha_inicio, fecha_fin) SELECT 'Mi Viaje', '', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY) FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM viajes LIMIT 1)");

    // Insertar configuración inicial
    $conn->query("INSERT INTO configurations (config_key, config_value) VALUES ('installed', '1') ON DUPLICATE KEY UPDATE config_value = '1'");
    $conn->query("INSERT INTO configurations (config_key, config_value) VALUES ('title_web', '$title_web') ON DUPLICATE KEY UPDATE config_value = '$title_web'");
    $footer_text = "copyright &copy; " . date('Y') . " - developed by <b>joseromera.net</b>";
    $conn->query("INSERT INTO `configurations` (`config_key`, `config_value`) VALUES ('footer_text', '$footer_text') ON DUPLICATE KEY UPDATE config_value = '$footer_text'");

    // Crear lock file para bloquear futuros accesos a este script
    file_put_contents(__DIR__ . '/.installed', date('Y-m-d H:i:s'));

    $mensaje = "<div class='message success'>🎉 Instalación completada. <a href='/admin/login.php'>Ir al Panel</a></div>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación</title>
    <link href="assets/css/install.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <h2>🚀 Instalación</h2>
    <p>Completa los datos para configurar tu sitio.</p>

    <!-- Mostrar mensaje en la parte superior -->
    <?php if (!empty($mensaje)) echo $mensaje; ?>

    <form method="post">
        <label for="email">Correo Electrónico (Admin)</label>
        <input type="email" name="email" required>
        
        <label for="password">Contraseña</label>
        <input type="password" name="password" required>

        <label for="title_web">Título del Sitio</label>
        <input type="text" name="title_web" required>

        <button type="submit">Instalar</button>
    </form>
</div>

</body>
</html>