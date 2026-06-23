# TravelGuide

Aplicación web en PHP + MySQL para gestionar y publicar guías de viaje. Incluye panel de administración para organizar atracciones, restaurantes, traslados y herramientas, y una vista pública PWA para consultar el plan del viaje desde el móvil.

## Estructura

```
/admin      → Panel de administración
/plan       → Vista pública del viaje (PWA)
/content    → Subidas de imágenes y archivos
/uploads    → Logos de aerolíneas y otras imágenes subidas
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
| **Transportes** | Vuelos, trenes, buses, ferries y otros traslados con soporte de escalas |
| **Aerolíneas** | Catálogo de aerolíneas con logo (se redimensiona automáticamente a WebP) |
| **Maleta** | Lista de equipaje por categoría y peso |
| **Gastos** | Control de gastos por categoría y divisa |
| **Tareas** | Tareas de preparación del viaje |
| **Configuración web** | Título y pie de página del sitio |
| **Configuración de cuenta** | Perfil y contraseña del usuario |

---

## Requisitos

### Servidor

| Requisito | Versión mínima | Notas |
|-----------|---------------|-------|
| PHP | 8.0 | Recomendado 8.2+ |
| MySQL / MariaDB | 5.7 / 10.4 | Usa `utf8mb4` |
| Apache / Nginx | cualquiera | Con `mod_php` o PHP-FPM |

### Extensiones PHP obligatorias

| Extensión | Uso | Paquete Debian/Ubuntu |
|-----------|-----|-----------------------|
| `mysqli` | Base de datos | `php8.x-mysqli` |
| `mbstring` | Texto UTF-8 | `php8.x-mbstring` |
| `json` | Datos de escalas y transporte | `php8.x-json` (incluida en `php8.x-common`) |
| `session` | Autenticación y CSRF | `php8.x-common` |

### Extensiones PHP recomendadas

| Extensión | Uso | Paquete Debian/Ubuntu |
|-----------|-----|-----------------------|
| `gd` | Redimensionar logos de aerolíneas a WebP | `php8.x-gd` |

### Instalación rápida de dependencias (Debian / Ubuntu)

```bash
# Sustituye 8.4 por tu versión de PHP
PHP=8.4

apt install php${PHP} php${PHP}-mysqli php${PHP}-mbstring php${PHP}-gd \
            php${PHP}-json php${PHP}-common \
            mysql-server apache2 libapache2-mod-php${PHP}
```

> El asistente de instalación (`/admin/install.php`) comprueba automáticamente todos los requisitos y muestra el comando exacto para instalar cualquier extensión que falte.

---

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/loft17/TravelGuide.git /var/www/travel
```

### 2. Base de datos

```sql
CREATE DATABASE travel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON travel_db.* TO 'travel_user'@'localhost' IDENTIFIED BY 'tu_password';
FLUSH PRIVILEGES;
```

### 3. Configuración

Edita `config.php` con los datos de conexión:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'travel_user');
define('DB_PASS', 'tu_password');
define('DB_NAME', 'travel_db');
```

### 4. Permisos de subida

```bash
chown -R www-data:www-data /var/www/travel/uploads/
chown -R www-data:www-data /var/www/travel/content/
chmod -R 755 /var/www/travel/uploads/
chmod -R 755 /var/www/travel/content/
```

### 5. Asistente de instalación

Accede al asistente desde el navegador. Comprobará todos los requisitos antes de mostrar el formulario:

```
http://tu-dominio.com/admin/install.php
```

El asistente:
- Verifica PHP, extensiones, conexión a BD y permisos de escritura
- Crea todas las tablas necesarias
- Crea el usuario administrador
- Crea un viaje de ejemplo

Una vez completado, el acceso a `install.php` queda bloqueado automáticamente.

---

## Acceso

| Ruta | Descripción |
|------|-------------|
| `/admin` | Panel de administración |
| `/plan` | Vista pública del viaje |

---

## Contribuciones

- **[SRTdash admin dashboard](https://github.com/puikinsh/srtdash-admin-dashboard)** de puikinsh
- **[TCPDF](https://tcpdf.org/)** para generación de PDF

## Versión

v2026.06.22
