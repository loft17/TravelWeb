# TravelGuide

Aplicación web en PHP + MySQL para gestionar y publicar guías de viaje. Incluye panel de administración para organizar atracciones, restaurantes, ficheros y herramientas, y una vista pública para consultar el plan del viaje.

## Estructura

```
/admin      → Panel de administración (atracciones, comida, ficheros, herramientas)
/plan       → Vista pública del viaje con calendario
/content    → Subidas de imágenes y archivos
config.php  → Configuración de base de datos y URL
```

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

Edita `config.php` con los valores de tu entorno:

```php
define('TITLE_WEB', 'MiViaje');
define('URL_WEB',   'http://tu-dominio.com');
define('VIAJE_ID',  1);

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
