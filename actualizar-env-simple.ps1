# Script simple para actualizar .env
$envPath = "C:\xampp\php\www\martinezdevice\.env"

Write-Host "Actualizando .env..." -ForegroundColor Yellow

# Hacer backup
Copy-Item $envPath "$envPath.backup" -Force
Write-Host "Backup creado" -ForegroundColor Green

# Leer y modificar
$lines = Get-Content $envPath

$newLines = @()
foreach ($line in $lines) {
    if ($line -match "^APP_ENV=") {
        $newLines += "APP_ENV=production"
    }
    elseif ($line -match "^APP_DEBUG=") {
        $newLines += "APP_DEBUG=false"
    }
    elseif ($line -match "^SESSION_DOMAIN=") {
        $newLines += "SESSION_DOMAIN=.bsolutions.dev"
    }
    elseif ($line -match "^BCRYPT_ROUNDS=") {
        $newLines += "BCRYPT_ROUNDS=12"
    }
    else {
        $newLines += $line
    }
}

# Agregar SESSION_SECURE_COOKIE si no existe
$hasSecureCookie = $false
foreach ($line in $newLines) {
    if ($line -match "^SESSION_SECURE_COOKIE=") {
        $hasSecureCookie = $true
        break
    }
}

if (-not $hasSecureCookie) {
    $index = 0
    for ($i = 0; $i -lt $newLines.Length; $i++) {
        if ($newLines[$i] -match "^SESSION_DOMAIN=") {
            $index = $i + 1
            break
        }
    }
    $newLines = $newLines[0..($index-1)] + "SESSION_SECURE_COOKIE=false" + $newLines[$index..($newLines.Length-1)]
}

# Guardar
$newLines | Set-Content $envPath -Encoding UTF8

Write-Host "Archivo actualizado correctamente!" -ForegroundColor Green
Write-Host ""
Write-Host "Cambios aplicados:" -ForegroundColor Cyan
Write-Host "  APP_ENV=production"
Write-Host "  APP_DEBUG=false"
Write-Host "  SESSION_DOMAIN=.bsolutions.dev"
Write-Host "  BCRYPT_ROUNDS=12"
Write-Host "  SESSION_SECURE_COOKIE=false"

