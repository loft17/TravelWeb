<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

function get_config(string $key, string $default = ''): string
{
    if (!isset($_SESSION['site_config'])) {
        $conn = conectar_bd();
        $result = $conn->query("SELECT config_key, config_value FROM configurations");
        $_SESSION['site_config'] = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $_SESSION['site_config'][$row['config_key']] = $row['config_value'];
            }
            $result->free();
        }
        $conn->close();
    }
    return $_SESSION['site_config'][$key] ?? $default;
}

function clear_config_cache(): void
{
    unset($_SESSION['site_config']);
}
