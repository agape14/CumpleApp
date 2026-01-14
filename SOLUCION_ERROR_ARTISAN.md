# 🔧 Solución: Error "Could not open input file: artisan"

## ❌ El Error

```
Could not open input file: artisan
Script @php artisan package:discover --ansi handling the post-autoload-dump event returned with error code 1
```

## 🔍 ¿Por qué ocurre?

Este error aparece cuando ejecutas `composer install` en un directorio que **NO** tiene una instalación completa de Laravel. 

Los archivos que se generaron para CumpleApp son solo los **personalizados** de la aplicación (modelos, controladores, vistas, etc.), pero **NO incluyen** la estructura base completa de Laravel.

## ✅ Solución Paso a Paso

### Opción 1: Instalar en un Laravel Limpio (RECOMENDADO)

Esta es la forma más rápida y segura:

```bash
# 1. Navega a tu directorio de proyectos
cd c:\laragon\www

# 2. Crea un nuevo proyecto Laravel 11
composer create-project laravel/laravel CumpleApp_Clean

# 3. Copia TODOS los archivos de CumpleApp al nuevo proyecto
# (Reemplaza los archivos existentes cuando te lo pregunte)

# En Windows (PowerShell):
Copy-Item -Path "C:\laragon\www\CumpleApp\*" -Destination "C:\laragon\www\CumpleApp_Clean\" -Recurse -Force

# En Windows (CMD):
xcopy "C:\laragon\www\CumpleApp\*" "C:\laragon\www\CumpleApp_Clean\" /E /H /Y

# En Linux/Mac:
cp -rf CumpleApp/* CumpleApp_Clean/

# 4. Entra al nuevo proyecto
cd CumpleApp_Clean

# 5. Ahora ya puedes continuar con la instalación normal
composer install
php artisan key:generate
```

### Opción 2: Completar la instalación actual

He creado los archivos esenciales de Laravel. Ahora ejecuta:

```bash
# 1. Verifica que existe el archivo artisan
ls artisan    # Linux/Mac
dir artisan   # Windows

# 2. Si existe, ejecuta
composer install

# 3. Luego genera la clave
php artisan key:generate
```

### Opción 3: Instalación Manual de Laravel

Si prefieres hacerlo manualmente en el directorio actual:

```bash
# 1. Sal del directorio CumpleApp
cd ..

# 2. Renombra el directorio actual
mv CumpleApp CumpleApp_Files  # Linux/Mac
ren CumpleApp CumpleApp_Files  # Windows

# 3. Crea un nuevo proyecto Laravel con el nombre CumpleApp
composer create-project laravel/laravel CumpleApp

# 4. Copia los archivos personalizados al nuevo proyecto
# Copia estos directorios/archivos de CumpleApp_Files a CumpleApp:

- app/Models/*
- app/Http/Controllers/*
- app/Console/Commands/*
- app/Console/Kernel.php
- app/Mail/*
- database/migrations/*
- database/seeders/*
- resources/views/*
- routes/web.php
- routes/console.php
- composer.json (los require y scripts)
- Todos los archivos .md de documentación

# 5. Entra al proyecto
cd CumpleApp

# 6. Actualiza dependencias
composer update
```

## 📋 Verificación

Después de aplicar cualquiera de las soluciones, verifica:

```bash
# 1. El archivo artisan debe existir
ls -la artisan  # Linux/Mac
dir artisan     # Windows

# 2. La carpeta vendor debe existir
ls -la vendor/  # Linux/Mac
dir vendor\     # Windows

# 3. Composer debe funcionar sin errores
composer install

# 4. Artisan debe funcionar
php artisan list
```

## 🎯 Continuar con la Instalación

Una vez resuelto el error, continúa con estos pasos:

### 1. Crear archivo .env

```bash
# Linux/Mac
cp .env.example .env

# Windows
copy .env.example .env
```

O crea manualmente el archivo `.env` con este contenido mínimo:

```env
APP_NAME=CumpleApp
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=America/Lima
APP_URL=http://localhost

APP_LOCALE=es
APP_FALLBACK_LOCALE=es

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cumpleapp
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log
MAIL_FROM_ADDRESS=hello@cumpleapp.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. Generar clave de aplicación

```bash
php artisan key:generate
```

### 3. Crear base de datos

```sql
-- Entra a MySQL
mysql -u root -p

-- Crea la base de datos
CREATE DATABASE cumpleapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 4. Ejecutar migraciones

```bash
php artisan migrate --seed
```

### 5. Iniciar servidor

```bash
php artisan serve
```

### 6. Abrir en navegador

Visita: http://localhost:8000

## ⚠️ Errores Comunes Adicionales

### Error: "No application encryption key"
```bash
php artisan key:generate
```

### Error: "Class not found"
```bash
composer dump-autoload
```

### Error: "SQLSTATE[HY000] [2002]"
- Verifica que MySQL esté corriendo
- Verifica las credenciales en `.env`

### Error: "Permission denied" (Linux/Mac)
```bash
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

## 📚 Documentación Adicional

- `README.md` - Guía completa del proyecto
- `INSTALACION_RAPIDA.md` - Instalación en 5 minutos
- `CONFIGURACION.md` - Configuración del .env
- `CHECKLIST_INSTALACION.md` - Lista de verificación

## 💡 Consejo

Para futuros proyectos Laravel personalizados, la mejor práctica es:

1. Crear primero el proyecto Laravel limpio
2. Luego agregar/modificar los archivos personalizados
3. Nunca eliminar archivos base de Laravel

Esto evita este tipo de errores de configuración.

---

**¿Sigue sin funcionar?** Revisa los logs en `storage/logs/laravel.log` o abre un issue con el mensaje de error completo.

