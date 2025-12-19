# 📱 Guía para Crear App Móvil de CumpleApp

## 🎯 Recomendación: React Native

### ¿Por qué React Native?

✅ **Multiplataforma**: Un solo código para iOS y Android  
✅ **Rendimiento nativo**: Casi tan rápido como apps nativas  
✅ **Gran comunidad**: Muchos recursos y librerías disponibles  
✅ **JavaScript/TypeScript**: Si ya conoces JS, es fácil aprender  
✅ **Hot Reload**: Ver cambios en tiempo real  
✅ **Integración fácil**: Se conecta perfectamente con APIs REST de Laravel  
✅ **Mantenimiento**: Un solo código base para ambas plataformas  

### Alternativas Consideradas

| Tecnología | Pros | Contras |
|------------|------|---------|
| **React Native** ⭐ | Multiplataforma, gran comunidad, fácil | Requiere aprender React |
| **Flutter** | Excelente rendimiento, UI hermosa | Requiere aprender Dart |
| **Ionic** | Web-based, fácil si conoces web | Menor rendimiento que nativo |
| **PWA** | Muy simple, no requiere stores | Limitaciones de funcionalidad |
| **Nativo (Kotlin/Swift)** | Máximo rendimiento | Dos códigos separados |

## 🏗️ Arquitectura Propuesta

```
┌─────────────────┐
│  App Móvil      │
│  React Native   │
└────────┬────────┘
         │ HTTP/HTTPS
         │ JSON API
         ▼
┌─────────────────┐
│  API REST       │
│  Laravel 11     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Base de Datos  │
│  MySQL          │
└─────────────────┘
```

## 📋 Plan de Implementación

### Fase 1: Crear API REST en Laravel (Prioridad Alta)

Necesitas convertir tus rutas web en una API REST que devuelva JSON.

#### 1.1 Instalar Laravel Sanctum (Autenticación API)

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

#### 1.2 Crear Archivo de Rutas API

Crear `routes/api.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FamiliarApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DashboardApiController;
// ... otros controladores

Route::prefix('v1')->group(function () {
    
    // Rutas públicas
    Route::post('/login', [AuthApiController::class, 'login']);
    
    // Rutas protegidas
    Route::middleware('auth:sanctum')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [DashboardApiController::class, 'index']);
        
        // Familiares
        Route::apiResource('familiares', FamiliarApiController::class);
        
        // Ideas de regalos
        Route::post('familiares/{familiar}/ideas', [IdeaRegaloApiController::class, 'store']);
        Route::put('ideas/{idea}', [IdeaRegaloApiController::class, 'update']);
        Route::delete('ideas/{idea}', [IdeaRegaloApiController::class, 'destroy']);
        
        // Relaciones familiares
        Route::get('familiares/{familiar}/relaciones', [RelacionFamiliarApiController::class, 'index']);
        Route::post('relaciones-familiares', [RelacionFamiliarApiController::class, 'store']);
        Route::delete('relaciones-familiares/{relacion}', [RelacionFamiliarApiController::class, 'destroy']);
        
        // Regalos dados
        Route::get('familiares/{familiar}/regalos-dados', [RegaloDadoApiController::class, 'index']);
        Route::post('familiares/{familiar}/regalos-dados', [RegaloDadoApiController::class, 'store']);
        
        // Recordatorios
        Route::get('familiares/{familiar}/recordatorios', [RecordatorioApiController::class, 'index']);
        Route::post('familiares/{familiar}/recordatorios', [RecordatorioApiController::class, 'store']);
        
        // Cuotas mensuales
        Route::get('cuotas-mensuales', [CuotaMensualApiController::class, 'index']);
        Route::post('cuotas-mensuales', [CuotaMensualApiController::class, 'store']);
        
        // Configuración
        Route::get('configuracion', [ConfiguracionApiController::class, 'index']);
        Route::post('configuracion', [ConfiguracionApiController::class, 'update']);
        
        // Logout
        Route::post('/logout', [AuthApiController::class, 'logout']);
    });
});
```

#### 1.3 Crear Controladores API

Los controladores API deben retornar JSON en lugar de vistas:

