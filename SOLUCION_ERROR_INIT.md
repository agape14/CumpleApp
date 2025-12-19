# 🔧 Solución: Error "init command is deprecated"

## ❌ Problema

El comando `npx react-native@latest init` está **deprecado** y ya no funciona.

```
🚨️ The `init` command is deprecated.
- Switch to npx @react-native-community/cli init for the identical behavior.
```

## ✅ Solución

Usar el nuevo comando recomendado:

```bash
npx @react-native-community/cli@latest init CumpleAppMobile
```

## 📋 Comando Correcto Completo

```powershell
# 1. Ir al directorio padre
cd c:\laragon\www

# 2. Crear proyecto con el comando actualizado
npx @react-native-community/cli@latest init CumpleAppMobile

# Esto creará el proyecto en: c:\laragon\www\CumpleAppMobile
# ⚠️ Puede tardar varios minutos (descarga dependencias)
```

## 🚀 Alternativa: Usar Expo (Más Fácil)

Si tienes problemas con React Native CLI, puedes usar Expo que es más simple:

```bash
# Instalar Expo CLI
npm install -g expo-cli

# Crear proyecto
npx create-expo-app CumpleAppMobile

# Luego instalar dependencias de navegación
cd CumpleAppMobile
npm install @react-navigation/native @react-navigation/stack @react-navigation/bottom-tabs
npm install react-native-screens react-native-safe-area-context
npm install react-native-gesture-handler react-native-reanimated
npm install axios @react-native-async-storage/async-storage
npm install date-fns react-native-vector-icons
npm install react-native-linear-gradient
```

## 📝 Actualización del Script

El script `setup-react-native.ps1` ya está actualizado con el comando correcto.

## ⚠️ Nota sobre Versión de Node

Si ves advertencias sobre la versión de Node (requiere >= 20.19.4 y tienes 20.17.0):

**Opción 1:** Actualizar Node.js a la versión más reciente
- Descargar desde: https://nodejs.org/

**Opción 2:** Continuar de todas formas (solo son advertencias, no errores)
- El proyecto debería funcionar igual

## ✅ Verificación

Después de ejecutar el comando, verifica que se creó:

```powershell
cd c:\laragon\www\CumpleAppMobile
dir
# Deberías ver: android/, ios/, src/, App.tsx, package.json, etc.
```

---

**Comando correcto a usar:**
```bash
npx @react-native-community/cli@latest init CumpleAppMobile
```

