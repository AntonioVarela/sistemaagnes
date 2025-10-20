# Script de PowerShell para ejecutar pruebas
Write-Host "🧪 Ejecutando Suite Completa de Pruebas" -ForegroundColor Blue
Write-Host "========================================" -ForegroundColor Blue

# Función para mostrar resultados
function Show-Result {
    param($ExitCode, $Message)
    if ($ExitCode -eq 0) {
        Write-Host "✅ $Message" -ForegroundColor Green
    } else {
        Write-Host "❌ $Message" -ForegroundColor Red
    }
}

Write-Host "📋 Configurando entorno de pruebas..." -ForegroundColor Blue

# Verificar que PHPUnit esté disponible
if (-not (Test-Path "vendor\bin\phpunit.bat")) {
    Write-Host "❌ PHPUnit no encontrado. Instalando dependencias..." -ForegroundColor Red
    composer install --dev
}

# Limpiar cache
Write-Host "🧹 Limpiando cache..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Ejecutar migraciones para pruebas
Write-Host "🗄️ Preparando base de datos de pruebas..." -ForegroundColor Yellow
php artisan migrate:fresh --env=testing

Write-Host "🚀 Iniciando pruebas..." -ForegroundColor Blue
Write-Host ""

# 1. Pruebas Unitarias
Write-Host "📦 Ejecutando Pruebas Unitarias..." -ForegroundColor Blue
$unitResult = & vendor\bin\phpunit.bat tests\Unit --colors=always
$unitExitCode = $LASTEXITCODE
Show-Result $unitExitCode "Pruebas Unitarias"

Write-Host ""

# 2. Pruebas de Funcionalidad
Write-Host "🔧 Ejecutando Pruebas de Funcionalidad..." -ForegroundColor Blue
$featureResult = & vendor\bin\phpunit.bat tests\Feature --colors=always
$featureExitCode = $LASTEXITCODE
Show-Result $featureExitCode "Pruebas de Funcionalidad"

Write-Host ""

# 3. Pruebas de Seguridad
Write-Host "🔒 Ejecutando Pruebas de Seguridad..." -ForegroundColor Blue
$securityResult = & vendor\bin\phpunit.bat tests\Feature\SecurityTest.php --colors=always
$securityExitCode = $LASTEXITCODE
Show-Result $securityExitCode "Pruebas de Seguridad"

Write-Host ""

# 4. Pruebas de Base de Datos
Write-Host "🗄️ Ejecutando Pruebas de Base de Datos..." -ForegroundColor Blue
$databaseResult = & vendor\bin\phpunit.bat tests\Feature\DatabaseTest.php --colors=always
$databaseExitCode = $LASTEXITCODE
Show-Result $databaseExitCode "Pruebas de Base de Datos"

Write-Host ""

# 5. Pruebas de Controladores
Write-Host "🎮 Ejecutando Pruebas de Controladores..." -ForegroundColor Blue
$controllerResult = & vendor\bin\phpunit.bat tests\Feature\AdministradorControllerTest.php --colors=always
$controllerExitCode = $LASTEXITCODE
Show-Result $controllerExitCode "Pruebas de Controladores"

Write-Host ""
Write-Host "========================================" -ForegroundColor Blue
Write-Host "📊 RESUMEN DE RESULTADOS" -ForegroundColor Blue
Write-Host "========================================" -ForegroundColor Blue

# Calcular total de pruebas
$totalTests = 5
$passedTests = 0

if ($unitExitCode -eq 0) { $passedTests++ }
if ($featureExitCode -eq 0) { $passedTests++ }
if ($securityExitCode -eq 0) { $passedTests++ }
if ($databaseExitCode -eq 0) { $passedTests++ }
if ($controllerExitCode -eq 0) { $passedTests++ }

Write-Host "Pruebas Pasadas: $passedTests / $totalTests" -ForegroundColor Green

# Determinar resultado final
if ($passedTests -eq $totalTests) {
    Write-Host "🎉 ¡Todas las pruebas pasaron exitosamente!" -ForegroundColor Green
    Write-Host "✅ El sistema está funcionando correctamente y es seguro." -ForegroundColor Green
    exit 0
} else {
    Write-Host "⚠️ Algunas pruebas fallaron. Revisa los errores arriba." -ForegroundColor Red
    exit 1
}
