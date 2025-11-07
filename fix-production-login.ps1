# Script para corregir problemas de login en producción
# Ejecutar como Administrador: PowerShell -ExecutionPolicy Bypass -File fix-production-login.ps1

Write-Host "=== Corrección de Configuración de Producción ===" -ForegroundColor Cyan
Write-Host ""

$envFile = Join-Path $PSScriptRoot ".env"

if (-not (Test-Path $envFile)) {
    Write-Host "ERROR: No se encontró el archivo .env" -ForegroundColor Red
    exit 1
}

Write-Host "Leyendo archivo .env..." -ForegroundColor Yellow
$content = Get-Content $envFile -Raw

# Realizar reemplazos
Write-Host "Actualizando configuración..." -ForegroundColor Yellow

# Cambiar APP_ENV a production
$content = $content -replace '(?m)^APP_ENV=.*$', 'APP_ENV=production'

# Cambiar APP_DEBUG a false
$content = $content -replace '(?m)^APP_DEBUG=.*$', 'APP_DEBUG=false'

# Configurar SESSION_DOMAIN para subdominios
if ($content -notmatch 'SESSION_DOMAIN=') {
    $content = $content -replace '(?m)^(SESSION_DRIVER=.*)$', "`$1`nSESSION_DOMAIN=.bsolutions.dev"
} else {
    $content = $content -replace '(?m)^SESSION_DOMAIN=.*$', 'SESSION_DOMAIN=.bsolutions.dev'
}

# Configurar BCRYPT_ROUNDS
if ($content -notmatch 'BCRYPT_ROUNDS=') {
    $content = $content -replace '(?m)^(APP_KEY=.*)$', "`$1`nBCRYPT_ROUNDS=12"
} else {
    $content = $content -replace '(?m)^BCRYPT_ROUNDS=.*$', 'BCRYPT_ROUNDS=12'
}

# Agregar SESSION_SECURE_COOKIE si no existe (para HTTPS)
if ($content -notmatch 'SESSION_SECURE_COOKIE=') {
    $content = $content -replace '(?m)^(SESSION_DOMAIN=.*)$', "`$1`nSESSION_SECURE_COOKIE=false"
}

# Guardar archivo
try {
    Set-Content -Path $envFile -Value $content -NoNewline
    Write-Host "✓ Archivo .env actualizado correctamente" -ForegroundColor Green
} catch {
    Write-Host "ERROR: No se pudo escribir el archivo .env" -ForegroundColor Red
    Write-Host "Por favor, ejecuta este script como Administrador" -ForegroundColor Yellow
    Write-Host $_.Exception.Message -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "=== Limpiando caché de Laravel ===" -ForegroundColor Cyan
Set-Location $PSScriptRoot

# Limpiar caché
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

Write-Host ""
Write-Host "=== Verificando migración de sesiones ===" -ForegroundColor Cyan
php artisan migrate:status | Select-String "sessions"

Write-Host ""
Write-Host "=== Configuración completada ===" -ForegroundColor Green
Write-Host ""
Write-Host "Próximos pasos:" -ForegroundColor Yellow
Write-Host "1. Verifica que la tabla 'sessions' existe en la base de datos" -ForegroundColor White
Write-Host "2. Si no existe, ejecuta: php artisan migrate" -ForegroundColor White
Write-Host "3. Reinicia Apache desde XAMPP Control Panel" -ForegroundColor White
Write-Host "4. Prueba hacer login en: http://demomartinez.bsolutions.dev" -ForegroundColor White
Write-Host ""

