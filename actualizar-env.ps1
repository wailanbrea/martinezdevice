# Script para actualizar .env a configuración de producción
# Ejecutar: PowerShell -ExecutionPolicy Bypass -File actualizar-env.ps1

$envFile = Join-Path $PSScriptRoot ".env"
$backupFile = Join-Path $PSScriptRoot ".env.backup"

Write-Host "=== Actualizando .env para producción ===" -ForegroundColor Cyan
Write-Host ""

# Hacer backup
if (Test-Path $envFile) {
    Copy-Item $envFile $backupFile -Force
    Write-Host "✓ Backup creado: .env.backup" -ForegroundColor Green
}

# Leer contenido
$content = Get-Content $envFile -Raw

# Realizar cambios
Write-Host "Aplicando cambios..." -ForegroundColor Yellow
$content = $content -replace 'APP_ENV=local', 'APP_ENV=production'
$content = $content -replace 'APP_DEBUG=true', 'APP_DEBUG=false'
$content = $content -replace 'SESSION_DOMAIN=', 'SESSION_DOMAIN=.bsolutions.dev'
$content = $content -replace 'BCRYPT_ROUNDS=', 'BCRYPT_ROUNDS=12'

# Agregar SESSION_SECURE_COOKIE si no existe
if ($content -notmatch 'SESSION_SECURE_COOKIE=') {
    $content = $content -replace '(SESSION_DOMAIN=.*)', "`$1`nSESSION_SECURE_COOKIE=false"
}

# Guardar
try {
    [System.IO.File]::WriteAllText($envFile, $content, [System.Text.Encoding]::UTF8)
    Write-Host "✓ Archivo .env actualizado correctamente!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Cambios aplicados:" -ForegroundColor Cyan
    Write-Host "  - APP_ENV=production" -ForegroundColor White
    Write-Host "  - APP_DEBUG=false" -ForegroundColor White
    Write-Host "  - SESSION_DOMAIN=.bsolutions.dev" -ForegroundColor White
    Write-Host "  - BCRYPT_ROUNDS=12" -ForegroundColor White
    Write-Host "  - SESSION_SECURE_COOKIE=false" -ForegroundColor White
} catch {
    Write-Host "ERROR: No se pudo escribir el archivo" -ForegroundColor Red
    Write-Host "Ejecuta este script como Administrador" -ForegroundColor Yellow
    Write-Host $_.Exception.Message -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Próximos pasos:" -ForegroundColor Yellow
Write-Host "1. Ejecuta: php artisan config:clear" -ForegroundColor White
Write-Host "2. Reinicia Apache desde XAMPP Control Panel" -ForegroundColor White
Write-Host "3. Prueba el login en: http://demomartinez.bsolutions.dev" -ForegroundColor White

