<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

function log_activity(string $action, string $detail = ''): void
{
    $conn     = conectar_bd();
    $userId   = $_SESSION['user_id']   ?? null;
    $userName = $_SESSION['user_name'] ?? 'Sistema';
    $ip       = $_SERVER['REMOTE_ADDR'] ?? '';

    $stmt = $conn->prepare(
        "INSERT INTO activity_log (user_id, user_name, action, detail, ip) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('issss', $userId, $userName, $action, $detail, $ip);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