```php
// app/Http/Controllers/Api/FamiliarApiController.php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Familiar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FamiliarApiController extends Controller
{
    public function index(): JsonResponse
    {
        $familiares = Familiar::with('parentesco')
            ->orderBy('nombre')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $familiares
        ]);
    }
    
    public function show(Familiar $familiar): JsonResponse
    {
        $familiar->load(['parentesco', 'ideasRegalos', 'relaciones', 'regalosDados']);
        
        return response()->json([
            'success' => true,
            'data' => $familiar
        ]);
    }
    
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'fecha_nacimiento' => 'required|date|before:today',
            // ... otros campos
        ]);
        
        $familiar = Familiar::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Familiar creado exitosamente',
            'data' => $familiar
        ], 201);
    }
    
    // ... otros métodos
}
```

#### 1.4 Configurar CORS

En `config/cors.php`:

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['*'], // En producción, especificar dominios
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

### Fase 2: Configurar React Native

#### 2.1 Requisitos Previos

```bash
# Instalar Node.js (v18 o superior)
# Descargar desde: https://nodejs.org/

# Instalar React Native CLI
npm install -g react-native-cli

# Para Android: Instalar Android Studio
# Para iOS: Instalar Xcode (solo en Mac)
```

#### 2.2 Crear Proyecto React Native

```bash
# Crear nuevo proyecto
npx react-native@latest init CumpleAppMobile

cd CumpleAppMobile

# Instalar dependencias de navegación
npm install @react-navigation/native @react-navigation/stack @react-navigation/bottom-tabs
npm install react-native-screens react-native-safe-area-context
npm install react-native-gesture-handler

# Instalar para hacer peticiones HTTP
npm install axios

# Instalar para almacenamiento local
npm install @react-native-async-storage/async-storage

# Instalar para manejar fechas
npm install date-fns

# Para iOS (solo en Mac)
cd ios && pod install && cd ..
```

#### 2.3 Estructura del Proyecto React Native

```
CumpleAppMobile/
├── src/
│   ├── api/
│   │   ├── api.js              # Configuración de axios
│   │   ├── auth.js             # Endpoints de autenticación
│   │   ├── familiares.js       # Endpoints de familiares
│   │   └── ...
│   ├── screens/
│   │   ├── LoginScreen.js
│   │   ├── DashboardScreen.js
│   │   ├── FamiliaresScreen.js
│   │   ├── FamiliarDetailScreen.js
│   │   └── ...
│   ├── components/
│   │   ├── FamiliarCard.js
│   │   ├── BirthdayCard.js
│   │   └── ...
│   ├── navigation/
│   │   └── AppNavigator.js
│   ├── context/
│   │   └── AuthContext.js
│   └── utils/
│       └── constants.js
├── App.js
└── package.json
```

#### 2.4 Ejemplo de Configuración API

```javascript
// src/api/api.js
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const API_BASE_URL = 'http://tu-servidor-laravel.com/api/v1';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Interceptor para agregar token
api.interceptors.request.use(
  async (config) => {
    const token = await AsyncStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

export default api;
```

```javascript
// src/api/auth.js
import api from './api';

export const authApi = {
  login: async (dni, password) => {
    const response = await api.post('/login', { dni, password });
    return response.data;
  },
  
  logout: async () => {
    const response = await api.post('/logout');
    return response.data;
  },
};
```

```javascript
// src/api/familiares.js
import api from './api';

export const familiaresApi = {
  getAll: async () => {
    const response = await api.get('/familiares');
    return response.data;
  },
  
  getById: async (id) => {
    const response = await api.get(`/familiares/${id}`);
    return response.data;
  },
  
  create: async (data) => {
    const response = await api.post('/familiares', data);
    return response.data;
  },
  
  update: async (id, data) => {
    const response = await api.put(`/familiares/${id}`, data);
    return response.data;
  },
  
  delete: async (id) => {
    const response = await api.delete(`/familiares/${id}`);
    return response.data;
  },
};
```

#### 2.5 Ejemplo de Pantalla

