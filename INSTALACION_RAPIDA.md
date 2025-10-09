# ⚡ Instalación Rápida de CumpleApp

Guía rápida para poner en marcha CumpleApp en 5 minutos.

## 📋 Pre-requisitos

- PHP 8.2+
- MySQL 5.7+
- Composer

## 🚀 Pasos de Instalación

### ⚠️ IMPORTANTE: Dos métodos de instalación

#### Método 1: Si ya tienes Laravel instalado (RECOMENDADO)
Si estás viendo este proyecto en una instalación existente de Laravel, continúa con el paso 2.

#### Método 2: Si es un proyecto nuevo
Si descargaste solo los archivos de CumpleApp sin Laravel base, primero debes crear un proyecto Laravel:

```bash
# Sal del directorio actual si estás dentro de CumpleApp
cd ..

# Crea un nuevo proyecto Laravel
composer create-project laravel/laravel CumpleApp_Temp

# Copia los archivos generados a este nuevo proyecto
# (En Windows, usa xcopy o el explorador de archivos)
cp -r CumpleApp/* CumpleApp_Temp/
cd CumpleApp_Temp
```

### 1️⃣ Verificar estructura del proyecto

Asegúrate de tener el archivo `artisan` en la raíz:
```bash
ls artisan  # En Linux/Mac
dir artisan  # En Windows
```

Si **NO** existe, ejecuta:
```bash
# Instalar Laravel en el directorio actual
composer create-project --prefer-dist laravel/laravel .
# Luego reemplaza los archivos con los de CumpleApp
```

### 2️⃣ Instalar/Actualizar dependencias

```bash
composer install
# o si ya existe vendor/
composer update
```

### 3️⃣ Configurar el archivo .env

Copia el contenido del archivo `CONFIGURACION.md` y crea un archivo `.env` en la raíz del proyecto.

O si existe `.env.example`:
```bash
cp .env.example .env
```

Edita el `.env` y configura tu base de datos:

```env
DB_DATABASE=cumpleapp
DB_USERNAME=root
DB_PASSWORD=tu_password
```

### 4️⃣ Generar la clave de la aplicación

```bash
php artisan key:generate
```

### 5️⃣ Crear la base de datos

```bash
# Entra a MySQL
mysql -u root -p

# Crea la base de datos
CREATE DATABASE cumpleapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 6️⃣ Ejecutar migraciones y seeders

```bash
php artisan migrate --seed
```

### 7️⃣ Iniciar el servidor

```bash
php artisan serve
```

### 8️⃣ Abrir en el navegador

Ve a: http://localhost:8000

¡Listo! 🎉

## 📧 Configuración de Email (Opcional)

Para activar las notificaciones de cumpleaños, configura el email en `.env`:

### Opción 1: Gmail

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_contraseña_app_de_google
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu_email@gmail.com
```

**Nota**: Necesitas crear una "Contraseña de Aplicación" en Google:
1. Ve a https://myaccount.google.com/security
2. Activa la verificación en 2 pasos
3. Ve a "Contraseñas de aplicación"
4. Genera una para "Correo"
5. Usa esa contraseña en `MAIL_PASSWORD`

### Opción 2: Mailtrap (Para pruebas)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_username_mailtrap
MAIL_PASSWORD=tu_password_mailtrap
```

## 🔔 Activar Notificaciones Automáticas

### Probar manualmente

```bash
php artisan birthdays:send-reminders
```

### Activar el scheduler (Linux/Mac)

Abre el crontab:
```bash
crontab -e
```

Agrega esta línea (reemplaza la ruta):
```bash
* * * * * cd /ruta/completa/a/CumpleApp && php artisan schedule:run >> /dev/null 2>&1
```

### Activar el scheduler (Windows)

1. Abre el "Programador de Tareas"
2. Crea una tarea básica
3. Nombre: "CumpleApp Scheduler"
4. Disparador: Diariamente, repetir cada 1 minuto
5. Acción: Iniciar programa
   - Programa: `C:\ruta\a\php.exe`
   - Argumentos: `artisan schedule:run`
   - Directorio: `C:\laragon\www\CumpleApp`

## 🎯 Primeros Pasos

1. **Agrega un familiar**:
   - Ve a "Familiares" → "Agregar Familiar"
   - Completa el formulario
   - Guarda

2. **Agrega ideas de regalos**:
   - Haz clic en un familiar
   - En la sección "Ideas de Regalos", haz clic en "Agregar"
   - Completa la idea y guarda

3. **Explora el Dashboard**:
   - Ve al Dashboard
   - Verás estadísticas y próximos cumpleaños
   - Revisa el gráfico de distribución mensual

## ⚠️ Solución de Problemas

### Error: "No application encryption key"
```bash
php artisan key:generate
```

### Error: "SQLSTATE[HY000] [1045] Access denied"
Verifica las credenciales de MySQL en `.env`

### Error: "Class 'App\Models\...' not found"
```bash
composer dump-autoload
```

### La página no carga estilos
```bash
php artisan cache:clear
php artisan view:clear
```

## 📚 Documentación Completa

Para más información, consulta:
- `README.md` - Documentación completa
- `CONFIGURACION.md` - Configuración detallada

## 💡 Consejos

- Usa datos reales desde el principio para aprovechar mejor la app
- Activa las notificaciones para no olvidar ningún cumpleaños
- Revisa el Dashboard regularmente
- Agrega ideas de regalos durante todo el año

---

**¿Necesitas ayuda?** Abre un issue en el repositorio o consulta la documentación completa.

