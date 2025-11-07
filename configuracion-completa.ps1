# Script de Configuración Completa - Martínez Service
# Este script verifica y configura todo correctamente

Write-Host "=== CONFIGURACIÓN COMPLETA - MARTINEZ SERVICE ===" -ForegroundColor Cyan
Write-Host ""

$errores = 0
$advertencias = 0

# 1. Verificar .env
Write-Host "1. Verificando archivo .env..." -ForegroundColor Yellow
if (Test-Path .env) {
    $envContent = Get-Content .env -Raw
    
    # Verificar APP_KEY
    if ($envContent -match "APP_KEY=base64:") {
        Write-Host "   OK - APP_KEY configurada" -ForegroundColor Green
    } else {
        Write-Host "   ERROR - APP_KEY no configurada" -ForegroundColor Red
        Write-Host "   Ejecutando: php artisan key:generate" -ForegroundColor Yellow
        php artisan key:generate --force
        $errores++
    }
    
    # Verificar CACHE_STORE
    if ($envContent -match "CACHE_STORE=file") {
        Write-Host "   OK - CACHE_STORE configurado" -ForegroundColor Green
    } else {
        Write-Host "   ADVERTENCIA - CACHE_STORE no configurado correctamente" -ForegroundColor Yellow
        $advertencias++
    }
    
    # Verificar LOG_CHANNEL
    if ($envContent -match "LOG_CHANNEL=stack") {
        Write-Host "   OK - LOG_CHANNEL configurado" -ForegroundColor Green
    } else {
        Write-Host "   ADVERTENCIA - LOG_CHANNEL no configurado correctamente" -ForegroundColor Yellow
        $advertencias++
    }
} else {
    Write-Host "   ERROR - .env no existe" -ForegroundColor Red
    $errores++
}

# 2. Verificar base de datos
Write-Host "`n2. Verificando conexión a base de datos..." -ForegroundColor Yellow
$dbCheck = php artisan db:show 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "   OK - Conexión a base de datos exitosa" -ForegroundColor Green
} else {
    Write-Host "   ERROR - No se puede conectar a la base de datos" -ForegroundColor Red
    $errores++
}

# 3. Verificar migraciones
Write-Host "`n3. Verificando migraciones..." -ForegroundColor Yellow
$migraciones = php artisan migrate:status 2>&1 | Select-String -Pattern "Ran"
if ($migraciones) {
    $count = ($migraciones | Measure-Object).Count
    Write-Host "   OK - $count migraciones ejecutadas" -ForegroundColor Green
} else {
    Write-Host "   ADVERTENCIA - No hay migraciones ejecutadas" -ForegroundColor Yellow
    $advertencias++
}

# 4. Verificar permisos de directorios
Write-Host "`n4. Verificando directorios..." -ForegroundColor Yellow
$directorios = @("storage", "bootstrap/cache", "public")
foreach ($dir in $directorios) {
    if (Test-Path $dir) {
        Write-Host "   OK - $dir existe" -ForegroundColor Green
    } else {
        Write-Host "   ERROR - $dir NO existe" -ForegroundColor Red
        $errores++
    }
}

# 5. Verificar storage link
Write-Host "`n5. Verificando enlace de storage..." -ForegroundColor Yellow
if (Test-Path "public\storage") {
    Write-Host "   OK - Enlace de storage existe" -ForegroundColor Green
} else {
    Write-Host "   ADVERTENCIA - Enlace de storage no existe" -ForegroundColor Yellow
    Write-Host "   Creando enlace..." -ForegroundColor Yellow
    php artisan storage:link
    $advertencias++
}

# 6. Limpiar y optimizar cachés
Write-Host "`n6. Limpiando y optimizando cachés..." -ForegroundColor Yellow
php artisan config:clear | Out-Null
php artisan cache:clear | Out-Null
php artisan route:clear | Out-Null
php artisan view:clear | Out-Null
Write-Host "   OK - Cachés limpiados" -ForegroundColor Green

# 7. Optimizar para desarrollo
Write-Host "`n7. Optimizando configuración..." -ForegroundColor Yellow
php artisan config:cache 2>&1 | Out-Null
if ($LASTEXITCODE -eq 0) {
    Write-Host "   OK - Configuración cacheada" -ForegroundColor Green
} else {
    Write-Host "   ADVERTENCIA - Error al cachear configuración" -ForegroundColor Yellow
    $advertencias++
}

# 8. Verificar Apache
Write-Host "`n8. Verificando Apache..." -ForegroundColor Yellow
$apache = Get-Process -Name httpd -ErrorAction SilentlyContinue
if ($apache) {
    Write-Host "   OK - Apache está corriendo" -ForegroundColor Green
} else {
    Write-Host "   ADVERTENCIA - Apache NO está corriendo" -ForegroundColor Yellow
    Write-Host "   Inicia Apache desde XAMPP Control Panel" -ForegroundColor Yellow
    $advertencias++
}

# 9. Verificar MySQL
Write-Host "`n9. Verificando MySQL..." -ForegroundColor Yellow
$mysql = Get-Process -Name mysqld -ErrorAction SilentlyContinue
if ($mysql) {
    Write-Host "   OK - MySQL está corriendo" -ForegroundColor Green
} else {
    Write-Host "   ADVERTENCIA - MySQL NO está corriendo" -ForegroundColor Yellow
    Write-Host "   Inicia MySQL desde XAMPP Control Panel" -ForegroundColor Yellow
    $advertencias++
}

# 10. Verificar VirtualHost
Write-Host "`n10. Verificando VirtualHost de Apache..." -ForegroundColor Yellow
$vhost = C:\xampp\apache\bin\httpd.exe -S 2>&1 | Select-String -Pattern "demomartinez"
if ($vhost) {
    Write-Host "   OK - VirtualHost configurado" -ForegroundColor Green
} else {
    Write-Host "   ADVERTENCIA - VirtualHost no encontrado" -ForegroundColor Yellow
    $advertencias++
}

# Resumen
Write-Host "`n============================================" -ForegroundColor Gray
Write-Host "`nRESUMEN DE CONFIGURACIÓN:" -ForegroundColor Cyan
Write-Host ""

if ($errores -eq 0 -and $advertencias -eq 0) {
    Write-Host "PERFECTO - Todo configurado correctamente" -ForegroundColor Green
    Write-Host ""
    Write-Host "URLs disponibles:" -ForegroundColor Cyan
    Write-Host "  Local (Apache): http://demomartinez.bsolutions.dev" -ForegroundColor White
    Write-Host "  Producción:     https://demomartinez.bsolutions.dev" -ForegroundColor White
} elseif ($errores -eq 0) {
    Write-Host "BIEN - $advertencias advertencia(s) encontradas" -ForegroundColor Yellow
    Write-Host "El sistema debería funcionar, pero revisa las advertencias." -ForegroundColor White
} else {
    Write-Host "ATENCIÓN - $errores error(es) y $advertencias advertencia(s)" -ForegroundColor Red
    Write-Host "Corrige los errores antes de continuar." -ForegroundColor White
}

Write-Host ""
Write-Host "Próximos pasos:" -ForegroundColor Yellow
Write-Host "1. Asegúrate de que Apache y MySQL estén corriendo en XAMPP" -ForegroundColor White
Write-Host "2. Para servidor local: http://demomartinez.bsolutions.dev" -ForegroundColor White
Write-Host "3. Para producción: https://demomartinez.bsolutions.dev (ya funcionando)" -ForegroundColor White
Write-Host ""

