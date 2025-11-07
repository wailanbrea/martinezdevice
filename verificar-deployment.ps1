# Script para verificar que el deployment se completó correctamente
# Uso: .\verificar-deployment.ps1

Write-Host "=== Verificación de Deployment ===" -ForegroundColor Cyan
Write-Host ""

$errors = 0
$warnings = 0

# Verificar .env
Write-Host -NoNewline "Verificando .env... "
if (Test-Path .env) {
    $envContent = Get-Content .env -Raw
    if ($envContent -match "APP_KEY=base64:") {
        Write-Host "OK" -ForegroundColor Green
    } else {
        Write-Host "ADVERTENCIA: APP_KEY no configurada" -ForegroundColor Yellow
        $warnings++
    }
} else {
    Write-Host "ERROR: .env no existe" -ForegroundColor Red
    $errors++
}

# Verificar storage link
Write-Host -NoNewline "Verificando enlace de storage... "
if (Test-Path public\storage) {
    Write-Host "OK" -ForegroundColor Green
} else {
    Write-Host "ADVERTENCIA: Enlace de storage no existe" -ForegroundColor Yellow
    $warnings++
}

# Verificar directorios
Write-Host -NoNewline "Verificando directorios... "
if ((Test-Path storage) -and (Test-Path bootstrap\cache) -and (Test-Path public)) {
    Write-Host "OK" -ForegroundColor Green
} else {
    Write-Host "ERROR: Directorios faltantes" -ForegroundColor Red
    $errors++
}

# Verificar migraciones
Write-Host -NoNewline "Verificando migraciones... "
$migrateStatus = php artisan migrate:status 2>&1
if ($LASTEXITCODE -eq 0) {
    $ranMigrations = ($migrateStatus | Select-String -Pattern "Ran" | Measure-Object).Count
    Write-Host "OK ($ranMigrations migraciones ejecutadas)" -ForegroundColor Green
} else {
    Write-Host "ERROR: No se pueden verificar migraciones" -ForegroundColor Red
    $errors++
}

# Verificar cachés
Write-Host -NoNewline "Verificando cachés... "
$about = php artisan about 2>&1
if ($about -match "Config.*CACHED" -and $about -match "Routes.*CACHED" -and $about -match "Views.*CACHED") {
    Write-Host "OK" -ForegroundColor Green
} else {
    Write-Host "ADVERTENCIA: Algunos cachés no están optimizados" -ForegroundColor Yellow
    $warnings++
}

# Verificar conexión a base de datos
Write-Host -NoNewline "Verificando conexión a BD... "
$dbShow = php artisan db:show 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "OK" -ForegroundColor Green
} else {
    Write-Host "ADVERTENCIA: No se pudo verificar conexión" -ForegroundColor Yellow
    $warnings++
}

# Resumen
Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Gray
if ($errors -eq 0 -and $warnings -eq 0) {
    Write-Host "Deployment completado exitosamente" -ForegroundColor Green
    Write-Host ""
    Write-Host "El proyecto está listo para probar." -ForegroundColor White
    Write-Host "Accede a: http://demomartinez.bsolutions.dev" -ForegroundColor Cyan
    exit 0
} elseif ($errors -eq 0) {
    Write-Host "Deployment completado con $warnings advertencia(s)" -ForegroundColor Yellow
    Write-Host "El proyecto debería funcionar, pero revisa las advertencias." -ForegroundColor White
    exit 0
} else {
    Write-Host "Deployment incompleto: $errors error(es) y $warnings advertencia(s)" -ForegroundColor Red
    Write-Host "Por favor, corrige los errores antes de continuar." -ForegroundColor White
    exit 1
}

