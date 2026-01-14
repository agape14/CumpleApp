# 🔥 Configuración Completa de Firebase Cloud Messaging en CumpleApp

## 📋 Índice

1. [Requisitos Previos](#requisitos-previos)
2. [Configuración de Firebase Console](#configuración-de-firebase-console)
3. [Configuración en Laravel](#configuración-en-laravel)
4. [Configuración en React Native](#configuración-en-react-native)
5. [Pruebas](#pruebas)
6. [Comandos Disponibles](#comandos-disponibles)
7. [Solución de Problemas](#solución-de-problemas)

---

## 📋 Requisitos Previos

- ✅ Laravel 11 instalado
- ✅ Firebase Admin SDK instalado (`composer require kreait/firebase-php`)
- ✅ Proyecto Firebase creado
- ✅ App Android/iOS configurada en Firebase

---

## 🔧 Configuración de Firebase Console

### 1. Crear/Acceder al Proyecto Firebase

1. Ve a [Firebase Console](https://console.firebase.google.com/)
2. Crea un nuevo proyecto o selecciona uno existente
3. Nombra tu proyecto: **CumpleApp**

### 2. Configurar la App Android

1. En la página principal del proyecto, haz clic en **Android** (icono de Android)
2. Completa los datos:
   - **Nombre del paquete de Android**: `com.cumpleapp` (o el que uses en tu app)
   - **Apodo de la app** (opcional): CumpleApp
   - **Certificado de firma SHA-1** (opcional para desarrollo)
3. Descarga el archivo `google-services.json`
4. Guarda este archivo, lo necesitarás para React Native

### 3. Habilitar Cloud Messaging

1. En el menú lateral, ve a **Build** → **Cloud Messaging**
2. Verifica que esté habilitado
3. Anota el **Server Key** (lo necesitarás para pruebas manuales)

### 4. Generar Credenciales para el Backend

1. Haz clic en el icono de engranaje ⚙️ → **Configuración del proyecto**
2. Ve a la pestaña **Cuentas de servicio**
3. Asegúrate de que la opción **Admin SDK** esté seleccionada
4. Haz clic en **Generar nueva clave privada**
5. Confirma haciendo clic en **Generar clave**
6. Se descargará un archivo JSON (ej: `cumpleapp-firebase-adminsdk-xxxxx.json`)

⚠️ **IMPORTANTE**: Guarda este archivo de forma segura, contiene credenciales sensibles.

---

## 🔧 Configuración en Laravel

### 1. Colocar las Credenciales

1. Copia el archivo JSON descargado a:
   ```
   storage/app/firebase/firebase-credentials.json
   ```

2. Puedes usar el archivo de ejemplo como referencia:
   ```
   storage/app/firebase/firebase-credentials.example.json
   ```

### 2. Verificar Permisos del Directorio

**En Linux/Mac:**
```bash
chmod -R 775 storage/app/firebase
chown -R www-data:www-data storage/app/firebase
```

**En Windows (PowerShell como Administrador):**
```powershell
icacls storage\app\firebase /grant Users:M
```

### 3. Ejecutar la Migración

```bash
php artisan migrate
```

Esto creará la tabla `fcm_tokens` con la siguiente estructura:
- `id`: ID único
- `familiar_id`: ID del familiar asociado (nullable)
- `token`: Token FCM único
- `device_type`: Tipo de dispositivo (android/ios)
- `last_used_at`: Última vez que se usó
- `created_at`, `updated_at`: Timestamps

### 4. Verificar la Instalación

```bash
php artisan birthdays:send-notifications
```

Si todo está bien configurado, verás:
```
🔍 Buscando cumpleaños que son mañana...
ℹ️  No hay cumpleaños mañana.
```

Si hay un error, revisa la sección de [Solución de Problemas](#solución-de-problemas).

---

## 📱 Configuración en React Native

### 1. Instalar Dependencias

```bash
# En tu proyecto React Native
npm install @react-native-firebase/app @react-native-firebase/messaging
# O con yarn
yarn add @react-native-firebase/app @react-native-firebase/messaging
```

### 2. Configurar Android

1. Copia `google-services.json` a:
   ```
   android/app/google-services.json
   ```

2. Edita `android/build.gradle`:
   ```gradle
   buildscript {
     dependencies {
       // Agregar esta línea
       classpath 'com.google.gms:google-services:4.4.0'
     }
   }
   ```

3. Edita `android/app/build.gradle`:
   ```gradle
   // Al final del archivo
   apply plugin: 'com.google.gms.google-services'
   ```

### 3. Crear Servicio de FCM Token

Crea el archivo `src/services/fcmTokenService.ts`:

```typescript
import axios from 'axios';

const API_BASE_URL = 'https://tu-servidor.com/api/v1';

export const sendFcmTokenToBackend = async (token: string, familiarId?: number) => {
  try {
    const response = await axios.post(`${API_BASE_URL}/fcm-token`, {
      token: token,
      device_type: 'android',
      familiar_id: familiarId, // Opcional
    });
    
    console.log('✅ Token FCM enviado al backend:', response.data);
    return true;
  } catch (error) {
    console.error('❌ Error enviando token FCM:', error);
    return false;
  }
};

export const removeFcmTokenFromBackend = async (token: string) => {
  try {
    const response = await axios.delete(`${API_BASE_URL}/fcm-token`, {
      data: { token }
    });
    
    console.log('✅ Token FCM eliminado del backend:', response.data);
    return true;
  } catch (error) {
    console.error('❌ Error eliminando token FCM:', error);
    return false;
  }
};
```

### 4. Integrar en App.tsx

```typescript
import React, { useEffect, useState } from 'react';
import messaging from '@react-native-firebase/messaging';
import { sendFcmTokenToBackend } from './src/services/fcmTokenService';

function App() {
  const [fcmToken, setFcmToken] = useState<string | null>(null);

  useEffect(() => {
    // Solicitar permisos
    const requestPermission = async () => {
      const authStatus = await messaging().requestPermission();
      const enabled =
        authStatus === messaging.AuthorizationStatus.AUTHORIZED ||
        authStatus === messaging.AuthorizationStatus.PROVISIONAL;

      if (enabled) {
        console.log('✅ Permisos de notificaciones concedidos');
        getFCMToken();
      }
    };

    // Obtener token FCM
    const getFCMToken = async () => {
      try {
        const token = await messaging().getToken();
        console.log('📱 FCM Token:', token);
        setFcmToken(token);
        
        // Enviar al backend
        await sendFcmTokenToBackend(token);
      } catch (error) {
        console.error('❌ Error obteniendo FCM token:', error);
      }
    };

    requestPermission();

    // Listener para actualizaciones del token
    const unsubscribe = messaging().onTokenRefresh(async (token) => {
      console.log('🔄 Token FCM actualizado:', token);
      setFcmToken(token);
      await sendFcmTokenToBackend(token);
    });

    // Listener para notificaciones en primer plano
    const unsubscribeForeground = messaging().onMessage(async (remoteMessage) => {
      console.log('📩 Notificación recibida en primer plano:', remoteMessage);
      // Aquí puedes mostrar una notificación local o un modal
    });

    return () => {
      unsubscribe();
      unsubscribeForeground();
    };
  }, []);

  return (
    // Tu componente App
  );
}

export default App;
```

### 5. Configurar Notificaciones en Background

Crea el archivo `index.js` en la raíz (si no existe):

```javascript
import { AppRegistry } from 'react-native';
import messaging from '@react-native-firebase/messaging';
import App from './App';
import { name as appName } from './app.json';

// Handler para notificaciones en background
messaging().setBackgroundMessageHandler(async (remoteMessage) => {
  console.log('📩 Notificación recibida en background:', remoteMessage);
});

AppRegistry.registerComponent(appName, () => App);
```

---

## 🧪 Pruebas

### 1. Probar el Envío de Token

1. Ejecuta la app React Native
2. Verifica en la consola que se obtenga el token
3. Verifica en la base de datos que el token se haya guardado:

```sql
SELECT * FROM fcm_tokens;
```

### 2. Probar Notificación Manual

Desde Firebase Console:

1. Ve a **Cloud Messaging**
2. Haz clic en **Enviar mensaje de prueba**
3. Pega el token FCM de tu dispositivo
4. Envía el mensaje

### 3. Probar Comando de Notificaciones

```bash
php artisan birthdays:send-notifications
```

### 4. Probar con Tinker

```bash
php artisan tinker
```

```php
use App\Services\FirebaseNotificationService;

$service = new FirebaseNotificationService();

// Obtener un token de prueba
$token = DB::table('fcm_tokens')->first()->token;

// Enviar notificación
$service->sendToToken($token, 'Juan Pérez', '25');
```

---

## 📝 Comandos Disponibles

### Enviar Notificaciones de Cumpleaños

```bash
php artisan birthdays:send-notifications
```

Busca cumpleaños que son mañana y envía notificaciones push a todos los dispositivos registrados.

### Ver Tokens Registrados

```bash
php artisan tinker
```

```php
DB::table('fcm_tokens')->get();
```

### Limpiar Tokens Antiguos

```bash
php artisan tinker
```

```php
// Eliminar tokens no usados en los últimos 30 días
DB::table('fcm_tokens')
  ->where('last_used_at', '<', now()->subDays(30))
  ->delete();
```

---

## 🔄 Configuración del Cron Job

### En el Servidor (Producción)

Edita el crontab:

```bash
crontab -e
```

Agrega esta línea:

```bash
0 8 * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

### Verificar que el Schedule Esté Configurado

El archivo `app/Console/Kernel.php` ya tiene configurado:

```php
protected function schedule(Schedule $schedule): void
{
    // Enviar notificaciones push de cumpleaños todos los días a las 8:00 AM
    $schedule->command('birthdays:send-notifications')
        ->dailyAt('08:00')
        ->timezone('America/Lima');
}
```

### En Desarrollo Local

```bash
# Ejecutar el scheduler cada minuto
php artisan schedule:work
```

---

## 🔍 Solución de Problemas

### Error: "Credenciales de Firebase no configuradas"

**Causa**: El archivo de credenciales no existe o tiene un nombre incorrecto.

**Solución**:
1. Verifica que el archivo existe:
   ```bash
   ls -la storage/app/firebase/
   ```
2. El nombre debe ser exactamente: `firebase-credentials.json`
3. Copia el archivo de ejemplo y reemplaza con tus credenciales:
   ```bash
   cp storage/app/firebase/firebase-credentials.example.json storage/app/firebase/firebase-credentials.json
   ```

### Error: "Permission denied"

**Causa**: El servidor web no tiene permisos para leer el archivo.

**Solución**:
```bash
# Linux/Mac
sudo chmod -R 775 storage/app/firebase
sudo chown -R www-data:www-data storage/app/firebase

# Windows
icacls storage\app\firebase /grant Users:M
```

### Error: "Invalid service account credentials"

**Causa**: El archivo JSON está corrupto, incompleto o es incorrecto.

**Solución**:
1. Descarga nuevamente las credenciales desde Firebase Console
2. Verifica que el archivo JSON esté completo
3. Usa un validador JSON para verificar la sintaxis:
   ```bash
   php artisan tinker
   json_decode(file_get_contents(storage_path('app/firebase/firebase-credentials.json')));
   ```

### Error: "Requested entity was not found"

**Causa**: El token FCM es inválido o ha expirado.

**Solución**:
El sistema automáticamente elimina tokens inválidos. Si el problema persiste:
1. Desinstala y reinstala la app móvil
2. Limpia los tokens de la base de datos:
   ```sql
   TRUNCATE TABLE fcm_tokens;
   ```

### No se Reciben Notificaciones en Android

**Posibles causas y soluciones**:

1. **Permisos no concedidos**:
   - Verifica en Configuración → Apps → CumpleApp → Notificaciones
   - Asegúrate de que estén habilitadas

2. **App en modo ahorro de batería**:
   - Desactiva la optimización de batería para CumpleApp
   - Configuración → Batería → Optimización de batería

3. **Token no registrado**:
   - Verifica en la base de datos que el token existe:
     ```sql
     SELECT * FROM fcm_tokens WHERE token = 'tu-token';
     ```

4. **Credenciales incorrectas**:
   - Verifica que el `project_id` en las credenciales coincida con tu proyecto Firebase

### Notificaciones No Se Muestran en Primer Plano

**Solución**: Agrega un listener en `App.tsx`:

```typescript
messaging().onMessage(async (remoteMessage) => {
  // Mostrar notificación local
  Alert.alert(
    remoteMessage.notification?.title || 'Notificación',
    remoteMessage.notification?.body || ''
  );
});
```

---

## 📊 Estructura de la Base de Datos

### Tabla: fcm_tokens

| Campo         | Tipo          | Descripción                           |
|---------------|---------------|---------------------------------------|
| id            | BIGINT        | ID único autoincremental              |
| familiar_id   | BIGINT (null) | ID del familiar asociado              |
| token         | VARCHAR       | Token FCM único del dispositivo       |
| device_type   | VARCHAR       | Tipo de dispositivo (android/ios)     |
| last_used_at  | TIMESTAMP     | Última vez que se usó                 |
| created_at    | TIMESTAMP     | Fecha de creación                     |
| updated_at    | TIMESTAMP     | Fecha de última actualización         |

---

## 🚀 Endpoints API

### POST /api/v1/fcm-token

Registra o actualiza un token FCM.

**Request Body**:
```json
{
  "token": "fcm-token-aqui",
  "device_type": "android",
  "familiar_id": 1
}
```

**Response**:
```json
{
  "success": true,
  "message": "Token guardado correctamente"
}
```

### DELETE /api/v1/fcm-token

Elimina un token FCM.

**Request Body**:
```json
{
  "token": "fcm-token-aqui"
}
```

**Response**:
```json
{
  "success": true,
  "message": "Token eliminado correctamente"
}
```

---

## 📚 Recursos Adicionales

- [Documentación oficial de Firebase](https://firebase.google.com/docs)
- [Kreait Firebase PHP SDK](https://firebase-php.readthedocs.io/)
- [React Native Firebase](https://rnfirebase.io/)
- [Laravel Task Scheduling](https://laravel.com/docs/11.x/scheduling)

---

## ✅ Checklist de Implementación

- [ ] Proyecto Firebase creado
- [ ] App Android configurada en Firebase
- [ ] Archivo `google-services.json` descargado
- [ ] Credenciales de servicio descargadas
- [ ] Credenciales colocadas en `storage/app/firebase/firebase-credentials.json`
- [ ] Migración ejecutada (`php artisan migrate`)
- [ ] React Native configurado con Firebase
- [ ] Token FCM enviado al backend
- [ ] Token verificado en la base de datos
- [ ] Prueba manual de notificación exitosa
- [ ] Comando `birthdays:send-notifications` ejecutado correctamente
- [ ] Cron job configurado en el servidor

---

**¡Listo! 🎉** Tu aplicación ahora puede enviar notificaciones push automáticas.

