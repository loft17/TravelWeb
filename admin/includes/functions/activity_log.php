<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

function log_activity(string $action, string $detail = ''): void
{
    $conn = conectar_bd();

    // Crear tabla si no existe (migración automática para installs existentes)
    $conn->query("CREATE TABLE IF NOT EXISTS activity_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT DEFAULT NULL,
        user_name VARCHAR(100) DEFAULT NULL,
        action VARCHAR(100) NOT NULL,
        detail TEXT DEFAULT NULL,
        ip VARCHAR(45) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $userId   = $_SESSION['user_id']   ?? null;
    $userName = $_SESSION['user_name'] ?? 'Sistema';
    $ip       = $_SERVER['REMOTE_ADDR'] ?? '';

    $stmt = $conn->prepare(
        "INSERT INTO activity_log (user_id, user_name, action, detail, ip) VALUES (?, ?, ?, ?, ?)"
    );
    if ($stmt) {
        $stmt->bind_param('issss', $userId, $userName, $action, $detail, $ip);
        $stmt->execute();
        $stmt->close();
    }
    $conn->close();
}
