# Script para configurar el archivo hosts de Windows
# Para demomartinez.bsolutions.dev
# Debe ejecutarse como Administrador

Write-Host "Configurando archivo hosts para demomartinez.bsolutions.dev..." -ForegroundColor Cyan

$hostsPath = "C:\Windows\System32\drivers\etc\hosts"
$entry = "127.0.0.1    demomartinez.bsolutions.dev"

# Verificar si ya existe
$hostsContent = Get-Content $hostsPath -ErrorAction SilentlyContinue
if ($hostsContent -match "demomartinez\.bsolutions\.dev") {
    Write-Host "La entrada ya existe en el archivo hosts" -ForegroundColor Yellow
    $existingLine = $hostsContent | Where-Object { $_ -match "demomartinez\.bsolutions\.dev" }
    Write-Host "Linea existente: $existingLine" -ForegroundColor Gray
} else {
    try {
        Add-Content -Path $hostsPath -Value $entry -Force
        Write-Host "Entrada agregada exitosamente al archivo hosts" -ForegroundColor Green
        Write-Host "Entrada: $entry" -ForegroundColor White
    } catch {
        Write-Host "Error al agregar entrada: $_" -ForegroundColor Red
        Write-Host "Asegurate de ejecutar este script como Administrador" -ForegroundColor Yellow
        exit 1
    }
}

# Limpiar cache DNS
Write-Host ""
Write-Host "Limpiando cache DNS..." -ForegroundColor Cyan
ipconfig /flushdns | Out-Null
Write-Host "Cache DNS limpiada" -ForegroundColor Green

Write-Host ""
Write-Host "Configuracion completada!" -ForegroundColor Green
Write-Host "Ahora puedes acceder a: http://demomartinez.bsolutions.dev" -ForegroundColor Cyan

