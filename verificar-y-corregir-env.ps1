# Script para verificar y corregir .env según .env.example
# Este script asegura que todas las variables necesarias estén presentes

$envPath = Join-Path $PSScriptRoot ".env"
$envExamplePath = Join-Path $PSScriptRoot ".env.example"

Write-Host "=== Verificación y Corrección de .env ===" -ForegroundColor Cyan
Write-Host ""

if (-not (Test-Path $envExamplePath)) {
    Write-Host "ERROR: No se encontró .env.example" -ForegroundColor Red
    exit 1
}

if (-not (Test-Path $envPath)) {
    Write-Host "ERROR: No se encontró .env. Copiando desde .env.example..." -ForegroundColor Yellow
    Copy-Item $envExamplePath $envPath
    Write-Host "✓ .env creado desde .env.example" -ForegroundColor Green
}

# Leer ambos archivos
$envContent = Get-Content $envPath -Raw
$envExampleContent = Get-Content $envExamplePath -Raw

$corrections = @()

# Variables críticas que deben estar correctas
$requiredVars = @{
    "LOG_CHANNEL" = "stack"
    "LOG_STACK" = "single"
    "CACHE_STORE" = "file"
    "LOG_DEPRECATIONS_CHANNEL" = "null"
    "LOG_LEVEL" = "debug"
}

# Verificar y corregir cada variable
foreach ($var in $requiredVars.Keys) {
    $value = $requiredVars[$var]
    
    # Verificar si la variable existe
    if ($envContent -notmatch "$var=") {
        # Variable no existe, agregarla después de LOG_LEVEL o al final
        if ($envContent -match "(LOG_LEVEL=.*\r?\n)") {
            $envContent = $envContent -replace "(LOG_LEVEL=.*\r?\n)", "`$1$var=$value`r`n"
            $corrections += "Agregado: $var=$value"
        } elseif ($envContent -match "(APP_FAKER_LOCALE=.*\r?\n)") {
            $envContent = $envContent -replace "(APP_FAKER_LOCALE=.*\r?\n)", "`$1`r`n$var=$value`r`n"
            $corrections += "Agregado: $var=$value"
        }
    } else {
        # Variable existe, verificar si está vacía o incorrecta
        if ($envContent -match "$var=\s*$" -or $envContent -match "$var=\r?\n") {
            $envContent = $envContent -replace "$var=\s*\r?\n", "$var=$value`r`n"
            $corrections += "Corregido: $var=$value"
        } elseif ($var -eq "LOG_STACK" -and $envContent -match "LOG_STACK=stack") {
            # LOG_STACK=stack es correcto, pero verificar que LOG_CHANNEL también esté
            if ($envContent -notmatch "LOG_CHANNEL=stack") {
                $envContent = $envContent -replace "(LOG_STACK=.*\r?\n)", "`$1LOG_CHANNEL=stack`r`n"
                $corrections += "Agregado: LOG_CHANNEL=stack"
            }
        }
    }
}

# Verificar LOG_CHANNEL específicamente
if ($envContent -notmatch "LOG_CHANNEL=") {
    if ($envContent -match "(LOG_STACK=.*\r?\n)") {
        $envContent = $envContent -replace "(LOG_STACK=.*\r?\n)", "LOG_CHANNEL=stack`r`n`$1"
        $corrections += "Agregado: LOG_CHANNEL=stack"
    }
}

# Verificar CACHE_STORE específicamente
if ($envContent -notmatch "CACHE_STORE=") {
    if ($envContent -match "(BCRYPT_ROUNDS=.*\r?\n)") {
        $envContent = $envContent -replace "(BCRYPT_ROUNDS=.*\r?\n)", "`$1`r`nCACHE_STORE=file`r`n"
        $corrections += "Agregado: CACHE_STORE=file"
    }
}

# Guardar cambios si hay correcciones
if ($corrections.Count -gt 0) {
    # Crear backup
    $backup = "$envPath.backup." + (Get-Date -Format "yyyyMMdd_HHmmss")
    Copy-Item $envPath $backup
    Write-Host "✓ Backup creado: $backup" -ForegroundColor Green
    Write-Host ""
    
    Write-Host "Correcciones realizadas:" -ForegroundColor Yellow
    foreach ($correction in $corrections) {
        Write-Host "  - $correction" -ForegroundColor White
    }
    
    $envContent | Set-Content $envPath -NoNewline
    Write-Host ""
    Write-Host "✓ Archivo .env actualizado" -ForegroundColor Green
} else {
    Write-Host "✓ Todas las variables están correctamente configuradas" -ForegroundColor Green
}

Write-Host ""
Write-Host "=== Verificación completada ===" -ForegroundColor Cyan

