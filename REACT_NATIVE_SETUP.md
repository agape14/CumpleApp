# 📱 Setup de React Native para CumpleApp

## 🚀 Instalación Inicial

```bash
# 1. Crear proyecto React Native
npx react-native@latest init CumpleAppMobile --template react-native-template-typescript

# 2. Navegar al directorio
cd CumpleAppMobile

# 3. Instalar dependencias principales
npm install @react-navigation/native @react-navigation/stack @react-navigation/bottom-tabs
npm install react-native-screens react-native-safe-area-context
npm install react-native-gesture-handler react-native-reanimated
npm install axios @react-native-async-storage/async-storage
npm install date-fns react-native-vector-icons
npm install @react-native-community/blur react-native-linear-gradient

# 4. Para iOS (solo en Mac)
cd ios && pod install && cd ..
```

## 📁 Estructura de Carpetas

La estructura completa está en la carpeta `react-native-app/` que se creará.

## ⚙️ Configuración

1. Cambiar la URL de la API en `src/config/api.ts`
2. Configurar el puerto según tu entorno (Android: 10.0.2.2, iOS: localhost)

## 🎨 Diseño UI/UX

- Gradientes modernos
- Animaciones suaves
- Cards con sombras
- Iconos vectoriales
- Paleta de colores consistente con la web

