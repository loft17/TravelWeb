# TravelGuide

Aplicación web en PHP + MySQL para gestionar y publicar guías de viaje. Incluye panel de administración para organizar atracciones, restaurantes, traslados y herramientas, y una vista pública PWA para consultar el plan del viaje desde el móvil.

## Estructura

```
/admin      → Panel de administración
/plan       → Vista pública del viaje (PWA)
/content    → Subidas de imágenes y archivos
config.php  → Configuración de base de datos
```

## Funcionalidades

### Vista pública `/plan`

| Sección | Descripción |
|---------|-------------|
| **Hoy** | Atracciones y traslados del día, calculados con la fecha del dispositivo |
| **Gastronomía** | Lista de restaurantes con puntuación y marcado de visitados |
| **Traslados** | Todos los traslados del viaje agrupados por fecha |
| **Calendario** | Vista mensual con días que tienen plan o transporte |
| **Buscar** | Búsqueda de atracciones, restaurantes y traslados |
| **Dark mode** | Toggle en el header, persiste en `localStorage` |
| **PWA** | Instalable en móvil, funciona offline con service worker |

**Cambio automático de viaje:** la web detecta qué viaje mostrar según la fecha actual. Si hoy está dentro del rango `fecha_inicio–fecha_fin` de un viaje, ese es el activo. Si no, muestra el próximo viaje programado.

### Panel de administración `/admin`

| Sección | Descripción |
|---------|-------------|
| **Viajes** | Crear y editar viajes con fechas obligatorias. Valida solapamiento de fechas. Muestra qué viaje está activo en la web |
| **Atracciones** | Gestión de atracciones por día y viaje |
| **Comida** | Restaurantes con puntuación y estado visitado |
| **Traslados** | Vuelos, trenes, buses, ferries y otros traslados |
| **Maleta** | Lista de equipaje por categoría y peso |
| **Gastos** | Control de gastos por categoría y divisa |
| **Tareas** | Tareas de preparación del viaje |
| **Configuración web** | Título y pie de página del sitio |
| **Configuración de cuenta** | Perfil y contraseña del usuario |

## Requisitos

- PHP con extensión `mbstring`
- MySQL / MariaDB
- Servidor web (Apache / Nginx)

```bash
apt install php-mbstring
```

## Instalación

```bash
git clone https://github.com/loft17/TravelGuide.git
```

### Base de datos

```sql
CREATE DATABASE travel_db;
GRANT ALL PRIVILEGES ON travel_db.* TO 'travel_user'@'localhost' IDENTIFIED BY 'tu_password';
```

### Configuración

Edita `config.php` con los datos de conexión a la base de datos:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'travel_user');
define('DB_PASS', 'tu_password');
define('DB_NAME', 'travel_db');
```

### Permisos de subida

```bash
chown -R www-data:www-data /var/www/travel/content/
chmod -R 775 /var/www/travel/content/
```

### Instalación inicial

Accede al asistente de instalación para crear las tablas y el usuario administrador:

```
http://tu-dominio.com/admin/install.php
```

El asistente crea todas las tablas necesarias (incluida `transportes`) y un viaje de ejemplo con fechas por defecto.

## Acceso

| Ruta | Descripción |
|------|-------------|
| `/admin` | Panel de administración |
| `/plan` | Vista pública del viaje |

## Contribuciones

- **[SRTdash admin dashboard](https://github.com/puikinsh/srtdash-admin-dashboard)** de puikinsh
- **[TCPDF](https://tcpdf.org/)** para generación de PDF

## Versión

v2026.06.19
