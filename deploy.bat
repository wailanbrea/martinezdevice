@echo off
REM Script de Deployment para Martínez Service (Windows)
REM Uso: deploy.bat

echo 🚀 Iniciando deployment de Martínez Service...
echo.

REM Activar modo mantenimiento
echo 📦 Activando modo mantenimiento...
php artisan down

REM Obtener últimos cambios del repositorio
echo 📥 Obteniendo últimos cambios...
git pull origin main

REM Instalar dependencias de Composer
echo 📚 Instalando dependencias de Composer...
composer install --no-dev --optimize-autoloader

REM Instalar dependencias de NPM y compilar
echo 📦 Instalando dependencias de NPM...
call npm install
call npm run build

REM Ejecutar migraciones
echo 🗄️ Ejecutando migraciones...
php artisan migrate --force

REM Limpiar cachés
echo 🧹 Limpiando cachés...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

REM Optimizar para producción
echo ⚡ Optimizando para producción...
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

REM Desactivar modo mantenimiento
echo ✅ Desactivando modo mantenimiento...
php artisan up

echo.
echo 🎉 ¡Deployment completado exitosamente!
echo.
pause

