#!/bin/bash

# Script de Deployment para Martínez Service
# Uso: ./deploy.sh

echo "🚀 Iniciando deployment de Martínez Service..."
echo ""

# Activar modo mantenimiento
echo "📦 Activando modo mantenimiento..."
php artisan down || true

# Obtener últimos cambios del repositorio
echo "📥 Obteniendo últimos cambios..."
git pull origin main

# Instalar dependencias de Composer
echo "📚 Instalando dependencias de Composer..."
composer install --no-dev --optimize-autoloader

# Instalar dependencias de NPM y compilar
echo "📦 Instalando dependencias de NPM..."
npm install
npm run build

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force

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

