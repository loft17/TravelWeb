<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = conectar_bd();

// Migración automática: añadir lat/lng si no existen
$check = $conn->query("SHOW COLUMNS FROM atracciones LIKE 'lat'");
if ($check->num_rows === 0) {
    $conn->query("ALTER TABLE atracciones ADD COLUMN lat DECIMAL(10,8) DEFAULT NULL AFTER activo");
    $conn->query("ALTER TABLE atracciones ADD COLUMN lng DECIMAL(11,8) DEFAULT NULL AFTER lat");
}

// Guardar coordenadas via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_save_coords'])) {
    csrf_check();
    $id  = intval($_POST['id']  ?? 0);
    $lat = floatval($_POST['lat'] ?? 0);
    $lng = floatval($_POST['lng'] ?? 0);
    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE atracciones SET lat = ?, lng = ? WHERE id = ?");
        $stmt->bind_param('ddi', $lat, $lng, $id);
        $stmt->execute();
        $stmt->close();
    }
    $conn->close();
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit();
}

$result      = $conn->query("SELECT id, nombre, ciudad, fecha, visto, activo, lat, lng, imagen_url FROM atracciones ORDER BY fecha, orden");
$atracciones = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$conn->close();

include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
?>
<!doctype html>
<html class="no-js" lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mapa de Atracciones</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        #map { height: 600px; width: 100%; border-radius: 8px; }
        .map-legend { background: #fff; padding: 10px 14px; border-radius: 6px; box-shadow: 0 1px 5px rgba(0,0,0,.3); line-height: 1.8; font-size:13px; }
        .map-legend i { display:inline-block; width:14px; height:14px; border-radius:50%; margin-right:6px; vertical-align:middle; }
    </style>
</head>
<body>
<div class="page-container">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php'; ?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php'; ?>

    <div class="main-content">
        <div class="main-content-inner">

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="header-title mb-0">Mapa de Atracciones</h4>
                                <small class="text-muted">Haz clic derecho sobre el mapa para fijar coordenadas a una atracción</small>
                            </div>
                            <div id="map"></div>
                            <div class="mt-2">
                                <span class="badge" style="background:#2196F3; color:#fff">● Pendiente de ver</span>
                                <span class="badge ml-2" style="background:#4CAF50; color:#fff">● Vista</span>
                                <span class="badge ml-2" style="background:#9E9E9E; color:#fff">● Inactiva</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de atracciones sin coordenadas -->
            <?php
            $sinCoords = array_filter($atracciones, fn($a) => $a['lat'] === null || $a['lat'] == 0);
            if (!empty($sinCoords)):
            ?>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="header-title">Sin coordenadas (<?= count($sinCoords) ?>)</h5>
                            <p class="text-muted small">Edita la atracción y añade latitud/longitud para que aparezca en el mapa.</p>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($sinCoords as $a): ?>
                                    <a href="edit-atraccion.php?id=<?= intval($a['id']) ?>" class="btn btn-outline-secondary btn-sm mb-1">
                                        <?= htmlspecialchars($a['nombre']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php'; ?>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php'; ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
var atracciones = <?= json_encode(array_values($atracciones), JSON_HEX_TAG) ?>;
var csrfToken   = '<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>';

var conCoords = atracciones.filter(function(a){ return a.lat && a.lng; });

// Centro del mapa: promedio de coordenadas disponibles o Europa por defecto
var centerLat = 40.4168, centerLng = -3.7038, zoom = 5;
if (conCoords.length > 0) {
    centerLat = conCoords.reduce(function(s,a){ return s + parseFloat(a.lat); }, 0) / conCoords.length;
    centerLng = conCoords.reduce(function(s,a){ return s + parseFloat(a.lng); }, 0) / conCoords.length;
    zoom = conCoords.length === 1 ? 13 : 8;
}

var map = L.map('map').setView([centerLat, centerLng], zoom);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19
}).addTo(map);

function markerColor(a) {
    if (!a.activo || a.activo == 0) return '#9E9E9E';
    return (a.visto && a.visto != 0) ? '#4CAF50' : '#2196F3';
}

function makeIcon(color) {
    return L.divIcon({
        className: '',
        html: '<div style="width:14px;height:14px;border-radius:50%;background:' + color + ';border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,.4)"></div>',
        iconSize: [14, 14],
        iconAnchor: [7, 7],
        popupAnchor: [0, -10]
    });
}

conCoords.forEach(function(a) {
    var color = markerColor(a);
    var popup = '<strong>' + a.nombre + '</strong>';
    if (a.ciudad) popup += '<br><small>' + a.ciudad + '</small>';
    if (a.fecha)  popup += '<br><small>' + a.fecha + '</small>';
    popup += '<br><a href="/admin/pages/atracciones/edit-atraccion.php?id=' + a.id + '" class="btn btn-xs btn-primary mt-1" style="font-size:11px;padding:2px 6px">Editar</a>';

    L.marker([parseFloat(a.lat), parseFloat(a.lng)], { icon: makeIcon(color) })
        .addTo(map)
        .bindPopup(popup);
});

// Dibujar línea de ruta entre atracciones ordenadas con coords
if (conCoords.length > 1) {
    var latlngs = conCoords
        .filter(function(a){ return a.lat && a.lng; })
        .sort(function(a,b){ return a.fecha > b.fecha ? 1 : -1; })
        .map(function(a){ return [parseFloat(a.lat), parseFloat(a.lng)]; });
    L.polyline(latlngs, { color: '#607D8B', weight: 2, dashArray: '6,4', opacity: 0.7 }).addTo(map);
}

if (conCoords.length === 0) {
    map.setView([40.4168, -3.7038], 5);
}
</script>
</body>
</html>
