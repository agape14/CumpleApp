# 🔥 Resumen de Integración Firebase - CumpleApp

## ✅ Estado de la Implementación

### Backend (Laravel) - ✅ COMPLETADO

| Componente | Estado | Ubicación |
|------------|--------|-----------|
| Paquete Firebase Admin SDK | ✅ Instalado | `composer.json` |
| Migración FCM Tokens | ✅ Creada | `database/migrations/2025_12_22_105453_create_fcm_tokens_table.php` |
| Controlador FCM | ✅ Implementado | `app/Http/Controllers/Api/FcmTokenController.php` |
| Servicio Firebase | ✅ Implementado | `app/Services/FirebaseNotificationService.php` |
| Comando Notificaciones | ✅ Implementado | `app/Console/Commands/SendBirthdayNotifications.php` |
| Rutas API | ✅ Configuradas | `routes/api.php` |
| Cron Job | ✅ Configurado | `app/Console/Kernel.php` |
| Directorio Credenciales | ✅ Creado | `storage/app/firebase/` |

### Pendiente - ⚠️ CONFIGURACIÓN REQUERIDA

1. **Obtener Credenciales de Firebase** ⚠️
   - Descargar el archivo JSON de credenciales desde Firebase Console
   - Colocarlo en: `storage/app/firebase/firebase-credentials.json`
   - Ver instrucciones en: `FIREBASE_SETUP.md`

2. **Ejecutar Migración** ⚠️
   ```bash
   php artisan migrate
   ```

3. **Configurar React Native** (Si aplica)
   - Instalar paquetes Firebase en la app móvil
   - Configurar `google-services.json`
   - Implementar envío de token al backend

---

## 📁 Archivos Creados/Modificados

### Nuevos Archivos

```
storage/app/firebase/
├── .gitignore                              # Ignora credenciales en Git
├── README.md                               # Instrucciones del directorio
└── firebase-credentials.example.json       # Ejemplo de estructura

FIREBASE_SETUP.md                           # Guía completa de configuración
INTEGRACION_FIREBASE_RESUMEN.md            # Este archivo
```

### Archivos Modificados

```
app/Services/FirebaseNotificationService.php    # Mejorado para tolerar falta de credenciales
app/Console/Kernel.php                          # Ya tenía configurado el cron job
routes/api.php                                  # Ya tenía las rutas configuradas
```

---

## 🔧 Funcionalidades Implementadas

### 1. API REST para FCM Tokens

#### `POST /api/v1/fcm-token`
Registra o actualiza un token FCM de un dispositivo.

**Parámetros:**
- `token` (requerido): Token FCM del dispositivo
- `device_type` (opcional): `android` o `ios`
- `familiar_id` (opcional): ID del familiar asociado

**Respuesta:**
```json
{
  "success": true,
  "message": "Token guardado correctamente"
}
```

#### `DELETE /api/v1/fcm-token`
Elimina un token FCM de la base de datos.

**Parámetros:**
- `token` (requerido): Token a eliminar

**Respuesta:**
```json
{
  "success": true,
  "message": "Token eliminado correctamente"
}
```

### 2. Servicio de Notificaciones Firebase

**Clase:** `App\Services\FirebaseNotificationService`

**Métodos disponibles:**

```php
// Enviar a un token específico
$service->sendToToken($token, $userName, $years);

// Enviar a múltiples tokens
$service->sendToMultipleTokens($tokens, $userName, $years);

// Enviar a todos los tokens registrados
$service->sendToAllTokens($userName, $years);

// Enviar a tokens de un familiar específico
$service->sendToFamiliar($familiarId, $userName, $years);

// Enviar notificación personalizada
$service->sendCustomNotification($token, $title, $body, $data);
```

### 3. Comando Artisan de Notificaciones

**Comando:** `php artisan birthdays:send-notifications`

**Función:**
- Busca familiares que cumplen años mañana
- Filtra solo los que tienen `notificar = true`
- Envía notificación push a todos los dispositivos registrados
- Registra estadísticas de envío (exitosos/fallidos)
- Limpia automáticamente tokens inválidos

**Programación:**
- Se ejecuta automáticamente todos los días a las 8:00 AM
- Zona horaria: `America/Lima`
- Configurado en `app/Console/Kernel.php`

### 4. Base de Datos

**Tabla:** `fcm_tokens`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | BIGINT | ID autoincremental |
| `familiar_id` | BIGINT NULL | ID del familiar (opcional) |
| `token` | VARCHAR | Token FCM único |
| `device_type` | VARCHAR | `android` o `ios` |
| `last_used_at` | TIMESTAMP | Última vez usado |
| `created_at` | TIMESTAMP | Fecha de creación |
| `updated_at` | TIMESTAMP | Última actualización |

**Índices:**
- `familiar_id` (para búsquedas por familiar)
- `token` (para búsquedas rápidas)

**Relaciones:**
- Foreign key: `familiar_id` → `familiares.id` (CASCADE)

---

## 🚀 Cómo Usar

### Configuración Inicial (Solo una vez)

1. **Descargar credenciales de Firebase:**
   ```bash
   # Ir a Firebase Console → Configuración → Cuentas de servicio
   # Descargar el archivo JSON
   ```

