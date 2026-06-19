<?php
// Incluye tu archivo de configuración
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Conecta a la base de datos
$conn = conectar_bd();

// Consulta para obtener el título del sitio desde la base de datos
$sql = "SELECT config_value FROM configurations WHERE config_key = 'title_web' LIMIT 1";
$result = $conn->query($sql);

$site_title = ($result && $row = $result->fetch_assoc()) ? $row['config_value'] : 'TravelGuide';

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#ffffff" id="theme-color-meta">
  <link rel="manifest" href="/plan/manifest.json">
  <script>
    (function () {
      var t = localStorage.getItem('theme') || 'light';
      document.documentElement.setAttribute('data-theme', t);
    })();
  </script>
  <title><?php echo htmlspecialchars($site_title); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Gidole&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <!-- Checkbox para controlar el menú -->
  <input type="checkbox" id="menu-toggle">
  <header>
    <!-- Se muestra el título obtenido de la BD -->
    <div class="titulo"><?php echo htmlspecialchars($site_title); ?></div>
    <div style="display:flex;align-items:center;gap:12px;">
      <?php if (!empty($_SESSION['user_name'])): ?>
        <span style="font-size:.75em;color:#71717a;"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
        <a href="/plan/logout.php" style="font-size:.75em;color:#71717a;text-decoration:none;border:1px solid #ddd;padding:3px 8px;border-radius:4px;">Salir</a>
      <?php endif; ?>
      <button id="dark-toggle" class="dark-btn" title="Cambiar tema" aria-label="Cambiar tema">
        <span class="material-icons" id="dark-icon">dark_mode</span>
      </button>
      <label for="menu-toggle" class="menu-btn">
        <span></span>
        <span></span>
        <span></span>
      </label>
    </div>
  </header>
  
  <!-- Menú de pantalla completa con nuevo diseño -->
  <div class="menu-overlay">
    <div class="menu-header">
      <!-- Etiqueta para cerrar el menú -->
      <label for="menu-toggle" class="close-btn">Cerrar</label>
    </div>
    <nav>
      <a href="/plan/index.php">Hoy</a>
      <a href="/plan/comida.php">Gastronomía</a>
      <a href="/plan/transportes.php">Traslados</a>
      <a href="/plan/calendario.php">Calendario</a>
      <a href="/plan/buscar.php">Buscar</a>
    </nav>
  </div>
</body>
</html>
