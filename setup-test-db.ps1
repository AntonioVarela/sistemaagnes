# Script para configurar base de datos de pruebas
Write-Host "🗄️ Configurando Base de Datos de Pruebas" -ForegroundColor Blue
Write-Host "=========================================" -ForegroundColor Blue

# Crear base de datos de pruebas si no existe
Write-Host "📋 Creando base de datos de pruebas..." -ForegroundColor Yellow

# Configurar variables de entorno para pruebas
$env:APP_ENV = "testing"
$env:DB_CONNECTION = "mysql"
$env:DB_DATABASE = "sistema_agnes_test"
$env:DB_HOST = "127.0.0.1"
$env:DB_PORT = "3306"
$env:DB_USERNAME = "root"
$env:DB_PASSWORD = ""

# Ejecutar migraciones
Write-Host "🔄 Ejecutando migraciones..." -ForegroundColor Yellow
php artisan migrate:fresh --env=testing --force

# Ejecutar seeders
Write-Host "🌱 Ejecutando seeders..." -ForegroundColor Yellow
php artisan db:seed --env=testing --force

Write-Host "✅ Base de datos de pruebas configurada exitosamente!" -ForegroundColor Green
Write-Host "🚀 Ahora puedes ejecutar las pruebas con: php artisan test" -ForegroundColor Blue
