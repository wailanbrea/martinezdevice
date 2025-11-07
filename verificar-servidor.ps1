# Script de Verificación Final
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Verificacion del Servidor" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# 1. Verificar archivo hosts
Write-Host "1. Verificando archivo hosts..." -ForegroundColor Yellow
$hostsContent = Get-Content "C:\Windows\System32\drivers\etc\hosts" -ErrorAction SilentlyContinue
if ($hostsContent -match "demomartinez\.bsolutions\.dev") {
    Write-Host "   OK: Entrada encontrada en archivo hosts" -ForegroundColor Green
    $line = $hostsContent | Where-Object { $_ -match "demomartinez\.bsolutions\.dev" }
    Write-Host "   $line" -ForegroundColor Gray
} else {
    Write-Host "   ERROR: Entrada NO encontrada" -ForegroundColor Red
}
Write-Host ""

# 2. Verificar Apache
Write-Host "2. Verificando Apache..." -ForegroundColor Yellow
$apache = Get-Process -Name httpd -ErrorAction SilentlyContinue
if ($apache) {
    Write-Host "   OK: Apache esta corriendo" -ForegroundColor Green
} else {
    Write-Host "   ERROR: Apache NO esta corriendo" -ForegroundColor Red
}
Write-Host ""

# 3. Verificar MySQL
Write-Host "3. Verificando MySQL..." -ForegroundColor Yellow
$mysql = Get-Process -Name mysqld -ErrorAction SilentlyContinue
if ($mysql) {
    Write-Host "   OK: MySQL esta corriendo" -ForegroundColor Green
} else {
    Write-Host "   ERROR: MySQL NO esta corriendo" -ForegroundColor Red
}
Write-Host ""

# 4. Verificar VirtualHost
Write-Host "4. Verificando VirtualHost..." -ForegroundColor Yellow
$vhost = & "C:\xampp\apache\bin\httpd.exe" -S 2>&1 | Select-String "demomartinez"
if ($vhost) {
    Write-Host "   OK: VirtualHost configurado" -ForegroundColor Green
} else {
    Write-Host "   ERROR: VirtualHost NO encontrado" -ForegroundColor Red
}
Write-Host ""

# 5. Probar conexion
Write-Host "5. Probando conexion..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://demomartinez.bsolutions.dev" -UseBasicParsing -TimeoutSec 10 -ErrorAction Stop
    Write-Host "   OK: Servidor respondiendo (Status: $($response.StatusCode))" -ForegroundColor Green
    if ($response.Content -match "Laravel|login|Martinez") {
        Write-Host "   OK: Contenido de Laravel detectado" -ForegroundColor Green
    }
} catch {
    Write-Host "   ERROR: No se pudo conectar" -ForegroundColor Red
    Write-Host "   Mensaje: $($_.Exception.Message)" -ForegroundColor Gray
}
Write-Host ""

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Resumen" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "URL de acceso:" -ForegroundColor Cyan
Write-Host "  http://demomartinez.bsolutions.dev" -ForegroundColor Yellow
Write-Host ""
Write-Host "Credenciales:" -ForegroundColor Cyan
Write-Host "  Email: admin@martinezservice.com" -ForegroundColor White
Write-Host "  Password: password" -ForegroundColor White
Write-Host ""