```javascript
// src/screens/FamiliaresScreen.js
import React, { useState, useEffect } from 'react';
import { View, Text, FlatList, StyleSheet } from 'react-native';
import { familiaresApi } from '../api/familiares';
import FamiliarCard from '../components/FamiliarCard';

const FamiliaresScreen = () => {
  const [familiares, setFamiliares] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadFamiliares();
  }, []);

  const loadFamiliares = async () => {
    try {
      const response = await familiaresApi.getAll();
      setFamiliares(response.data);
    } catch (error) {
      console.error('Error cargando familiares:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      <FlatList
        data={familiares}
        keyExtractor={(item) => item.id.toString()}
        renderItem={({ item }) => <FamiliarCard familiar={item} />}
        refreshing={loading}
        onRefresh={loadFamiliares}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 16,
  },
});

export default FamiliaresScreen;
```

### Fase 3: Ejecutar la App

#### 3.1 Para Android

```bash
# Asegúrate de tener un emulador Android corriendo o un dispositivo conectado
npx react-native run-android
```

#### 3.2 Para iOS (solo en Mac)

```bash
# Asegúrate de tener un simulador iOS o dispositivo conectado
npx react-native run-ios
```

#### 3.3 Configurar URL del Backend

En desarrollo, necesitas cambiar la URL en `src/api/api.js`:

```javascript
// Para emulador Android
const API_BASE_URL = 'http://10.0.2.2:8000/api/v1';

// Para simulador iOS
const API_BASE_URL = 'http://localhost:8000/api/v1';

// Para dispositivo físico (usar IP de tu computadora)
const API_BASE_URL = 'http://192.168.1.100:8000/api/v1';
```

## 🔧 Configuración Adicional

### Notificaciones Push

Para notificaciones push en la app móvil:

```bash
npm install @react-native-firebase/app @react-native-firebase/messaging
```

### Cámara y Galería

Para tomar fotos de regalos:

```bash
npm install react-native-image-picker
```

### Calendario

Para integrar con calendario del dispositivo:

```bash
npm install react-native-calendar-events
```

## 📱 Funcionalidades Principales a Implementar

1. ✅ **Autenticación**: Login con DNI
2. ✅ **Dashboard**: Próximos cumpleaños, estadísticas
3. ✅ **Lista de Familiares**: Ver todos los familiares
4. ✅ **Detalle de Familiar**: Ver información completa
5. ✅ **Agregar/Editar Familiar**: Formularios
6. ✅ **Ideas de Regalos**: Ver y gestionar ideas
7. ✅ **Recordatorios**: Configurar recordatorios
8. ✅ **Cuotas Mensuales**: Ver y gestionar cuotas
9. ✅ **Notificaciones Push**: Recordatorios en el móvil

## 🚀 Pasos Siguientes

1. **Crear API REST en Laravel** (Fase 1)
2. **Configurar React Native** (Fase 2)
3. **Implementar autenticación** en la app
4. **Crear pantallas principales** (Dashboard, Familiares)
5. **Agregar funcionalidades** una por una
6. **Probar en dispositivos reales**
7. **Publicar en stores** (Google Play, App Store)

## 📚 Recursos Útiles

- **Documentación React Native**: https://reactnative.dev/docs/getting-started
- **React Navigation**: https://reactnavigation.org/
- **Axios**: https://axios-http.com/
- **Laravel Sanctum**: https://laravel.com/docs/sanctum

## ⚠️ Consideraciones Importantes

1. **Seguridad**: Usar HTTPS en producción
2. **Autenticación**: Implementar refresh tokens
3. **Caché**: Cachear datos para mejor rendimiento
4. **Offline**: Considerar modo offline con AsyncStorage
5. **Testing**: Probar en dispositivos reales, no solo emuladores
6. **Performance**: Optimizar imágenes y datos
7. **UX**: Diseño intuitivo y rápido

## 🎯 Alternativa Rápida: PWA (Progressive Web App)

Si quieres algo más rápido sin pasar por las stores:

1. Hacer tu app web responsive
2. Agregar Service Worker
3. Configurar manifest.json
4. Los usuarios pueden "instalar" desde el navegador

**Ventajas**: Rápido, no requiere stores  
**Desventajas**: Limitaciones de funcionalidad nativa

---

**¿Necesitas ayuda con algún paso específico?** Puedo ayudarte a crear los controladores API o las pantallas de React Native.

