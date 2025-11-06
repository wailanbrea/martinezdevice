#!/bin/bash

# Script de Verificación de Requisitos para Martínez Service
# Uso: ./check-requirements.sh

echo "🔍 Verificando requisitos del sistema para Martínez Service..."
echo ""

ERRORS=0
WARNINGS=0

# Verificar PHP
echo -n "Verificando PHP... "
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -r 'echo PHP_VERSION;')
    PHP_MAJOR=$(echo $PHP_VERSION | cut -d. -f1)
    PHP_MINOR=$(echo $PHP_VERSION | cut -d. -f2)
    
    if [ "$PHP_MAJOR" -ge 8 ] && [ "$PHP_MINOR" -ge 3 ]; then
        echo "✅ OK (v$PHP_VERSION)"
    else
        echo "❌ ERROR: Se requiere PHP >= 8.3. Versión actual: $PHP_VERSION"
        ERRORS=$((ERRORS + 1))
    fi
else
    echo "❌ ERROR: PHP no está instalado"
    ERRORS=$((ERRORS + 1))
fi

# Verificar extensiones PHP necesarias
echo -n "Verificando extensiones PHP... "
REQUIRED_EXTENSIONS=("pdo" "pdo_mysql" "mbstring" "xml" "openssl" "json" "gd" "zip" "fileinfo" "tokenizer" "ctype" "bcmath")
MISSING_EXTENSIONS=()

for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if ! php -m | grep -q "^$ext$"; then
        MISSING_EXTENSIONS+=("$ext")
    fi
done

if [ ${#MISSING_EXTENSIONS[@]} -eq 0 ]; then
    echo "✅ OK"
else
    echo "❌ ERROR: Extensiones faltantes: ${MISSING_EXTENSIONS[*]}"
    ERRORS=$((ERRORS + 1))
fi

# Verificar Composer
echo -n "Verificando Composer... "
if command -v composer &> /dev/null; then
    COMPOSER_VERSION=$(composer --version | head -n1)
    echo "✅ OK ($COMPOSER_VERSION)"
else
    echo "❌ ERROR: Composer no está instalado"
    ERRORS=$((ERRORS + 1))
fi

# Verificar Node.js (opcional pero recomendado)
echo -n "Verificando Node.js... "
if command -v node &> /dev/null; then
    NODE_VERSION=$(node -v)
    echo "✅ OK ($NODE_VERSION)"
else
    echo "⚠️  ADVERTENCIA: Node.js no está instalado (necesario para compilar assets)"
    WARNINGS=$((WARNINGS + 1))
fi

# Verificar NPM (opcional pero recomendado)
echo -n "Verificando NPM... "
if command -v npm &> /dev/null; then
    NPM_VERSION=$(npm -v)
    echo "✅ OK (v$NPM_VERSION)"
else
    echo "⚠️  ADVERTENCIA: NPM no está instalado (necesario para compilar assets)"
    WARNINGS=$((WARNINGS + 1))
fi

# Verificar MySQL/MariaDB
echo -n "Verificando MySQL/MariaDB... "
if command -v mysql &> /dev/null || command -v mariadb &> /dev/null; then
    echo "✅ OK"
else
    echo "⚠️  ADVERTENCIA: MySQL/MariaDB no está instalado o no está en el PATH"
    WARNINGS=$((WARNINGS + 1))
fi

# Verificar permisos de directorios
echo -n "Verificando permisos de storage... "
if [ -d "storage" ]; then
    if [ -w "storage" ]; then
        echo "✅ OK"
    else
        echo "❌ ERROR: El directorio storage no tiene permisos de escritura"
        ERRORS=$((ERRORS + 1))
    fi
else
    echo "⚠️  ADVERTENCIA: El directorio storage no existe"
    WARNINGS=$((WARNINGS + 1))
fi

# Verificar archivo .env
echo -n "Verificando archivo .env... "
if [ -f ".env" ]; then
    # Verificar que APP_KEY esté configurado
    if grep -q "APP_KEY=base64:" .env 2>/dev/null || grep -q "APP_KEY=" .env 2>/dev/null && ! grep -q "APP_KEY=$" .env 2>/dev/null; then
        echo "✅ OK"
    else
        echo "⚠️  ADVERTENCIA: APP_KEY no está configurado. Ejecuta: php artisan key:generate"
        WARNINGS=$((WARNINGS + 1))
    fi
else
    if [ -f ".env.example" ]; then
        echo "⚠️  ADVERTENCIA: .env no existe. Copia .env.example a .env y configúralo"
        WARNINGS=$((WARNINGS + 1))
    else
        echo "❌ ERROR: No se encontró .env ni .env.example"
        ERRORS=$((ERRORS + 1))
    fi
fi

# Verificar conexión a base de datos (si .env existe)
if [ -f ".env" ]; then
    echo -n "Verificando conexión a base de datos... "
    if php artisan db:show &> /dev/null 2>&1; then
        echo "✅ OK"
    else
        echo "❌ ERROR: No se puede conectar a la base de datos. Verifica tu configuración en .env"
        ERRORS=$((ERRORS + 1))
    fi
fi

# Resumen
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo "✅ Todos los requisitos están cumplidos. El sistema está listo para deployment."
    exit 0
elif [ $ERRORS -eq 0 ]; then
    echo "⚠️  Hay $WARNINGS advertencia(s). El sistema puede funcionar, pero revisa las advertencias."
    exit 0
else
    echo "❌ Hay $ERRORS error(es) y $WARNINGS advertencia(s). Por favor, corrige los errores antes de continuar."
    exit 1
fi

