# 🔥 Comandos Útiles - Firebase en CumpleApp

## 📋 Comandos Artisan

### Notificaciones Push

```bash
# Enviar notificaciones de cumpleaños (mañana)
php artisan birthdays:send-notifications

# Ver ayuda del comando
php artisan help birthdays:send-notifications
```

### Verificar Configuración

```bash
# Ver estado de las migraciones
php artisan migrate:status

# Ejecutar migraciones pendientes
php artisan migrate

# Ver lista de comandos disponibles
php artisan list
```

### Scheduler (Tareas Programadas)

```bash
# Ejecutar el scheduler manualmente (una vez)
php artisan schedule:run

# Ejecutar el scheduler en modo watch (desarrollo)
php artisan schedule:work

# Ver lista de tareas programadas
php artisan schedule:list
```

---

## 🗄️ Consultas de Base de Datos

### Ver Tokens Registrados

```bash
php artisan tinker
```

```php
// Ver todos los tokens
DB::table('fcm_tokens')->get();

// Ver solo los tokens activos (usados recientemente)
DB::table('fcm_tokens')
    ->where('last_used_at', '>', now()->subDays(7))
    ->get();

// Contar tokens por tipo de dispositivo
DB::table('fcm_tokens')
    ->select('device_type', DB::raw('COUNT(*) as total'))
    ->groupBy('device_type')
    ->get();

// Ver tokens asociados a un familiar
DB::table('fcm_tokens')
    ->where('familiar_id', 1)
    ->get();
```

### Limpiar Tokens Antiguos

```php
// Eliminar tokens no usados en 30 días
$deleted = DB::table('fcm_tokens')
    ->where('last_used_at', '<', now()->subDays(30))
    ->delete();

echo "Tokens eliminados: {$deleted}\n";

// Eliminar tokens huérfanos (familiar eliminado)
DB::table('fcm_tokens')
    ->whereNotNull('familiar_id')
    ->whereNotIn('familiar_id', DB::table('familiares')->pluck('id'))
    ->delete();
```

### Insertar Token de Prueba

```php
DB::table('fcm_tokens')->insert([
    'token' => 'TOKEN_DE_PRUEBA_AQUI',
    'device_type' => 'android',
    'familiar_id' => null,
    'last_used_at' => now(),
    'created_at' => now(),
    'updated_at' => now(),
]);
```

---

## 🧪 Pruebas con Tinker

### Enviar Notificación de Prueba

```bash
php artisan tinker
```

```php
use App\Services\FirebaseNotificationService;

// Crear instancia del servicio
$service = new FirebaseNotificationService();

// Obtener un token de prueba
$token = DB::table('fcm_tokens')->first()->token ?? null;

if ($token) {
    // Enviar notificación de prueba
    $result = $service->sendToToken($token, 'Juan Pérez', '25');
    
    if ($result) {
        echo "✅ Notificación enviada correctamente\n";
    } else {
        echo "❌ Error al enviar notificación\n";
    }
} else {
    echo "⚠️ No hay tokens registrados\n";
}
```

### Enviar a Todos los Tokens

```php
use App\Services\FirebaseNotificationService;

$service = new FirebaseNotificationService();

$result = $service->sendToAllTokens('María López', '30');

echo "📊 Resultados:\n";
echo "   Enviadas: {$result['success']}\n";
echo "   Fallidas: {$result['failed']}\n";
echo "   Total: {$result['total']}\n";
```

### Enviar Notificación Personalizada

```php
use App\Services\FirebaseNotificationService;

$service = new FirebaseNotificationService();

$token = DB::table('fcm_tokens')->first()->token;

$service->sendCustomNotification(
    $token,
    '🎂 Recordatorio',
    'No olvides comprar el pastel',
    [
        'type' => 'reminder',
        'action' => 'open_app',
    ]
);
```

### Simular Cumpleaños de Mañana

