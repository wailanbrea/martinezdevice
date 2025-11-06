#!/bin/bash

# Script de Deployment para Martínez Service
# Uso: ./deploy.sh

set -e  # Salir si hay algún error

echo "🚀 Iniciando deployment de Martínez Service..."
echo ""

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    echo "❌ Error: No se encontró el archivo artisan. Asegúrate de estar en el directorio raíz del proyecto."
    exit 1
fi

# Verificar que existe el archivo .env
if [ ! -f ".env" ]; then
    echo "⚠️  Advertencia: No se encontró el archivo .env"
    if [ -f ".env.example" ]; then
        echo "📋 Copiando .env.example a .env..."
        cp .env.example .env
        echo "⚠️  IMPORTANTE: Edita el archivo .env con tus credenciales antes de continuar."
        echo "   Ejecuta: nano .env"
        exit 1
    else
        echo "❌ Error: No se encontró .env.example. No se puede continuar."
        exit 1
    fi
fi

# Verificar que PHP está instalado
if ! command -v php &> /dev/null; then
    echo "❌ Error: PHP no está instalado o no está en el PATH."
    exit 1
fi

# Verificar que Composer está instalado
if ! command -v composer &> /dev/null; then
    echo "❌ Error: Composer no está instalado o no está en el PATH."
    exit 1
fi

# Activar modo mantenimiento
echo "📦 Activando modo mantenimiento..."
php artisan down || true

# Obtener últimos cambios del repositorio
echo "📥 Obteniendo últimos cambios..."
if ! git pull origin main; then
    echo "⚠️  Advertencia: Error al hacer git pull. Continuando con el deployment..."
fi

# Instalar dependencias de Composer
echo "📚 Instalando dependencias de Composer..."
if ! composer install --no-dev --optimize-autoloader --no-interaction; then
    echo "❌ Error: Falló la instalación de dependencias de Composer."
    php artisan up
    exit 1
fi

# Instalar dependencias de NPM y compilar (si existe package.json)
if [ -f "package.json" ]; then
    echo "📦 Instalando dependencias de NPM..."
    if command -v npm &> /dev/null; then
        npm install --production
        npm run build
    else
        echo "⚠️  Advertencia: NPM no está instalado. Saltando compilación de assets."
    fi
else
    echo "ℹ️  No se encontró package.json. Saltando instalación de NPM."
fi

# Verificar conexión a la base de datos antes de migrar
echo "🔍 Verificando conexión a la base de datos..."
if ! php artisan db:show &> /dev/null; then
    echo "❌ Error: No se puede conectar a la base de datos. Verifica tu configuración en .env"
    php artisan up
    exit 1
fi

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
if ! php artisan migrate --force; then
    echo "❌ Error: Falló la ejecución de migraciones."
    php artisan up
    exit 1
fi

# Crear enlace simbólico de storage si no existe
if [ ! -L "public/storage" ]; then
    echo "🔗 Creando enlace simbólico de storage..."
    php artisan storage:link
fi

# Asegurar permisos correctos
echo "🔐 Configurando permisos..."
if [ -d "storage" ]; then
    chmod -R 775 storage bootstrap/cache 2>/dev/null || true
    # Intentar cambiar propietario si es posible (puede fallar si no eres root)
    if [ "$EUID" -eq 0 ]; then
        chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
    fi
fi

# Limpiar cachés
echo "🧹 Limpiando cachés..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimizar para producción
echo "⚡ Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Desactivar modo mantenimiento
echo "✅ Desactivando modo mantenimiento..."
php artisan up

echo ""
echo "🎉 ¡Deployment completado exitosamente!"
echo ""
echo "📝 Verificaciones finales:"
echo "   - Verifica que el servidor web apunta al directorio 'public'"
echo "   - Verifica los permisos de storage y bootstrap/cache"
echo "   - Revisa los logs en storage/logs/laravel.log si hay problemas"
echo ""

