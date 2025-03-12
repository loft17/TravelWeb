# Pendientes:
- Optimizar maleta.php
- Optimizar task.php


# Puesta en Marcha
## Instalacion
```
git clone https://github.com/loft17/TravelGuide.git
```

## Configuración


### Creamos la base de datos y damos permisos:
Nos conectamos a la base de datos para crear el usuario y la tabla:
```
mysql -u root -p
```

```
CREATE DATABASE travel_db;
GRANT ALL PRIVILEGES ON travel_db.* TO "travel_user"@"localhost" IDENTIFIED BY "pruebas";
```

Modificamos el fichero config.php y modificamos los parametros:
```
define('URL_WEB', 'http://travelpre.joseromera.net');

define('DB_HOST', 'localhost');
define('DB_USER', 'usuario');
define('DB_PASS', 'password'); // Cambia por tu contraseña
define('DB_NAME', 'base_de_datos');
```

### Permisos
Damos los permisos adecuados para pode subir imagenes:
```
chown -R www-data:www-data /srv/www/travelweb/public_html/content/
chmod -R 775 /srv/www/travelweb/public_html/content/
```

## Accediendo:
Ahora vamos a la url para iniciar la configuracion final y poder conectar:
```
http://localhost/admin/install.php
```

# Contribuciones
- **[RuangAdmin](https://github.com/indrijunanda/RuangAdmin)** de indrijunanda

# v2025.03.12
..
