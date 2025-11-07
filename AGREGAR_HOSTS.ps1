# Script para agregar entrada al archivo hosts
# Debe ejecutarse como Administrador

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Configurar Archivo Hosts" -ForegroundColor Cyan
Write-Host "  demomartinez.bsolutions.dev" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Verificar permisos de administrador
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "ERROR: Este script requiere permisos de Administrador" -ForegroundColor Red
    Write-Host ""
    Write-Host "Por favor:" -ForegroundColor Yellow
    Write-Host "1. Cierra esta ventana" -ForegroundColor White
    Write-Host "2. Haz clic derecho en este archivo" -ForegroundColor White
    Write-Host "3. Selecciona 'Ejecutar con PowerShell' o 'Ejecutar como administrador'" -ForegroundColor White
    Write-Host ""
    pause
    exit 1
}

$hostsPath = "C:\Windows\System32\drivers\etc\hosts"
$entry = "127.0.0.1    demomartinez.bsolutions.dev"

Write-Host "Verificando archivo hosts..." -ForegroundColor Yellow
Write-Host ""

# Verificar si ya existe
$hostsContent = Get-Content $hostsPath -ErrorAction SilentlyContinue
if ($hostsContent -match "demomartinez\.bsolutions\.dev") {
    Write-Host "OK: La entrada ya existe en el archivo hosts" -ForegroundColor Green
    Write-Host ""
    $existingLine = $hostsContent | Where-Object { $_ -match "demomartinez\.bsolutions\.dev" }
    Write-Host "Entrada encontrada:" -ForegroundColor Cyan
    Write-Host $existingLine -ForegroundColor White
    Write-Host ""
} else {
    Write-Host "Agregando entrada al archivo hosts..." -ForegroundColor Yellow
    try {
        Add-Content -Path $hostsPath -Value $entry -Force
        Write-Host "OK: Entrada agregada exitosamente" -ForegroundColor Green
        Write-Host ""
        Write-Host "Entrada agregada: $entry" -ForegroundColor White
        Write-Host ""
    } catch {
        Write-Host "ERROR: No se pudo agregar la entrada" -ForegroundColor Red
        Write-Host "Error: $_" -ForegroundColor Red
        Write-Host ""
        pause
        exit 1
    }
}

Write-Host "Limpiando cache DNS..." -ForegroundColor Yellow
try {
    ipconfig /flushdns | Out-Null
    Write-Host "OK: Cache DNS limpiada" -ForegroundColor Green
    Write-Host ""
} catch {
    Write-Host "ADVERTENCIA: No se pudo limpiar la cache DNS" -ForegroundColor Yellow
    Write-Host "Ejecuta manualmente: ipconfig /flushdns" -ForegroundColor White
    Write-Host ""
}

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Configuracion Completada!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Ahora puedes acceder a:" -ForegroundColor Cyan
Write-Host "  http://demomartinez.bsolutions.dev" -ForegroundColor Yellow
Write-Host ""
Write-Host "Credenciales de administrador:" -ForegroundColor Cyan
Write-Host "  Email: admin@martinezservice.com" -ForegroundColor White
Write-Host "  Password: password" -ForegroundColor White
Write-Host ""
Write-Host "Presiona cualquier tecla para continuar..." -ForegroundColor Gray
pause

