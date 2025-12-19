# 🚀 Guía para Levantar el Proyecto React Native

## ⚠️ Problema Actual

El proyecto React Native necesita crearse desde cero porque faltan las carpetas nativas (`android/`, `ios/`) y archivos de configuración (`metro.config.js`, etc.).

## 📋 Solución: Crear el Proyecto Correctamente

### Opción 1: Crear Proyecto Nuevo y Copiar Archivos (Recomendado)

#### Paso 1: Crear el Proyecto React Native

```bash
# Salir de la carpeta actual
cd c:\laragon\www\CumpleApp

# Crear nuevo proyecto React Native (en una ubicación temporal)
npx react-native@latest init CumpleAppMobileTemp --template react-native-template-typescript

# O si prefieres JavaScript puro:
npx react-native@latest init CumpleAppMobileTemp
```

#### Paso 2: Copiar Archivos del Proyecto Actual

```bash
# Copiar los archivos que creamos
# Desde react-native-app/src/ a CumpleAppMobileTemp/src/
```

**O mejor aún, vamos a hacerlo manualmente:**

1. **Eliminar la carpeta `react-native-app` actual** (solo tiene archivos fuente, no es un proyecto completo)
2. **Crear el proyecto nuevo** con el comando de arriba
3. **Copiar nuestros archivos** a la estructura correcta

### Opción 2: Crear Proyecto en Ubicación Independiente (Recomendado)

```bash
# Ir al directorio padre (c:\laragon\www)
cd c:\laragon\www

# Crear proyecto nuevo (independiente de CumpleApp)
# NOTA: Usar el comando actualizado (init está deprecado)
npx @react-native-community/cli@latest init CumpleAppMobile

# El proyecto se creará en: c:\laragon\www\CumpleAppMobile
# (no dentro de CumpleApp)
```

## 🔧 Pasos Completos para Levantar el Proyecto

### 1. Preparar el Entorno

```bash
# Asegúrate de tener instalado:
# - Node.js 18+
# - Java JDK 11+ (para Android)
# - Android Studio (para Android)
# - Xcode (para iOS, solo Mac)
```

### 2. Crear el Proyecto React Native

```bash
# Ir al directorio padre para crear proyecto independiente
cd c:\laragon\www

# Crear proyecto (independiente de CumpleApp):
# NOTA: El comando 'init' está deprecado, usar el nuevo comando
npx @react-native-community/cli@latest init CumpleAppMobile

# El proyecto se creará en: c:\laragon\www\CumpleAppMobile
# Esto puede tardar varios minutos...
```

### 3. Instalar Dependencias Adicionales

```bash
cd CumpleAppMobile

# Instalar todas las dependencias que necesitamos
npm install @react-navigation/native @react-navigation/stack @react-navigation/bottom-tabs
npm install react-native-screens react-native-safe-area-context
npm install react-native-gesture-handler react-native-reanimated
npm install axios @react-native-async-storage/async-storage
npm install date-fns react-native-vector-icons
npm install react-native-linear-gradient

# Para iOS (solo en Mac)
cd ios && pod install && cd ..
```

### 4. Copiar Nuestros Archivos

Necesitas copiar los archivos que creamos desde `react-native-app/src/` a `CumpleAppMobile/src/`:

**Estructura a copiar:**
```
react-native-app/src/
├── config/
│   └── api.ts
├── constants/
│   ├── colors.ts
│   └── styles.ts
├── context/
│   └── AuthContext.tsx
├── navigation/
│   └── AppNavigator.tsx
├── screens/
│   ├── LoginScreen.tsx
│   ├── DashboardScreen.tsx
│   └── FamiliaresScreen.tsx
└── services/
    ├── authService.ts
    ├── familiaresService.ts
    └── dashboardService.ts
```

**Y también:**
- `react-native-app/App.tsx` → `CumpleAppMobile/App.tsx`

### 5. Configurar la URL de la API

Editar `src/config/api.ts` y cambiar la URL según tu entorno:

```typescript
// Para Android Emulador
const API_BASE_URL = 'http://10.0.2.2:8000/api/v1';

// Para iOS Simulador
const API_BASE_URL = 'http://localhost:8000/api/v1';

// Para Dispositivo Físico (usar IP de tu computadora)
const API_BASE_URL = 'http://192.168.1.100:8000/api/v1';
```

## 📱 Ejecutar en el Celular Físico

### Para Android

#### Opción A: USB (Recomendado)

1. **Habilitar Modo Desarrollador en tu celular:**
   - Ir a Configuración → Acerca del teléfono
   - Tocar 7 veces en "Número de compilación"
   - Activar "Opciones de desarrollador"
   - Activar "Depuración USB"

