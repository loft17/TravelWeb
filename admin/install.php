<?php
require_once __DIR__ . '/../config.php';

if (file_exists(__DIR__ . '/.installed')) {
    header("Location: /admin/login.php");
    exit();
}

// ── Comprobaciones previas ────────────────────────────────────────────────────
$php_maj = PHP_MAJOR_VERSION;
$php_min = PHP_MINOR_VERSION;
$php_ver = PHP_VERSION;

// Intenta conexión a BD sin seleccionar base de datos
$db_ok  = false;
$db_msg = '';
$_test  = @new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($_test->connect_errno) {
    $db_msg = $_test->connect_error;
} else {
    $db_ok = true;
    $_test->close();
}

$upload_dir = realpath(__DIR__ . '/../uploads') ?: (__DIR__ . '/../uploads');
$upload_ok  = is_dir($upload_dir) && is_writable($upload_dir);

$checks = [
    [
        'label'    => 'PHP &ge; 8.0',
        'ok'       => version_compare($php_ver, '8.0.0', '>='),
        'required' => true,
        'detail'   => 'Versión detectada: <code>' . htmlspecialchars($php_ver) . '</code>',
        'fix'      => 'apt install php8.4',
    ],
    [
        'label'    => 'Extensión <code>mysqli</code>',
        'ok'       => extension_loaded('mysqli'),
        'required' => true,
        'detail'   => 'Necesaria para conectar con MySQL / MariaDB',
        'fix'      => "apt install php{$php_maj}.{$php_min}-mysqli",
    ],
    [
        'label'    => 'Extensión <code>mbstring</code>',
        'ok'       => extension_loaded('mbstring'),
        'required' => true,
        'detail'   => 'Necesaria para manejo de texto UTF-8',
        'fix'      => "apt install php{$php_maj}.{$php_min}-mbstring",
    ],
    [
        'label'    => 'Extensión <code>json</code>',
        'ok'       => extension_loaded('json'),
        'required' => true,
        'detail'   => 'Necesaria para escalas y datos de transporte',
        'fix'      => "apt install php{$php_maj}.{$php_min}-json",
    ],
    [
        'label'    => 'Extensión <code>session</code>',
        'ok'       => function_exists('session_start'),
        'required' => true,
        'detail'   => 'Necesaria para autenticación y CSRF',
        'fix'      => "apt install php{$php_maj}.{$php_min}-common",
    ],
    [
        'label'    => 'Extensión <code>gd</code>',
        'ok'       => extension_loaded('gd'),
        'required' => false,
        'detail'   => 'Recomendada: permite redimensionar y convertir imágenes subidas',
        'fix'      => "apt install php{$php_maj}.{$php_min}-gd",
    ],
    [
        'label'    => 'Conexión a base de datos',
        'ok'       => $db_ok,
        'required' => true,
        'detail'   => $db_ok
            ? 'Host: <code>' . htmlspecialchars(DB_HOST) . '</code> &nbsp;·&nbsp; BD: <code>' . htmlspecialchars(DB_NAME) . '</code>'
            : '<span style="color:#c0392b">' . htmlspecialchars($db_msg) . '</span>',
        'fix'      => 'Edita <code>config.php</code> y verifica DB_HOST, DB_USER, DB_PASS',
    ],
    [
        'label'    => 'Directorio <code>uploads/</code> escribible',
        'ok'       => $upload_ok,
        'required' => false,
        'detail'   => '<code>' . htmlspecialchars($upload_dir) . '</code>',
        'fix'      => "chown -R www-data:www-data {$upload_dir}",
    ],
];