```php
use Carbon\Carbon;

// Ver familiares que cumplen años mañana
$tomorrow = Carbon::tomorrow();

$birthdays = DB::table('familiares')
    ->whereRaw('MONTH(fecha_nacimiento) = ?', [$tomorrow->month])
    ->whereRaw('DAY(fecha_nacimiento) = ?', [$tomorrow->day])
    ->where('notificar', true)
    ->get();

echo "Cumpleaños mañana ({$tomorrow->format('d/m/Y')}): {$birthdays->count()}\n";

foreach ($birthdays as $birthday) {
    echo "  - {$birthday->nombre}\n";
}
```

---

## 📊 Monitoreo y Logs

### Ver Logs en Tiempo Real

```bash
# Ver todos los logs
tail -f storage/logs/laravel.log

# Filtrar solo logs de Firebase
tail -f storage/logs/laravel.log | grep -i firebase

# Filtrar solo errores
tail -f storage/logs/laravel.log | grep -i error

# Filtrar logs de notificaciones
tail -f storage/logs/laravel.log | grep -i "notificación\|notification"
```

### Limpiar Logs

```bash
# Limpiar logs antiguos
echo "" > storage/logs/laravel.log

# O eliminar el archivo
rm storage/logs/laravel.log

# Laravel creará uno nuevo automáticamente
```

### Ver Logs Desde Tinker

```php
// Ver últimas 10 líneas del log
$log = file_get_contents(storage_path('logs/laravel.log'));
$lines = explode("\n", $log);
$lastLines = array_slice($lines, -10);
echo implode("\n", $lastLines);
```

---

## 🔧 Mantenimiento

### Verificar Integridad de la Base de Datos

```bash
php artisan tinker
```

```php
// Verificar tokens duplicados
$duplicates = DB::table('fcm_tokens')
    ->select('token', DB::raw('COUNT(*) as count'))
    ->groupBy('token')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->isEmpty()) {
    echo "✅ No hay tokens duplicados\n";
} else {
    echo "⚠️ Tokens duplicados encontrados:\n";
    foreach ($duplicates as $dup) {
        echo "  - {$dup->token} ({$dup->count} veces)\n";
    }
}
```

### Actualizar Tokens Antiguos

```php
// Actualizar last_used_at de todos los tokens
DB::table('fcm_tokens')->update([
    'last_used_at' => now()
]);

// Marcar tokens inactivos
DB::table('fcm_tokens')
    ->where('last_used_at', '<', now()->subDays(30))
    ->update(['device_type' => 'inactive_' . DB::raw('device_type')]);
```

### Optimizar Tablas

```bash
# Desde la terminal
mysql -u usuario -p nombre_base_datos -e "OPTIMIZE TABLE fcm_tokens;"

# O desde tinker
DB::statement('OPTIMIZE TABLE fcm_tokens');
```

---

## 🚀 Comandos de Producción

### Configurar Cron Job

```bash
# Editar crontab
crontab -e

# Agregar esta línea (ejecuta el scheduler cada minuto)
* * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

### Verificar Cron Job

```bash
# Ver cron jobs configurados
crontab -l

# Ver logs del cron
grep CRON /var/log/syslog

# O en algunos sistemas
tail -f /var/log/cron
```

### Ejecutar Comando en Background

```bash
# Con nohup
nohup php artisan birthdays:send-notifications > /dev/null 2>&1 &

# O como servicio systemd
sudo systemctl start cumpleapp-notifications
```

---

## 🐛 Debug y Troubleshooting

### Verificar Conexión con Firebase

```bash
php artisan tinker
```

```php
use Kreait\Firebase\Factory;

