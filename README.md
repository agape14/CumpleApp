# 🎂 CumpleApp - Aplicación de Recordatorios de Cumpleaños

![Laravel](https://img.shields.io/badge/Laravel-11-red?logo=laravel)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple?logo=bootstrap)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue?logo=php)
![License](https://img.shields.io/badge/License-MIT-green)

CumpleApp es una aplicación web moderna y elegante para gestionar y recordar los cumpleaños de tus seres queridos. Nunca más olvides una fecha importante con nuestro sistema de notificaciones automáticas.

## ✨ Características

- 📅 **Gestión de Familiares**: Registra información completa de tus familiares y amigos
- 🎁 **Ideas de Regalos**: Guarda y organiza ideas de regalos para cada persona
- 🔔 **Notificaciones Automáticas**: Recibe recordatorios por email de cumpleaños
- 🔥 **Notificaciones Push**: Notificaciones móviles con Firebase Cloud Messaging
- 📊 **Dashboard Interactivo**: Visualiza estadísticas y próximos cumpleaños
- ♐ **Signos Zodiacales**: Calcula automáticamente el signo zodiacal
- 🎨 **Interfaz Moderna**: Diseño responsivo con Bootstrap 5
- 📱 **Acciones Rápidas**: Llamadas y emails directos desde la app
- 🌐 **API REST**: API completa para integración con apps móviles

## 🚀 Requisitos

- PHP 8.2 o superior
- Composer
- MySQL 5.7 o superior
- Node.js y NPM (opcional, para compilar assets)
- Extensiones PHP requeridas:
  - OpenSSL
  - PDO
  - Mbstring
  - Tokenizer
  - XML
  - Ctype
  - JSON
  - BCMath

## 📦 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/cumpleapp.git
cd cumpleapp
```

### 2. Instalar dependencias

```bash
composer install
```

### 3. Configurar variables de entorno

```bash
cp .env.example .env
```

Edita el archivo `.env` y configura tu base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cumpleapp
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

Configura también el servicio de correo para las notificaciones:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_contraseña_app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu_email@gmail.com
MAIL_FROM_NAME="CumpleApp"
```

### 4. Generar clave de aplicación

```bash
php artisan key:generate
```

### 5. Ejecutar migraciones y seeders

```bash
php artisan migrate --seed
```

Esto creará las tablas necesarias y poblará la tabla de parentescos con valores iniciales.

### 6. Iniciar servidor de desarrollo

```bash
php artisan serve
```

La aplicación estará disponible en: `http://localhost:8000`

## 📋 Estructura de la Base de Datos

### Tabla `parentescos`
- `id`: Identificador único
- `nombre_parentesco`: Tipo de parentesco (Padre, Madre, Hermano, etc.)
- `timestamps`: Fechas de creación y actualización

### Tabla `familiares`
- `id`: Identificador único
- `nombre`: Nombre completo
- `fecha_nacimiento`: Fecha de nacimiento
- `telefono`: Número telefónico (opcional)
- `email`: Correo electrónico (opcional)
- `notificar`: Activar/desactivar notificaciones
- `notas`: Notas adicionales (opcional)
- `parentesco_id`: Relación con parentesco
- `timestamps`: Fechas de creación y actualización

### Tabla `ideas_regalos`
- `id`: Identificador único
- `idea`: Descripción de la idea
- `precio_estimado`: Precio aproximado (opcional)
- `link_compra`: URL del producto (opcional)
- `comprado`: Estado de compra
- `familiar_id`: Relación con familiar (eliminación en cascada)
- `timestamps`: Fechas de creación y actualización

## 🎯 Uso

### Dashboard
El dashboard muestra:
- Total de familiares registrados
- Cumpleaños del mes actual
- Cumpleaños de hoy (con alertas destacadas)
- Próximo cumpleaños
- Gráfico de distribución por mes

### Gestión de Familiares
- **Crear**: Agregar nuevos familiares con toda su información
- **Ver**: Consultar detalles completos y gestionar ideas de regalos
- **Editar**: Actualizar información
- **Eliminar**: Borrar familiar (elimina también sus ideas de regalos)

### Ideas de Regalos
Para cada familiar puedes:
- Agregar ideas de regalos
- Establecer precio estimado
- Guardar links de compra
- Marcar como comprado/no comprado
- Eliminar ideas

### Notificaciones Automáticas

El sistema incluye un comando artisan que envía recordatorios:

```bash
php artisan birthdays:send-reminders
```

Este comando está programado para ejecutarse automáticamente todos los días a las 8:00 AM.

#### Configurar el Scheduler

Para que las notificaciones funcionen automáticamente, agrega esta entrada a tu crontab:

```bash
* * * * * cd /ruta-a-tu-proyecto && php artisan schedule:run >> /dev/null 2>&1
```

O en Windows, usa el Programador de Tareas para ejecutar:

```cmd
php artisan schedule:run
```

## 🎨 Características de Diseño

- **UI/UX Moderna**: Interfaz intuitiva y atractiva
- **Gradientes**: Diseño con gradientes coloridos
- **Animaciones**: Transiciones suaves y efectos hover
- **Responsivo**: Funciona perfectamente en móviles, tablets y escritorio
- **Iconos**: Bootstrap Icons para una mejor experiencia visual
- **Gráficos**: Chart.js para visualización de datos

## 🔧 Comandos Útiles

```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Ejecutar migraciones
php artisan migrate

# Revertir migraciones
php artisan migrate:rollback

# Refrescar base de datos (¡cuidado, borra todos los datos!)
php artisan migrate:fresh --seed

# Enviar recordatorios por email manualmente
php artisan birthdays:send-reminders

# Enviar notificaciones push manualmente
php artisan birthdays:send-notifications

# Ver lista de rutas
php artisan route:list

# Ver comandos de Firebase
# Ver: COMANDOS_FIREBASE.md para más comandos
```

## 📧 Configuración de Email

### Gmail
1. Activa la verificación en 2 pasos
2. Genera una contraseña de aplicación
3. Usa esa contraseña en `MAIL_PASSWORD`

### Mailtrap (Desarrollo)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_username
MAIL_PASSWORD=tu_password
```

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Haz fork del proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver archivo `LICENSE` para más detalles.

## 👨‍💻 Autor

Desarrollado con ❤️ para nunca olvidar un cumpleaños

## 🎉 Capturas de Pantalla

### Dashboard
El dashboard muestra una vista general de todos los cumpleaños, estadísticas y próximos eventos.

### Gestión de Familiares
Interfaz intuitiva para agregar, editar y visualizar información de tus seres queridos.

### Ideas de Regalos
Sistema completo para gestionar ideas de regalos con precios y links de compra.

## 🚀 Próximas Características

- [ ] Exportar cumpleaños a Google Calendar
- [ ] Notificaciones por WhatsApp
- [x] Historial de regalos dados ✅
- [x] Recordatorios personalizados (X días antes) ✅
- [ ] Temas personalizables
- [x] API REST ✅
- [x] Notificaciones Push (Firebase) ✅
- [ ] Aplicación móvil (en desarrollo)

## 📚 Documentación Adicional

- **[FIREBASE_README.md](FIREBASE_README.md)** - Índice de documentación Firebase
- **[FIREBASE_SETUP.md](FIREBASE_SETUP.md)** - Guía completa de configuración Firebase
- **[COMANDOS_FIREBASE.md](COMANDOS_FIREBASE.md)** - Comandos útiles para Firebase
- **[ESTRUCTURA_PROYECTO.md](ESTRUCTURA_PROYECTO.md)** - Estructura completa del proyecto
- **[GUIA_AUTENTICACION.md](GUIA_AUTENTICACION.md)** - Sistema de autenticación
- **[GUIA_COMANDOS_ARTISAN.md](GUIA_COMANDOS_ARTISAN.md)** - Comandos Artisan disponibles

## ❓ Soporte

Si tienes alguna pregunta o problema, por favor abre un issue en el repositorio.

---

**CumpleApp** - Nunca olvides un cumpleaños 🎂

