# Script para corregir valores vacíos en .env
# Este script corrige los problemas de CACHE_STORE y LOG_CHANNEL vacíos

$envPath = Join-Path $PSScriptRoot ".env"

if (-not (Test-Path $envPath)) {
    Write-Host "ERROR: No se encontró el archivo .env" -ForegroundColor Red
    exit 1
}

Write-Host "Corrigiendo archivo .env..." -ForegroundColor Yellow

# Leer el contenido del archivo
$content = Get-Content $envPath -Raw

# Corregir CACHE_STORE si está vacío
if ($content -match "CACHE_STORE=\s*$" -or $content -match "CACHE_STORE=\r?\n") {
    $content = $content -replace "CACHE_STORE=\s*\r?\n", "CACHE_STORE=file`r`n"
    Write-Host "✓ CACHE_STORE corregido a 'file'" -ForegroundColor Green
} elseif (-not ($content -match "CACHE_STORE=")) {
    # Si no existe, agregarlo después de APP_ENV
    if ($content -match "(APP_ENV=.*\r?\n)") {
        $content = $content -replace "(APP_ENV=.*\r?\n)", "`$1CACHE_STORE=file`r`n"
        Write-Host "✓ CACHE_STORE agregado como 'file'" -ForegroundColor Green
    }
}

# Corregir LOG_CHANNEL si está vacío
if ($content -match "LOG_CHANNEL=\s*$" -or $content -match "LOG_CHANNEL=\r?\n") {
    $content = $content -replace "LOG_CHANNEL=\s*\r?\n", "LOG_CHANNEL=stack`r`n"
    Write-Host "✓ LOG_CHANNEL corregido a 'stack'" -ForegroundColor Green
} elseif (-not ($content -match "LOG_CHANNEL=")) {
    # Si no existe, agregarlo después de LOG_LEVEL
    if ($content -match "(LOG_LEVEL=.*\r?\n)") {
        $content = $content -replace "(LOG_LEVEL=.*\r?\n)", "`$1LOG_CHANNEL=stack`r`n"
        Write-Host "✓ LOG_CHANNEL agregado como 'stack'" -ForegroundColor Green
    } elseif ($content -match "(APP_DEBUG=.*\r?\n)") {
        $content = $content -replace "(APP_DEBUG=.*\r?\n)", "`$1LOG_CHANNEL=stack`r`n"
        Write-Host "✓ LOG_CHANNEL agregado como 'stack'" -ForegroundColor Green
    }
}

# Corregir LOG_STACK si está vacío (eliminar si está vacío)
if ($content -match "LOG_STACK=\s*$" -or $content -match "LOG_STACK=\r?\n") {
    $content = $content -replace "LOG_STACK=\s*\r?\n", ""
    Write-Host "✓ LOG_STACK vacío eliminado" -ForegroundColor Green
}

# Corregir LOG_DEPRECATIONS_CHANNEL si está vacío
if ($content -match "LOG_DEPRECATIONS_CHANNEL=\s*$" -or $content -match "LOG_DEPRECATIONS_CHANNEL=\r?\n") {
    $content = $content -replace "LOG_DEPRECATIONS_CHANNEL=\s*\r?\n", "LOG_DEPRECATIONS_CHANNEL=null`r`n"
    Write-Host "✓ LOG_DEPRECATIONS_CHANNEL corregido a 'null'" -ForegroundColor Green
}

# Corregir LOG_LEVEL si está vacío
if ($content -match "LOG_LEVEL=\s*$" -or $content -match "LOG_LEVEL=\r?\n") {
    $content = $content -replace "LOG_LEVEL=\s*\r?\n", "LOG_LEVEL=debug`r`n"
    Write-Host "✓ LOG_LEVEL corregido a 'debug'" -ForegroundColor Green
}

# Guardar el archivo corregido
$content | Set-Content $envPath -NoNewline

Write-Host "`n✓ Archivo .env corregido exitosamente" -ForegroundColor Green
Write-Host "`nAhora ejecuta: php artisan config:clear" -ForegroundColor Yellow