$required_ok  = array_reduce($checks, fn($c, $ch) => $c && (!$ch['required'] || $ch['ok']), true);
$has_warnings = array_reduce($checks, fn($c, $ch) => $c || (!$ch['required'] && !$ch['ok']), false);
$all_ok       = array_reduce($checks, fn($c, $ch) => $c && $ch['ok'], true);
// ─────────────────────────────────────────────────────────────────────────────

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && $required_ok) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    if ($conn->connect_error) {
        die("❌ Error de conexión: " . $conn->connect_error);
    }

    $conn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->select_db(DB_NAME);
    if ($conn->error) {
        die("❌ Error seleccionando la base de datos: " . $conn->error);
    }

    $sql = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        image_profile VARCHAR(255) DEFAULT NULL,
        date_reg TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        active BOOLEAN DEFAULT TRUE,
        rol ENUM('admin','usuario','moderador') NOT NULL DEFAULT 'usuario',
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
        id INT(11) NOT NULL AUTO_INCREMENT,
        nombre VARCHAR(100) NOT NULL,
        categoria VARCHAR(50) DEFAULT NULL,
        cantidad INT(11) DEFAULT 1,
        peso DECIMAL(5,2) DEFAULT 0.00,
        importante TINYINT(1) DEFAULT 0,
        fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        viaje_id INT NOT NULL DEFAULT 1,
        PRIMARY KEY (id)
    );
    CREATE TABLE IF NOT EXISTS tareas (
        id INT(11) NOT NULL AUTO_INCREMENT,
        titulo VARCHAR(255) DEFAULT NULL,
        fecha_inicio DATE DEFAULT NULL,
        fecha_fin DATE DEFAULT NULL,
        completado TINYINT(1) DEFAULT 0,
        info TEXT DEFAULT NULL,
        url VARCHAR(255) DEFAULT NULL,
        fecha_creada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        fecha_actualizada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        fecha_terminada TIMESTAMP DEFAULT NULL,
        viaje_id INT NOT NULL DEFAULT 1,
        PRIMARY KEY (id)
    );
    CREATE TABLE IF NOT EXISTS calendar_events (
        id INT(11) NOT NULL AUTO_INCREMENT,
        fecha DATE NOT NULL,
        ciudad VARCHAR(100) DEFAULT NULL,
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
        escalas      TEXT         DEFAULT NULL,
        fecha_llegada DATE        DEFAULT NULL,
        duracion     VARCHAR(50)  DEFAULT NULL,
        ciudad_origen VARCHAR(100) DEFAULT NULL,
        aeropuerto_origen VARCHAR(150) DEFAULT NULL,
        ciudad_destino VARCHAR(100) DEFAULT NULL,
        aeropuerto_destino VARCHAR(150) DEFAULT NULL,
        aerolinea_id INT          DEFAULT NULL,
        compania     VARCHAR(150) DEFAULT NULL,
        viaje_id     INT          NOT NULL DEFAULT 1,
        created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    CREATE TABLE IF NOT EXISTS aerolineas (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        nombre     VARCHAR(100) NOT NULL,
        codigo     VARCHAR(10)  DEFAULT NULL,
        icono      VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    if (!$conn->multi_query($sql)) {
        die("❌ Error creando tablas: " . $conn->error);
    }
    while ($conn->more_results() && $conn->next_result()) {;}

    $result = $conn->query("SELECT config_value FROM configurations WHERE config_key = 'installed'");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['config_value'] == '1') {
            header("Location: /admin/login.php");
            exit();
        }
    }

    $email    = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $title_web = $conn->real_escape_string($_POST['title_web']);

    $user_check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($user_check && $user_check->num_rows == 0) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $conn->query("INSERT INTO users (name,email,password,rol) VALUES ('Admin','$email','$hashed','admin')");
    } else {
        $mensaje = "<div class='message error'>⚠️ El usuario ya existe.</div>";
    }

    $conn->query("INSERT INTO viajes (nombre,destino,fecha_inicio,fecha_fin)
                  SELECT 'Mi Viaje','',CURDATE(),DATE_ADD(CURDATE(),INTERVAL 7 DAY)
                  FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM viajes LIMIT 1)");

    $conn->query("INSERT INTO configurations (config_key,config_value) VALUES ('installed','1') ON DUPLICATE KEY UPDATE config_value='1'");
    $conn->query("INSERT INTO configurations (config_key,config_value) VALUES ('title_web','$title_web') ON DUPLICATE KEY UPDATE config_value='$title_web'");
    $footer = "copyright &copy; " . date('Y') . " - developed by <b>joseromera.net</b>";
    $conn->query("INSERT INTO configurations (config_key,config_value) VALUES ('footer_text','$footer') ON DUPLICATE KEY UPDATE config_value='$footer'");

    file_put_contents(__DIR__ . '/.installed', date('Y-m-d H:i:s'));
    $conn->close();

    $mensaje = "<div class='message success'>🎉 Instalación completada. <a href='/admin/login.php'>Ir al Panel</a></div>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación – TravelGuide</title>
    <link href="assets/css/install.css" rel="stylesheet">
</head>
<body>
<div class="container<?= $required_ok ? '' : ' container--wide' ?>">

    <h2>🚀 Instalación</h2>
    <p>Comprobando requisitos del servidor…</p>

    <!-- Tabla de comprobaciones -->
    <div class="checks">
        <?php foreach ($checks as $c): ?>
        <div class="check-row <?= $c['ok'] ? 'check-ok' : ($c['required'] ? 'check-fail' : 'check-warn') ?>">
            <span class="check-icon"><?= $c['ok'] ? '✓' : ($c['required'] ? '✗' : '⚠') ?></span>
            <div class="check-body">
                <span class="check-label"><?= $c['label'] ?></span>
                <?php if ($c['detail']): ?>
                    <span class="check-detail"><?= $c['detail'] ?></span>
                <?php endif; ?>
                <?php if (!$c['ok']): ?>
                    <span class="check-fix"><code><?= htmlspecialchars($c['fix']) ?></code></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (!$required_ok): ?>
        <div class="message error" style="margin-top:1.25rem">
            ✗ Hay requisitos obligatorios sin cumplir. Instálalos y recarga esta página.
        </div>
    <?php elseif ($has_warnings): ?>
        <div class="message warn" style="margin-top:1.25rem">
            ⚠ Algunos requisitos opcionales no están disponibles. Puedes continuar la instalación.
        </div>
    <?php else: ?>
        <div class="message success" style="margin-top:1.25rem">
            ✓ Todos los requisitos cumplidos.
        </div>
    <?php endif; ?>

    <?php if ($required_ok): ?>
        <hr style="border:none;border-top:1px solid #e8eaf0;margin:1.75rem 0 1.5rem">
        <p style="margin-bottom:0">Completa los datos para crear el administrador y configurar el sitio.</p>

        <?php if (!empty($mensaje)) echo $mensaje; ?>

        <form method="post">
            <label>Correo electrónico (Admin)</label>
            <input type="email" name="email" required>

            <label>Contraseña</label>
            <input type="password" name="password" required>

            <label>Título del sitio</label>
            <input type="text" name="title_web" required placeholder="Mi Guía de Viaje">

            <button type="submit">Instalar</button>
        </form>
    <?php endif; ?>

</div>
</body>
</html>
