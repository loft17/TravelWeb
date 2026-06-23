<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/activity_log.php';

function _ensure_aerolineas_table(): void {
    $conn = conectar_bd();
    $conn->query("CREATE TABLE IF NOT EXISTS aerolineas (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        nombre     VARCHAR(100) NOT NULL,
        codigo     VARCHAR(10)  DEFAULT NULL,
        icono      VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $conn->close();
}
_ensure_aerolineas_table();

function get_aerolineas(): array {
    static $cache = null;
    if ($cache !== null) return $cache;
    $conn = conectar_bd();
    $res  = $conn->query("SHOW TABLES LIKE 'aerolineas'");
    if (!$res || $res->num_rows === 0) { $conn->close(); return $cache = []; }
    $res2  = $conn->query("SELECT * FROM aerolineas ORDER BY nombre ASC");
    $cache = $res2 ? $res2->fetch_all(MYSQLI_ASSOC) : [];
    $conn->close();
    return $cache;
}

/**
 * Redimensiona la imagen al tamaño máximo indicado (manteniendo proporción)
 * y la guarda como WebP con calidad 85. Devuelve true en éxito.
 */
function _resize_and_save(string $src, string $ext, string $dest, int $maxW, int $maxH): bool {
    $map = ['jpg' => 'imagecreatefromjpeg', 'jpeg' => 'imagecreatefromjpeg',
            'png' => 'imagecreatefrompng',  'webp' => 'imagecreatefromwebp'];
    $fn = $map[$ext] ?? null;
    if (!$fn || !function_exists($fn)) return false;

    $src_img = $fn($src);
    if (!$src_img) return false;

    [$w, $h] = [imagesx($src_img), imagesy($src_img)];

    // Calcular nuevas dimensiones respetando proporción
    $ratio  = min($maxW / $w, $maxH / $h, 1); // nunca ampliar
    $newW   = (int) round($w * $ratio);
    $newH   = (int) round($h * $ratio);

    $dst_img = imagecreatetruecolor($newW, $newH);
    // Preservar transparencia para PNG/WebP
    imagealphablending($dst_img, false);
    imagesavealpha($dst_img, true);
    $transparent = imagecolorallocatealpha($dst_img, 0, 0, 0, 127);
    imagefill($dst_img, 0, 0, $transparent);

    imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $newW, $newH, $w, $h);
    $ok = imagewebp($dst_img, $dest, 85);
    imagedestroy($src_img);
    imagedestroy($dst_img);
    return $ok;
}

// Only handle POST when this file's page is active (avoid conflicts when included from other pages)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && basename($_SERVER['SCRIPT_FILENAME']) === 'show-aerolineas.php') {
    csrf_check();
    $accion = $_POST['accion'] ?? '';

    if (in_array($accion, ['agregar','editar'])) {
        $nombre = trim($_POST['nombre'] ?? '');
        $codigo = trim(strtoupper($_POST['codigo'] ?? '')) ?: null;
        $id     = intval($_POST['id'] ?? 0);

        if ($nombre === '') {
            $_SESSION['ae_error'] = 'El nombre es obligatorio.';
            header('Location: ' . $_SERVER['PHP_SELF']); exit;
        }

        // Icon: uploaded file takes priority over URL
        $icono = trim($_POST['icono_url'] ?? '') ?: null;

        if (!empty($_FILES['icono_file']['name']) && $_FILES['icono_file']['error'] === UPLOAD_ERR_OK) {
            $ext     = strtolower(pathinfo($_FILES['icono_file']['name'], PATHINFO_EXTENSION));
            $allowed = ['png','jpg','jpeg','svg','webp','gif'];
            if (!in_array($ext, $allowed)) {
                $_SESSION['ae_error'] = 'Formato no permitido (png, jpg, svg, webp).';
                header('Location: ' . $_SERVER['PHP_SELF']); exit;
            }
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/aerolineas/';
            // SVG y GIF animado se guardan tal cual sin redimensionar
            if (in_array($ext, ['svg', 'gif'])) {
                $filename = uniqid('al_') . '.' . $ext;
                if (move_uploaded_file($_FILES['icono_file']['tmp_name'], $upload_dir . $filename)) {
                    $icono = '/uploads/aerolineas/' . $filename;
                }
            } else {
                $filename = uniqid('al_') . '.webp';
                if (_resize_and_save($_FILES['icono_file']['tmp_name'], $ext, $upload_dir . $filename, 200, 80)) {
                    $icono = '/uploads/aerolineas/' . $filename;
                }
            }
        }

        $conn = conectar_bd();
        if ($accion === 'agregar') {
            $stmt = $conn->prepare("INSERT INTO aerolineas (nombre,codigo,icono) VALUES (?,?,?)");
            $stmt->bind_param('sss', $nombre, $codigo, $icono);
            $stmt->execute(); $stmt->close();
            log_activity('aerolinea_agregada', $nombre);
            $_SESSION['ae_success'] = 'Aerolínea añadida.';
        } else {
            if ($id > 0) {
                // Keep old icon if no new one provided
                if ($icono === null) {
                    $r = $conn->query("SELECT icono FROM aerolineas WHERE id=$id")->fetch_assoc();
                    $icono = $r['icono'] ?? null;
                }
                $stmt = $conn->prepare("UPDATE aerolineas SET nombre=?,codigo=?,icono=? WHERE id=?");
                $stmt->bind_param('sssi', $nombre, $codigo, $icono, $id);
                $stmt->execute(); $stmt->close();
                log_activity('aerolinea_editada', "id=$id: $nombre");
                $_SESSION['ae_success'] = 'Aerolínea actualizada.';
            }
        }
        $conn->close();

    } elseif ($accion === 'borrar') {
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $conn = conectar_bd();
            $row  = $conn->query("SELECT icono FROM aerolineas WHERE id=$id")->fetch_assoc();
            if ($row && $row['icono'] && str_starts_with($row['icono'], '/uploads/')) {
                $f = $_SERVER['DOCUMENT_ROOT'] . $row['icono'];
                if (file_exists($f)) unlink($f);
            }
            $stmt = $conn->prepare("DELETE FROM aerolineas WHERE id=?");
            $stmt->bind_param('i', $id); $stmt->execute(); $stmt->close();
            $conn->close();
            log_activity('aerolinea_borrada', "id=$id");
            $_SESSION['ae_success'] = 'Aerolínea eliminada.';
        }
    }
    header('Location: ' . $_SERVER['PHP_SELF']); exit;
}

$ae_error   = $_SESSION['ae_error']   ?? '';
$ae_success = $_SESSION['ae_success'] ?? '';
unset($_SESSION['ae_error'], $_SESSION['ae_success']);

$aerolineas = get_aerolineas();
