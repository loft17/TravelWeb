<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

function _ensure_sessions_table(): void
{
    $conn = conectar_bd();
    $conn->query("CREATE TABLE IF NOT EXISTS user_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        session_token VARCHAR(64) NOT NULL,
        ip VARCHAR(45) DEFAULT NULL,
        user_agent VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uq_token (session_token)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $conn->close();
}

function register_session(int $userId): void
{
    _ensure_sessions_table();

    $token = bin2hex(random_bytes(32));
    $_SESSION['session_token']      = $token;
    $_SESSION['session_last_check'] = time();

    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

    $conn = conectar_bd();
    $stmt = $conn->prepare("INSERT INTO user_sessions (user_id, session_token, ip, user_agent) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('isss', $userId, $token, $ip, $ua);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function refresh_session(): void
{
    if (!isset($_SESSION['session_token'], $_SESSION['user_id'])) return;

    // Solo revalida contra BD cada 5 minutos para no cargar en cada request
    if (isset($_SESSION['session_last_check']) && (time() - $_SESSION['session_last_check']) < 300) return;

    $conn   = conectar_bd();
    $token  = $_SESSION['session_token'];
    $userId = (int)$_SESSION['user_id'];
    $stmt   = $conn->prepare("UPDATE user_sessions SET last_activity = NOW() WHERE session_token = ? AND user_id = ?");
    $stmt->bind_param('si', $token, $userId);
    $stmt->execute();
    $found = $stmt->affected_rows > 0;
    $stmt->close();
    $conn->close();

    if ($found) {
        $_SESSION['session_last_check'] = time();
    } else {
        // Token eliminado externamente → forzar logout
        session_unset();
        session_destroy();
        header('Location: /admin/login.php');
        exit();
    }
}

function destroy_current_session(): void
{
    if (!isset($_SESSION['session_token'])) return;
    $conn  = conectar_bd();
    $token = $_SESSION['session_token'];
    $stmt  = $conn->prepare("DELETE FROM user_sessions WHERE session_token = ?");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function destroy_session_by_id(int $sessionId, int $userId): bool
{
    $conn = conectar_bd();
    $stmt = $conn->prepare("DELETE FROM user_sessions WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $sessionId, $userId);
    $stmt->execute();
    $deleted = $stmt->affected_rows > 0;
    $stmt->close();
    $conn->close();
    return $deleted;
}

function get_active_sessions(int $userId): array
{
    _ensure_sessions_table();
    $conn   = conectar_bd();
    $stmt   = $conn->prepare("SELECT id, ip, user_agent, created_at, last_activity, session_token FROM user_sessions WHERE user_id = ? ORDER BY last_activity DESC");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result   = $stmt->get_result();
    $sessions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $sessions;
}
