# 🔧 Configuración de CumpleApp

Este archivo contiene las variables de entorno necesarias para configurar la aplicación.

## Archivo .env

Crea un archivo `.env` en la raíz del proyecto con el siguiente contenido:

```env
APP_NAME=CumpleApp
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=America/Lima
APP_URL=http://localhost

APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_FAKER_LOCALE=es_ES

APP_MAINTENANCE_DRIVER=file
PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Configuración de Base de Datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cumpleapp
DB_USERNAME=root
DB_PASSWORD=

# Configuración de Sesiones
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# Configuración de Broadcasting y Storage
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

# Configuración de Caché
CACHE_STORE=database
CACHE_PREFIX=

# Configuración de Redis (opcional)
MEMCACHED_HOST=127.0.0.1
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Configuración de Email (Gmail)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_contraseña_app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# Configuración de AWS (opcional)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
```

## Pasos de Configuración

### 1. Base de Datos

```bash
# Crear la base de datos
mysql -u root -p
CREATE DATABASE cumpleapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 2. Generar APP_KEY

```bash
php artisan key:generate
```

### 3. Configurar Email (Gmail)

Para usar Gmail como servidor de correo:

1. Ve a tu cuenta de Google
2. Activa la verificación en 2 pasos
3. Ve a "Contraseñas de aplicación"
4. Genera una nueva contraseña para "Correo"
5. Usa esa contraseña en `MAIL_PASSWORD`

### 4. Configurar Email (Mailtrap - Desarrollo)

Para pruebas en desarrollo, usa Mailtrap:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_username_mailtrap
MAIL_PASSWORD=tu_password_mailtrap
MAIL_ENCRYPTION=tls
```

### 5. Ejecutar Migraciones

```bash
php artisan migrate --seed
```

### 6. Configurar Scheduler (Producción)

En Linux/Mac, agrega a crontab:
```bash
crontab -e

# Agregar esta línea:
* * * * * cd /ruta-completa-al-proyecto && php artisan schedule:run >> /dev/null 2>&1
```

En Windows, usa el Programador de Tareas:
- Acción: Ejecutar programa
- Programa: `C:\ruta\php\php.exe`
- Argumentos: `artisan schedule:run`
- Directorio: `C:\ruta\al\proyecto`
- Disparador: Cada 1 minuto

### 7. Configurar Zona Horaria

Ajusta `APP_TIMEZONE` según tu ubicación:

```env
# México
APP_TIMEZONE=America/Lima

# España
APP_TIMEZONE=Europe/Madrid

# Argentina
APP_TIMEZONE=America/Argentina/Buenos_Aires

# Chile
APP_TIMEZONE=America/Santiago

# Colombia
APP_TIMEZONE=America/Bogota
```

## Variables de Entorno Importantes

| Variable | Descripción | Valor por defecto |
|----------|-------------|-------------------|
| `APP_NAME` | Nombre de la aplicación | CumpleApp |
| `APP_ENV` | Entorno (local/production) | local |
| `APP_DEBUG` | Modo debug | true |
| `APP_TIMEZONE` | Zona horaria | America/Lima |
| `DB_DATABASE` | Nombre de la BD | cumpleapp |
| `DB_USERNAME` | Usuario de BD | root |
| `DB_PASSWORD` | Contraseña de BD | (vacío) |
| `MAIL_HOST` | Servidor SMTP | smtp.gmail.com |
| `MAIL_PORT` | Puerto SMTP | 587 |
| `MAIL_USERNAME` | Email de envío | - |
| `MAIL_PASSWORD` | Contraseña de email | - |

## Permisos de Archivos (Linux/Mac)

```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

## Solución de Problemas Comunes

### Error: "No application encryption key"
```bash
php artisan key:generate
```

### Error: "Could not find driver"
Instala la extensión PDO MySQL:
```bash
sudo apt-get install php-mysql
```

### Error: "Class 'Composer\...' not found"
```bash
composer install --optimize-autoloader
```

### Las notificaciones no se envían
1. Verifica la configuración de email
2. Prueba el comando manualmente: `php artisan birthdays:send-reminders`
3. Revisa los logs: `storage/logs/laravel.log`
4. Verifica que el scheduler esté configurado correctamente

## Modo Producción

Cuando vayas a producción, actualiza estas variables:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

# Usa una base de datos segura
DB_PASSWORD=contraseña_segura

# Configura email de producción
MAIL_FROM_ADDRESS=noreply@tudominio.com
```

Y ejecuta:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

