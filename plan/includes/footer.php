<?php
$pagActual = basename($_SERVER['PHP_SELF'], '.php');
?>
<nav class="bottom-nav" aria-label="Navegación principal">
    <a href="/plan/index.php"       class="bnav-item <?= $pagActual === 'index'       ? 'bnav-active' : '' ?>">
        <span class="material-icons">today</span>
        <span>Hoy</span>
    </a>
    <a href="/plan/comida.php"      class="bnav-item <?= $pagActual === 'comida'      ? 'bnav-active' : '' ?>">
        <span class="material-icons">restaurant</span>
        <span>Comida</span>
    </a>
    <a href="/plan/transportes.php" class="bnav-item <?= $pagActual === 'transportes' ? 'bnav-active' : '' ?>">
        <span class="material-icons">flight</span>
        <span>Traslados</span>
    </a>
    <a href="/plan/calendario.php"  class="bnav-item <?= $pagActual === 'calendario'  ? 'bnav-active' : '' ?>">
        <span class="material-icons">calendar_month</span>
        <span>Calendario</span>
    </a>
    <a href="/plan/buscar.php"      class="bnav-item <?= $pagActual === 'buscar'      ? 'bnav-active' : '' ?>">
        <span class="material-icons">search</span>
        <span>Buscar</span>
    </a>
</nav>
