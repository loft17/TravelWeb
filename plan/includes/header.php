<?php
// Incluye tu archivo de configuración
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Conecta a la base de datos
$conn = conectar_bd();

// Consulta para obtener el título del sitio desde la base de datos
$sql = "SELECT config_value FROM configurations WHERE config_key = 'title_web' LIMIT 1";
$result = $conn->query($sql);

// Valor por defecto (el definido en la constante) en caso de que no se encuentre en la BD
$site_title = TITLE_WEB;

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $site_title = $row['config_value'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Se utiliza el título obtenido desde la base de datos -->
  <title><?php echo htmlspecialchars($site_title); ?></title>
  <!-- Google Fonts: Gidole -->
  <link href="https://fonts.googleapis.com/css2?family=Gidole&display=swap" rel="stylesheet">
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <!-- Checkbox para controlar el menú -->
  <input type="checkbox" id="menu-toggle">
  <header>
    <!-- Se muestra el título obtenido de la BD -->
    <div class="titulo"><?php echo htmlspecialchars($site_title); ?></div>
    <label for="menu-toggle" class="menu-btn">
      <span></span>
      <span></span>
      <span></span>
    </label>
  </header>
  
  <!-- Menú de pantalla completa con nuevo diseño -->
  <div class="menu-overlay">
    <div class="menu-header">
      <!-- Etiqueta para cerrar el menú -->
      <label for="menu-toggle" class="close-btn">Cerrar</label>
    </div>
    <nav>
      <a href="#">Inicio</a>
      <a href="#">Acerca</a>
      <a href="#">Entradas</a>
      <a href="#">Contacto</a>
    </nav>
  </div>
</body>
</html>