2. **Colocar credenciales:**
   ```bash
   # Copiar el archivo a:
   cp cumpleapp-firebase-adminsdk-xxxxx.json storage/app/firebase/firebase-credentials.json
   ```

3. **Ejecutar migración:**
   ```bash
   php artisan migrate
   ```

4. **Verificar instalación:**
   ```bash
   php artisan birthdays:send-notifications
   ```

### Uso Diario (Automático)

El sistema enviará notificaciones automáticamente todos los días a las 8:00 AM para cumpleaños que sean al día siguiente.

### Uso Manual

**Probar notificación:**
```bash
php artisan tinker
```

```php
use App\Services\FirebaseNotificationService;

$service = new FirebaseNotificationService();

// Obtener un token de prueba
$token = DB::table('fcm_tokens')->first()->token ?? null;

if ($token) {
    $service->sendToToken($token, 'Juan Pérez', '25');
    echo "✅ Notificación enviada\n";
} else {
    echo "❌ No hay tokens registrados\n";
}
```

**Ver tokens registrados:**
```bash
php artisan tinker
```

```php
DB::table('fcm_tokens')->get();
```

**Limpiar tokens antiguos:**
```php
// Eliminar tokens no usados en 30 días
DB::table('fcm_tokens')
    ->where('last_used_at', '<', now()->subDays(30))
    ->delete();
```

---

## 🔄 Integración con React Native

### En tu proyecto React Native:

1. **Instalar dependencias:**
   ```bash
   npm install @react-native-firebase/app @react-native-firebase/messaging
   ```

2. **Configurar `google-services.json`** (Android)

3. **Obtener y enviar token:**
   ```typescript
   import messaging from '@react-native-firebase/messaging';
   import axios from 'axios';

   // Obtener token
   const token = await messaging().getToken();

   // Enviar al backend
   await axios.post('https://tu-api.com/api/v1/fcm-token', {
     token: token,
     device_type: 'android',
   });
   ```

Ver guía completa en `FIREBASE_SETUP.md` → Sección "Configuración en React Native"

---

## 📊 Monitoreo y Logs

### Ver logs de Firebase:

```bash
tail -f storage/logs/laravel.log | grep Firebase
```

### Logs importantes:

- ✅ `Servicio de Firebase inicializado correctamente`
- ✅ `Notificación enviada a token: xxx`
- ⚠️ `No hay tokens FCM registrados`
- ⚠️ `Token inválido eliminado: xxx`
- ❌ `Error enviando notificación: xxx`

### Estadísticas de envío:

```php
// En el comando birthdays:send-notifications
📊 Resumen:
   Total enviadas: 5
   Total fallidas: 0
```

---

## 🛠️ Solución de Problemas Comunes

### ❌ "Credenciales de Firebase no configuradas"

**Solución:**
```bash
# Verificar que el archivo existe
ls -la storage/app/firebase/firebase-credentials.json

# Si no existe, copiarlo desde Firebase Console
```

### ❌ "Permission denied"

**Solución:**
```bash
# Linux/Mac
chmod -R 775 storage/app/firebase

# Windows
icacls storage\app\firebase /grant Users:M
```

### ❌ "Invalid service account credentials"

**Solución:**
- Descargar nuevamente las credenciales desde Firebase Console
- Verificar que el archivo JSON esté completo y sea válido

### ⚠️ "No se reciben notificaciones en el dispositivo"

**Verificar:**
1. Token registrado en la BD:
   ```sql
   SELECT * FROM fcm_tokens;
   ```

2. Permisos de notificación en el dispositivo

3. App no esté en modo ahorro de batería

4. Credenciales correctas en Firebase Console

---

## 📚 Documentación Adicional

- **`FIREBASE_SETUP.md`**: Guía completa paso a paso de configuración
- **`storage/app/firebase/README.md`**: Instrucciones del directorio de credenciales
- **`ESTRUCTURA_PROYECTO.md`**: Documentación general del proyecto

---

## 🎯 Próximos Pasos

1. [ ] Descargar credenciales de Firebase Console
2. [ ] Colocar credenciales en `storage/app/firebase/firebase-credentials.json`
3. [ ] Ejecutar migración: `php artisan migrate`
4. [ ] Probar comando: `php artisan birthdays:send-notifications`
5. [ ] Configurar app React Native (si aplica)
6. [ ] Probar notificación end-to-end
7. [ ] Configurar cron job en servidor de producción

---

## ✨ Características Especiales

### Limpieza Automática de Tokens

El sistema elimina automáticamente tokens inválidos cuando:
- Firebase responde con error "not-found"
- Firebase responde con error "invalid-registration-token"

### Actualización de Uso

Cada vez que se envía una notificación exitosamente, se actualiza el campo `last_used_at` del token.

### Notificaciones Personalizadas

Puedes enviar notificaciones personalizadas usando el método `sendCustomNotification()`:

```php
$service->sendCustomNotification(
    $token,
    '🎉 Título personalizado',
    'Mensaje personalizado',
    ['data1' => 'valor1', 'data2' => 'valor2']
);
```

### Asociación con Familiares

Los tokens pueden asociarse opcionalmente a un familiar específico, permitiendo enviar notificaciones solo a ciertos usuarios.

---

**Última actualización:** 22 de diciembre de 2025  
**Versión:** 1.0  
**Estado:** Backend Completo - Pendiente Configuración

