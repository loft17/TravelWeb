<?php
// Obtener la fecha desde la URL (formato YYYY-MM-DD)
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;
?>
<!-- header.php -->


<header>
    <div class="header-container">
        <h1>Tailandia '25</h1>
        <button class="menu-toggle" aria-label="Abrir menú">
            <span class="material-icons">menu</span> <!-- Ícono de hamburguesa -->
        </button>
        <nav class="menu" id="menu">
            <ul>
                <li><a href="https://travel.joseromera.net">Inicio</a></li>
                <li><a href="#atracciones">Atracciones</a></li>
                <li><a href="#comida">Comida</a></li>
                <li><a href="#cultura">Cultura</a></li>
                <li><a href="https://travel.joseromera.net/admin">admin</a></li>
            </ul>
        </nav>
    </div>
</header>
