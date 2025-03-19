<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi Sitio Minimalista</title>
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
    <div class="titulo">Mi Sitio</div>
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