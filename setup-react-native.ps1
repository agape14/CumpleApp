# Script PowerShell para configurar React Native
# Crea el proyecto en c:\laragon\www\CumpleAppMobile (independiente de CumpleApp)

Write-Host "🚀 Configurando proyecto React Native..." -ForegroundColor Green

# 1. Ir al directorio padre (c:\laragon\www)
$currentDir = Get-Location
$parentDir = Split-Path -Parent $currentDir

# Si estamos en CumpleApp, ir al directorio padre
if ($currentDir.Path -like "*CumpleApp") {
    Set-Location $parentDir
    Write-Host "📍 Cambiando a directorio: $(Get-Location)" -ForegroundColor Cyan
}

# 2. Verificar si ya existe CumpleAppMobile
if (Test-Path "CumpleAppMobile") {
    Write-Host "⚠️  La carpeta CumpleAppMobile ya existe. ¿Deseas eliminarla? (S/N)" -ForegroundColor Yellow
    $response = Read-Host
    if ($response -eq "S" -or $response -eq "s") {
        Remove-Item -Recurse -Force "CumpleAppMobile"
        Write-Host "✅ Carpeta eliminada" -ForegroundColor Green
    } else {
        Write-Host "❌ Operación cancelada" -ForegroundColor Red
        exit
    }
}

# 3. Crear proyecto nuevo en ubicación independiente
Write-Host "📦 Creando proyecto React Native en: $(Get-Location)\CumpleAppMobile" -ForegroundColor Green
Write-Host "⚠️  Esto puede tardar varios minutos..." -ForegroundColor Yellow
npx @react-native-community/cli@latest init CumpleAppMobile --skip-install

# 4. Ir al proyecto
Set-Location CumpleAppMobile

# 5. Instalar dependencias base
Write-Host "📥 Instalando dependencias base..." -ForegroundColor Green
npm install

# 6. Instalar dependencias adicionales
Write-Host "📥 Instalando dependencias adicionales..." -ForegroundColor Green
npm install @react-navigation/native @react-navigation/stack @react-navigation/bottom-tabs
npm install react-native-screens react-native-safe-area-context
npm install react-native-gesture-handler react-native-reanimated
npm install axios @react-native-async-storage/async-storage
npm install date-fns react-native-vector-icons
npm install react-native-linear-gradient

# 7. Crear estructura de carpetas
Write-Host "📁 Creando estructura de carpetas..." -ForegroundColor Green
New-Item -ItemType Directory -Force -Path "src\config" | Out-Null
New-Item -ItemType Directory -Force -Path "src\constants" | Out-Null
New-Item -ItemType Directory -Force -Path "src\context" | Out-Null
New-Item -ItemType Directory -Force -Path "src\navigation" | Out-Null
New-Item -ItemType Directory -Force -Path "src\screens" | Out-Null
New-Item -ItemType Directory -Force -Path "src\services" | Out-Null

# 8. Copiar archivos desde CumpleApp/react-native-app
Write-Host "📋 Copiando archivos desde CumpleApp..." -ForegroundColor Green
$sourcePath = Join-Path $parentDir "CumpleApp\react-native-app\src"
$destPath = "src"

if (Test-Path $sourcePath) {
    Copy-Item -Path "$sourcePath\*" -Destination $destPath -Recurse -Force
    Write-Host "✅ Archivos copiados desde src/" -ForegroundColor Green
} else {
    Write-Host "⚠️  No se encontró react-native-app/src. Deberás copiar los archivos manualmente." -ForegroundColor Yellow
}

# Copiar App.tsx
$appSource = Join-Path $parentDir "CumpleApp\react-native-app\App.tsx"
if (Test-Path $appSource) {
    Copy-Item -Path $appSource -Destination "App.tsx" -Force
    Write-Host "✅ App.tsx copiado" -ForegroundColor Green
} else {
    Write-Host "⚠️  No se encontró App.tsx. Deberás copiarlo manualmente." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "✅ Proyecto creado exitosamente en: $(Get-Location)" -ForegroundColor Green
Write-Host ""
Write-Host "📝 Próximos pasos:" -ForegroundColor Cyan
Write-Host "1. Configurar URL de API en src/config/api.ts" -ForegroundColor White
Write-Host "   - Android Emulador: http://10.0.2.2:8000/api/v1" -ForegroundColor Gray
Write-Host "   - iOS Simulador: http://localhost:8000/api/v1" -ForegroundColor Gray
Write-Host "   - Dispositivo Físico: http://TU_IP:8000/api/v1" -ForegroundColor Gray
Write-Host ""
Write-Host "2. Asegúrate de que Laravel esté corriendo:" -ForegroundColor White
Write-Host "   cd ..\CumpleApp && php artisan serve" -ForegroundColor Gray
Write-Host ""
Write-Host "3. Ejecutar la app:" -ForegroundColor White
Write-Host "   npm run android  # o npm run ios" -ForegroundColor Gray
Write-Host ""