2. **Conectar el celular por USB**

3. **Verificar conexión:**
   ```bash
   adb devices
   # Debe mostrar tu dispositivo
   ```

4. **Ejecutar la app:**
   ```bash
   npm run android
   ```

#### Opción B: Red Local (WiFi)

1. **Conectar celular y computadora a la misma red WiFi**

2. **Obtener IP de tu computadora:**
   ```bash
   # Windows
   ipconfig
   # Buscar "Dirección IPv4" (ej: 192.168.1.100)
   
   # Mac/Linux
   ifconfig
   # Buscar "inet" (ej: 192.168.1.100)
   ```

3. **Configurar URL en `src/config/api.ts`:**
   ```typescript
   const API_BASE_URL = 'http://TU_IP:8000/api/v1';
   // Ejemplo: 'http://192.168.1.100:8000/api/v1'
   ```

4. **Asegurarse de que Laravel permita conexiones desde la red:**
   ```bash
   # En Laravel, ejecutar en la IP de la red:
   php artisan serve --host=0.0.0.0 --port=8000
   ```

5. **Instalar la app en el celular:**
   ```bash
   # Primero, construir el APK
   cd android
   ./gradlew assembleDebug
   
   # El APK estará en: android/app/build/outputs/apk/debug/app-debug.apk
   # Copiarlo al celular e instalarlo
   ```

### Para iOS (Solo Mac)

1. **Conectar iPhone por USB**
2. **Abrir Xcode y seleccionar tu dispositivo**
3. **Ejecutar:**
   ```bash
   npm run ios
   ```

## 🔥 Método Rápido: Script de Configuración

Puedo crear un script que haga todo automáticamente. Por ahora, sigue estos pasos:

### Script Manual (Copia y pega en PowerShell)

```powershell
# 1. Ir al directorio padre (c:\laragon\www)
cd c:\laragon\www

# 2. Crear proyecto nuevo (independiente)
# NOTA: Usar el comando actualizado (init está deprecado)
npx @react-native-community/cli@latest init CumpleAppMobile

# 3. Ir al proyecto
cd CumpleAppMobile

# 4. Instalar dependencias
npm install @react-navigation/native @react-navigation/stack @react-navigation/bottom-tabs
npm install react-native-screens react-native-safe-area-context
npm install react-native-gesture-handler react-native-reanimated
npm install axios @react-native-async-storage/async-storage
npm install date-fns react-native-vector-icons
npm install react-native-linear-gradient

# 5. Copiar archivos desde CumpleApp/react-native-app/src/ a CumpleAppMobile/src/
# (Puedes hacerlo manualmente o usar el script setup-react-native.ps1)
```

## 📝 Checklist de Configuración

- [ ] Node.js 18+ instalado
- [ ] React Native CLI instalado (`npm install -g react-native-cli`)
- [ ] Android Studio instalado (para Android)
- [ ] Proyecto React Native creado
- [ ] Dependencias instaladas
- [ ] Archivos copiados desde `react-native-app/src/`
- [ ] URL de API configurada en `src/config/api.ts`
- [ ] Laravel corriendo (`php artisan serve`)
- [ ] Celular conectado o emulador corriendo
- [ ] App ejecutándose

## 🐛 Solución de Problemas

### Error: "No Metro config found"
- El proyecto no está creado correctamente
- Crear proyecto nuevo con `npx react-native@latest init`

### Error: "Android project not found"
- Faltan las carpetas `android/` e `ios/`
- Crear proyecto nuevo (no solo copiar archivos)

### Error: "Network request failed" en el celular
- Verificar que celular y PC estén en la misma red WiFi
- Verificar que la IP en `api.ts` sea correcta
- Verificar que Laravel esté corriendo con `--host=0.0.0.0`
- Verificar firewall de Windows

### Error: "Cannot connect to Metro"
- Ejecutar `npm start` en una terminal
- En otra terminal, ejecutar `npm run android`

## 🎯 Resumen Rápido

1. **Crear proyecto:** `npx react-native@latest init CumpleAppMobile`
2. **Instalar dependencias:** Ver lista arriba
3. **Copiar archivos:** Desde `react-native-app/src/` a `CumpleAppMobile/src/`
4. **Configurar API:** Editar `src/config/api.ts`
5. **Ejecutar:** `npm run android` (con celular conectado o emulador)

---

**¿Necesitas ayuda con algún paso específico?** Puedo crear un script automatizado o ayudarte con la configuración paso a paso.