try {
    $credentialsPath = storage_path('app/firebase/firebase-credentials.json');
    
    if (!file_exists($credentialsPath)) {
        echo "❌ Archivo de credenciales no encontrado\n";
        exit;
    }
    
    $factory = (new Factory)->withServiceAccount($credentialsPath);
    $messaging = $factory->createMessaging();
    
    echo "✅ Conexión con Firebase exitosa\n";
    echo "Credenciales: {$credentialsPath}\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
```

### Verificar Formato de Token

```php
$token = 'TU_TOKEN_AQUI';

if (strlen($token) < 50) {
    echo "⚠️ Token muy corto, posiblemente inválido\n";
} else if (preg_match('/^[a-zA-Z0-9_-]+$/', $token)) {
    echo "✅ Formato de token válido\n";
} else {
    echo "❌ Formato de token inválido\n";
}
```

### Test End-to-End

```bash
php artisan tinker
```

```php
use App\Services\FirebaseNotificationService;

echo "🧪 Iniciando test completo de Firebase...\n\n";

// 1. Verificar servicio
try {
    $service = new FirebaseNotificationService();
    echo "✅ Servicio inicializado\n";
} catch (\Exception $e) {
    echo "❌ Error al inicializar servicio: " . $e->getMessage() . "\n";
    exit;
}

// 2. Verificar tokens en BD
$tokenCount = DB::table('fcm_tokens')->count();
echo "📊 Tokens registrados: {$tokenCount}\n";

if ($tokenCount === 0) {
    echo "⚠️ No hay tokens registrados. Registra un token desde la app móvil.\n";
    exit;
}

// 3. Obtener token de prueba
$token = DB::table('fcm_tokens')->first()->token;
echo "🎯 Token de prueba: " . substr($token, 0, 20) . "...\n";

// 4. Enviar notificación
echo "📤 Enviando notificación de prueba...\n";
$result = $service->sendToToken($token, 'Test User', '99');

if ($result) {
    echo "✅ ¡Test exitoso! Verifica tu dispositivo.\n";
} else {
    echo "❌ Error al enviar notificación. Revisa los logs.\n";
}
```

---

## 📝 Scripts Útiles

### Script de Verificación de Salud

Crea un archivo `check-firebase.php` en la raíz:

```php
<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use App\Services\FirebaseNotificationService;

echo "🏥 Verificación de Salud - Firebase\n";
echo str_repeat("=", 50) . "\n\n";

// 1. Credenciales
$credentialsPath = storage_path('app/firebase/firebase-credentials.json');
echo "📁 Credenciales: ";
echo file_exists($credentialsPath) ? "✅ Existen\n" : "❌ No encontradas\n";

// 2. Base de datos
echo "🗄️  Tabla fcm_tokens: ";
try {
    $count = DB::table('fcm_tokens')->count();
    echo "✅ Existe ({$count} tokens)\n";
} catch (\Exception $e) {
    echo "❌ Error\n";
}

// 3. Servicio Firebase
echo "🔥 Servicio Firebase: ";
try {
    $service = new FirebaseNotificationService();
    echo "✅ Inicializado\n";
} catch (\Exception $e) {
    echo "❌ Error\n";
}

// 4. Comando disponible
echo "⚙️  Comando artisan: ";
$output = shell_exec('php artisan list | grep birthdays:send-notifications');
echo $output ? "✅ Disponible\n" : "❌ No encontrado\n";

echo "\n" . str_repeat("=", 50) . "\n";
echo "✨ Verificación completada\n";
```

Ejecutar:
```bash
php check-firebase.php
```

---

## 📚 Comandos de Ayuda

```bash
# Ver todas las rutas API
php artisan route:list --path=api

# Ver todos los comandos artisan
php artisan list

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Optimizar aplicación
php artisan optimize

# Ver información del sistema
php artisan about

# Ver versión de Laravel
php artisan --version
```

---

**Tip:** Puedes crear alias para comandos frecuentes en tu `.bashrc` o `.zshrc`:

```bash
alias fcm-notify="php artisan birthdays:send-notifications"
alias fcm-tokens="php artisan tinker --execute='DB::table(\"fcm_tokens\")->get()'"
alias fcm-test="php check-firebase.php"
```

---

**Última actualización:** 22 de diciembre de 2025

