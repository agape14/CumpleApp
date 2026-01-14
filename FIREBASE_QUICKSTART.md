# 🚀 Firebase Cloud Messaging - Quick Start

## ⚡ Inicio Rápido (5 minutos)

### 1️⃣ Obtener Credenciales (2 min)

```bash
# 1. Ir a Firebase Console
https://console.firebase.google.com/

# 2. Seleccionar proyecto → Configuración ⚙️ → Cuentas de servicio

# 3. Clic en "Generar nueva clave privada"

# 4. Descargar archivo JSON
```

### 2️⃣ Configurar Backend (1 min)

```bash
# Copiar credenciales
cp cumpleapp-firebase-adminsdk-xxxxx.json storage/app/firebase/firebase-credentials.json

# La migración ya está ejecutada ✅
```

### 3️⃣ Probar (1 min)

```bash
# Ejecutar comando de prueba
php artisan birthdays:send-notifications

# Deberías ver:
# 🔍 Buscando cumpleaños que son mañana...
# ℹ️  No hay cumpleaños mañana.
```

### 4️⃣ Registrar Token desde App Móvil (1 min)

```bash
# Desde tu app React Native, hacer POST:
POST http://tu-servidor.com/api/v1/fcm-token
Content-Type: application/json

{
  "token": "fcm-token-del-dispositivo",
  "device_type": "android"
}
```

---

## ✅ Verificación Rápida

```bash
# ¿Está la tabla creada?
php artisan migrate:status | grep fcm_tokens
# ✅ Debe mostrar: [X] Ran

# ¿Están las rutas?
php artisan route:list --path=api/v1/fcm
# ✅ Debe mostrar: POST y DELETE /api/v1/fcm-token

# ¿Está el comando?
php artisan list | grep birthdays:send-notifications
# ✅ Debe aparecer en la lista

# ¿Hay tokens registrados?
php artisan tinker
>>> DB::table('fcm_tokens')->count();
# ✅ Debe retornar un número (0 si no hay tokens aún)
```

---

## 📱 Integración React Native (Opcional)

### Instalar

```bash
npm install @react-native-firebase/app @react-native-firebase/messaging
```

### Configurar

```typescript
// App.tsx
import messaging from '@react-native-firebase/messaging';
import axios from 'axios';

// Obtener token
const token = await messaging().getToken();

// Enviar al backend
await axios.post('https://api.cumpleapp.com/api/v1/fcm-token', {
  token: token,
  device_type: 'android',
});
```

---

## 🧪 Prueba Manual

```bash
php artisan tinker
```

```php
use App\Services\FirebaseNotificationService;

// Crear servicio
$service = new FirebaseNotificationService();

// Obtener token de prueba (si existe)
$token = DB::table('fcm_tokens')->first()->token ?? null;

if ($token) {
    // Enviar notificación
    $service->sendToToken($token, 'Juan Pérez', '25');
    echo "✅ Notificación enviada\n";
} else {
    echo "⚠️ No hay tokens registrados. Registra uno desde la app móvil.\n";
}
```

---

## 🔄 Flujo Automático

```
┌─────────────────────────────────────────────────────┐
│  Cron Job (Diario 8:00 AM)                         │
└─────────────────┬───────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────┐
│  php artisan birthdays:send-notifications           │
└─────────────────┬───────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────┐
│  ¿Hay cumpleaños mañana?                            │
└─────────────────┬───────────────────────────────────┘
                  │
         ┌────────┴────────┐
         │                 │
         ▼                 ▼
    ┌────────┐        ┌─────────┐
    │   SÍ   │        │   NO    │
    └────┬───┘        └────┬────┘
         │                 │
         ▼                 ▼
┌─────────────────┐   ┌─────────────┐
│ Obtener tokens  │   │  Finalizar  │
│ de BD           │   └─────────────┘
└────────┬────────┘
         │
         ▼
┌─────────────────────────────────────────────────────┐
│  Enviar notificación push a cada dispositivo        │
└─────────────────┬───────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────┐
│  📱 Usuario recibe notificación en su móvil         │
└─────────────────────────────────────────────────────┘
```

---

## 📊 Estado Actual

| Componente | Estado |
|------------|--------|
| Backend Laravel | ✅ Completo |
| Base de Datos | ✅ Migrada |
| API REST | ✅ Funcionando |
| Servicio Firebase | ✅ Implementado |
| Comando Artisan | ✅ Funcionando |
| Cron Job | ✅ Configurado |
| Documentación | ✅ Completa |
| Credenciales | ⚠️ Pendiente |
| App Móvil | ⚠️ Opcional |

---

## 🆘 Problemas Comunes

### ❌ "Credenciales de Firebase no configuradas"

```bash
# Solución:
ls storage/app/firebase/firebase-credentials.json
# Si no existe, descargarlo de Firebase Console
```

### ❌ "Permission denied"

```bash
# Linux/Mac
chmod -R 775 storage/app/firebase

# Windows
icacls storage\app\firebase /grant Users:M
```

### ⚠️ "No se reciben notificaciones"

1. Verificar token en BD:
   ```sql
   SELECT * FROM fcm_tokens;
   ```

2. Verificar permisos de la app móvil

3. Verificar credenciales en Firebase Console

---

## 📚 Más Información

- **Guía Completa**: [FIREBASE_SETUP.md](FIREBASE_SETUP.md)
- **Comandos Útiles**: [COMANDOS_FIREBASE.md](COMANDOS_FIREBASE.md)
- **Estado**: [INSTALACION_FIREBASE_COMPLETADA.md](INSTALACION_FIREBASE_COMPLETADA.md)
- **Índice**: [FIREBASE_README.md](FIREBASE_README.md)

---

## 🎯 Checklist Mínimo

- [ ] Descargar credenciales de Firebase Console
- [ ] Colocar en `storage/app/firebase/firebase-credentials.json`
- [ ] Ejecutar `php artisan birthdays:send-notifications`
- [ ] Ver mensaje sin errores
- [ ] (Opcional) Configurar app móvil
- [ ] (Opcional) Registrar token de prueba
- [ ] (Opcional) Enviar notificación de prueba

---

**¿Listo en 5 minutos? ¡Sí! 🚀**

**Siguiente paso**: Descargar credenciales → [FIREBASE_SETUP.md](FIREBASE_SETUP.md)

