# Script para actualizar .env
$envFile = 'C:\xampp\php\www\martinezdevice\.env'
$backupFile = 'C:\xampp\php\www\martinezdevice\.env.backup'

# Hacer backup
Copy-Item $envFile $backupFile -Force
Write-Host 'Backup creado: .env.backup' -ForegroundColor Green

# Leer contenido
$content = Get-Content $envFile -Raw

# Realizar cambios
$content = $content -replace 'APP_ENV=local', 'APP_ENV=production'
$content = $content -replace 'APP_DEBUG=true', 'APP_DEBUG=false'
$content = $content -replace 'SESSION_DOMAIN=', 'SESSION_DOMAIN=.bsolutions.dev'
$content = $content -replace 'BCRYPT_ROUNDS=', 'BCRYPT_ROUNDS=12'

# Agregar SESSION_SECURE_COOKIE si no existe
if ($content -notmatch 'SESSION_SECURE_COOKIE=') {
    $content = $content -replace '(SESSION_DOMAIN=.*)', "$1
SESSION_SECURE_COOKIE=false"
}

# Guardar
[System.IO.File]::WriteAllText($envFile, $content)
Write-Host 'Archivo .env actualizado correctamente!' -ForegroundColor Green
