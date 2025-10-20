# 🧪 Guía de Pruebas - Sistema Agnes

## 📋 Resumen

Este documento describe cómo ejecutar y mantener las pruebas del sistema Agnes para garantizar que funcione correctamente y sea seguro.

## 🚀 Configuración Inicial

### 1. Preparar Base de Datos de Pruebas

```powershell
# Ejecutar script de configuración
.\setup-test-db.ps1
```

### 2. Verificar Dependencias

```bash
# Instalar dependencias de desarrollo
composer install --dev
```

## 🧪 Ejecutar Pruebas

### Opción 1: Script Automatizado (Recomendado)

```powershell
# Ejecutar todas las pruebas
.\run-tests.ps1
```

### Opción 2: Comandos Individuales

```bash
# Todas las pruebas
php artisan test

# Solo pruebas unitarias
php artisan test --testsuite=Unit

# Solo pruebas de funcionalidad
php artisan test --testsuite=Feature

# Pruebas específicas
php artisan test tests/Feature/SecurityTest.php
```

## 📊 Tipos de Pruebas

### 🔧 Pruebas Unitarias (`tests/Unit/`)

- **UserTest.php**: Pruebas del modelo User
- **CircularTest.php**: Pruebas del modelo Circular

**Cubre:**
- Creación de modelos
- Soft deletes
- Relaciones entre modelos
- Métodos personalizados
- Validaciones

### 🎮 Pruebas de Funcionalidad (`tests/Feature/`)

- **AdministradorControllerTest.php**: Pruebas del controlador principal
- **SecurityTest.php**: Pruebas de seguridad
- **DatabaseTest.php**: Pruebas de base de datos

**Cubre:**
- Autenticación y autorización
- CRUD operations
- Manejo de archivos
- Validaciones de formularios
- Redirecciones

### 🔒 Pruebas de Seguridad

**Verifica:**
- Protección CSRF
- Validación de entrada
- Autorización de usuarios
- Protección XSS
- Validación de archivos
- Contraseñas seguras

### 🗄️ Pruebas de Base de Datos

**Verifica:**
- Integridad de datos
- Relaciones entre tablas
- Soft deletes
- Constraints
- Transacciones

## 🛠️ Configuración de Pruebas

### Base de Datos
- **Motor**: MySQL
- **Base de datos**: `sistema_agnes_test`
- **Configuración**: `phpunit.xml`

### Storage
- **S3**: Fake storage para pruebas
- **Archivos**: Simulados en memoria

### Entorno
- **APP_ENV**: testing
- **Cache**: Array (en memoria)
- **Session**: Array (en memoria)

## 📈 Interpretación de Resultados

### ✅ Pruebas Exitosas
```
✅ Pruebas Unitarias
✅ Pruebas de Funcionalidad  
✅ Pruebas de Seguridad
✅ Pruebas de Base de Datos
✅ Pruebas de Controladores

🎉 ¡Todas las pruebas pasaron exitosamente!
✅ El sistema está funcionando correctamente y es seguro.
```

### ❌ Pruebas Fallidas
```
❌ Algunas pruebas fallaron. Revisa los errores arriba.
```

## 🔧 Solución de Problemas

### Error: "could not find driver"
```bash
# Instalar extensión SQLite (si se usa)
# O cambiar a MySQL en phpunit.xml
```

### Error: "Database connection failed"
```bash
# Verificar configuración de MySQL
# Crear base de datos de pruebas
mysql -u root -p -e "CREATE DATABASE sistema_agnes_test;"
```

### Error: "Class not found"
```bash
# Regenerar autoload
composer dump-autoload
```

## 📝 Agregar Nuevas Pruebas

### 1. Prueba Unitaria
```php
<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\MiModelo;

class MiModeloTest extends TestCase
{
    public function test_mi_modelo_puede_ser_creado()
    {
        $modelo = MiModelo::factory()->create();
        $this->assertDatabaseHas('mi_tabla', ['id' => $modelo->id]);
    }
}
```

### 2. Prueba de Funcionalidad
```php
<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class MiControladorTest extends TestCase
{
    public function test_usuario_puede_acceder_a_ruta()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $response = $this->get('/mi-ruta');
        $response->assertStatus(200);
    }
}
```

## 🎯 Mejores Prácticas

### ✅ Hacer
- Usar factories para crear datos de prueba
- Limpiar base de datos entre pruebas
- Probar casos edge y errores
- Verificar tanto éxito como fallo
- Usar nombres descriptivos para pruebas

### ❌ Evitar
- Depender de datos existentes
- Pruebas que dependan unas de otras
- Hardcodear valores específicos
- Ignorar pruebas fallidas
- Pruebas que no agregan valor

## 📊 Métricas de Cobertura

### Objetivos
- **Cobertura de código**: > 80%
- **Pruebas unitarias**: > 70%
- **Pruebas de integración**: > 60%
- **Pruebas de seguridad**: 100%

### Comandos de Cobertura
```bash
# Instalar Xdebug para cobertura
# php artisan test --coverage
```

## 🚀 Integración Continua

### GitHub Actions
```yaml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
      - name: Install dependencies
        run: composer install
      - name: Run tests
        run: php artisan test
```

## 📞 Soporte

Si encuentras problemas con las pruebas:

1. **Revisar logs**: `storage/logs/laravel.log`
2. **Verificar configuración**: `phpunit.xml`
3. **Limpiar cache**: `php artisan config:clear`
4. **Reinstalar dependencias**: `composer install --dev`

---

**🎯 Objetivo**: Garantizar que el sistema funcione correctamente, sea seguro y mantenga la calidad del código.
