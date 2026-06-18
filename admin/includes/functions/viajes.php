<?php
// /admin/includes/functions/viajes.php

function ensure_viajes_setup(): void
{
    if (!empty($_SESSION['_migration_viajes'])) {
        return;
    }

    $conn = conectar_bd();

    $conn->query("CREATE TABLE IF NOT EXISTS viajes (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        nombre      VARCHAR(255) NOT NULL,
        destino     VARCHAR(255) DEFAULT '',
        fecha_inicio DATE DEFAULT NULL,
        fecha_fin    DATE DEFAULT NULL,
        activo      TINYINT(1) NOT NULL DEFAULT 1,
        created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Crear viaje por defecto si no hay ninguno
    $count = (int)$conn->query("SELECT COUNT(*) AS c FROM viajes")->fetch_assoc()['c'];
    if ($count === 0) {
        $conn->query("INSERT INTO viajes (nombre, destino) VALUES ('Mi Viaje', '')");
    }

    // Añadir viaje_id a todas las tablas de contenido
    $tablas = ['atracciones', 'gastos', 'comida', 'maleta', 'tareas', 'calendar_events'];
    foreach ($tablas as $tabla) {
        $existe = $conn->query("SHOW TABLES LIKE '$tabla'");
        if (!$existe || $existe->num_rows === 0) {
            continue;
        }
        $col = $conn->query("SHOW COLUMNS FROM `$tabla` LIKE 'viaje_id'");
        if ($col && $col->num_rows === 0) {
            $conn->query("ALTER TABLE `$tabla` ADD COLUMN viaje_id INT NOT NULL DEFAULT 1");
        }
    }

    // calendar_events: drop the old unique-on-fecha constraint so same date can exist in multiple trips
    $idx = $conn->query("SHOW INDEX FROM calendar_events WHERE Key_name = 'fecha'");
    if ($idx && $idx->num_rows > 0) {
        $conn->query("ALTER TABLE calendar_events DROP INDEX `fecha`");
    }

    $conn->close();
    $_SESSION['_migration_viajes'] = true;
}

function get_viaje_activo_id(): int
{
    if (empty($_SESSION['viaje_id'])) {
        $conn = conectar_bd();
        $row  = $conn->query("SELECT id FROM viajes ORDER BY id ASC LIMIT 1")->fetch_assoc();
        $conn->close();
        $_SESSION['viaje_id'] = $row ? (int)$row['id'] : 1;
    }
    return (int)$_SESSION['viaje_id'];
}

function get_viaje_activo(): array
{
    $id   = get_viaje_activo_id();
    $conn = conectar_bd();
    $stmt = $conn->prepare("SELECT * FROM viajes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $viaje = $stmt->get_result()->fetch_assoc() ?? ['id' => 1, 'nombre' => 'Mi Viaje', 'destino' => ''];
    $stmt->close();
    $conn->close();
    return $viaje;
}

function get_all_viajes(): array
{
    $conn   = conectar_bd();
    $result = $conn->query("SELECT * FROM viajes ORDER BY id ASC");
    $viajes = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $conn->close();
    return $viajes;
}
