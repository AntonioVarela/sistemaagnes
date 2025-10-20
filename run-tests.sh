#!/bin/bash

echo "🧪 Ejecutando Suite Completa de Pruebas"
echo "========================================"

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para mostrar resultados
show_result() {
    if [ $1 -eq 0 ]; then
        echo -e "${GREEN}✅ $2${NC}"
    else
        echo -e "${RED}❌ $2${NC}"
    fi
}

echo -e "${BLUE}📋 Configurando entorno de pruebas...${NC}"

# Verificar que PHPUnit esté instalado
if ! command -v ./vendor/bin/phpunit &> /dev/null; then
    echo -e "${RED}❌ PHPUnit no encontrado. Instalando dependencias...${NC}"
    composer install --dev
fi

# Limpiar cache
echo -e "${YELLOW}🧹 Limpiando cache...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Ejecutar migraciones para pruebas
echo -e "${YELLOW}🗄️ Preparando base de datos de pruebas...${NC}"
php artisan migrate:fresh --env=testing

echo -e "${BLUE}🚀 Iniciando pruebas...${NC}"
echo ""

# 1. Pruebas Unitarias
echo -e "${BLUE}📦 Ejecutando Pruebas Unitarias...${NC}"
./vendor/bin/phpunit tests/Unit --colors=always
UNIT_RESULT=$?
show_result $UNIT_RESULT "Pruebas Unitarias"

echo ""

# 2. Pruebas de Funcionalidad
echo -e "${BLUE}🔧 Ejecutando Pruebas de Funcionalidad...${NC}"
./vendor/bin/phpunit tests/Feature --colors=always
FEATURE_RESULT=$?
show_result $FEATURE_RESULT "Pruebas de Funcionalidad"

echo ""

# 3. Pruebas de Seguridad
echo -e "${BLUE}🔒 Ejecutando Pruebas de Seguridad...${NC}"
./vendor/bin/phpunit tests/Feature/SecurityTest.php --colors=always
SECURITY_RESULT=$?
show_result $SECURITY_RESULT "Pruebas de Seguridad"

echo ""

# 4. Pruebas de Base de Datos
echo -e "${BLUE}🗄️ Ejecutando Pruebas de Base de Datos...${NC}"
./vendor/bin/phpunit tests/Feature/DatabaseTest.php --colors=always
DATABASE_RESULT=$?
show_result $DATABASE_RESULT "Pruebas de Base de Datos"

echo ""

# 5. Pruebas de Controladores
echo -e "${BLUE}🎮 Ejecutando Pruebas de Controladores...${NC}"
./vendor/bin/phpunit tests/Feature/AdministradorControllerTest.php --colors=always
CONTROLLER_RESULT=$?
show_result $CONTROLLER_RESULT "Pruebas de Controladores"

echo ""
echo "========================================"
echo -e "${BLUE}📊 RESUMEN DE RESULTADOS${NC}"
echo "========================================"

# Calcular total de pruebas
TOTAL_TESTS=0
PASSED_TESTS=0

if [ $UNIT_RESULT -eq 0 ]; then
    PASSED_TESTS=$((PASSED_TESTS + 1))
fi
TOTAL_TESTS=$((TOTAL_TESTS + 1))

if [ $FEATURE_RESULT -eq 0 ]; then
    PASSED_TESTS=$((PASSED_TESTS + 1))
fi
TOTAL_TESTS=$((TOTAL_TESTS + 1))

if [ $SECURITY_RESULT -eq 0 ]; then
    PASSED_TESTS=$((PASSED_TESTS + 1))
fi
TOTAL_TESTS=$((TOTAL_TESTS + 1))

if [ $DATABASE_RESULT -eq 0 ]; then
    PASSED_TESTS=$((PASSED_TESTS + 1))
fi
TOTAL_TESTS=$((TOTAL_TESTS + 1))

if [ $CONTROLLER_RESULT -eq 0 ]; then
    PASSED_TESTS=$((PASSED_TESTS + 1))
fi
TOTAL_TESTS=$((TOTAL_TESTS + 1))

echo -e "Pruebas Pasadas: ${GREEN}$PASSED_TESTS${NC} / ${BLUE}$TOTAL_TESTS${NC}"

# Determinar resultado final
if [ $PASSED_TESTS -eq $TOTAL_TESTS ]; then
    echo -e "${GREEN}🎉 ¡Todas las pruebas pasaron exitosamente!${NC}"
    echo -e "${GREEN}✅ El sistema está funcionando correctamente y es seguro.${NC}"
    exit 0
else
    echo -e "${RED}⚠️ Algunas pruebas fallaron. Revisa los errores arriba.${NC}"
    exit 1
fi
