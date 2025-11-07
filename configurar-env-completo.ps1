# Script para configurar .env completo desde .env.example
# Ejecutar como: .\configurar-env-completo.ps1

$envExample = Join-Path $PSScriptRoot ".env.example"
$envFile = Join-Path $PSScriptRoot ".env"

Write-Host "=== Configuración de .env para Martinez Service ===" -ForegroundColor Cyan
Write-Host ""

# Verificar que existe .env.example
if (-not (Test-Path $envExample)) {
    Write-Host "ERROR: No se encontró .env.example" -ForegroundColor Red
    exit 1
}

# Crear backup del .env actual si existe
if (Test-Path $envFile) {
    $backup = "$envFile.backup." + (Get-Date -Format "yyyyMMdd_HHmmss")
    Copy-Item $envFile $backup
    Write-Host "✓ Backup creado: $backup" -ForegroundColor Green
}

# Copiar .env.example a .env
Copy-Item $envExample $envFile
Write-Host "✓ Archivo .env creado desde .env.example" -ForegroundColor Green

# Leer el contenido
$content = Get-Content $envFile -Raw

# Generar APP_KEY si está vacío
if ($content -match "APP_KEY=\s*$") {
    Write-Host ""
    Write-Host "Generando APP_KEY..." -ForegroundColor Yellow
    
    $output = php artisan key:generate --show 2>&1
    
    if ($LASTEXITCODE -eq 0) {
        # Extraer la clave del output
        $key = $output | Select-String -Pattern "base64:.*" | ForEach-Object { $_.Matches.Value }
        
        if ($key) {
            $content = $content -replace "APP_KEY=\s*$", "APP_KEY=$key"
            Write-Host "✓ APP_KEY generada correctamente" -ForegroundColor Green
        }
    }
}

# Guardar cambios
$content | Set-Content $envFile -NoNewline

Write-Host ""
Write-Host "=== Configuración completada ===" -ForegroundColor Green
Write-Host ""
Write-Host "Variables configuradas:" -ForegroundColor Cyan
Write-Host "  - LOG_CHANNEL=stack" -ForegroundColor White
Write-Host "  - LOG_STACK=single" -ForegroundColor White
Write-Host "  - CACHE_STORE=file" -ForegroundColor White
Write-Host "  - DB_DATABASE=martinez_device" -ForegroundColor White
Write-Host "  - FRONTEND_URL=http://localhost:3000" -ForegroundColor White
Write-Host ""
Write-Host "Próximos pasos:" -ForegroundColor Yellow
Write-Host "1. php artisan config:clear" -ForegroundColor White
Write-Host "2. php artisan cache:clear" -ForegroundColor White
Write-Host "3. php artisan migrate --seed" -ForegroundColor White
Write-Host ""
