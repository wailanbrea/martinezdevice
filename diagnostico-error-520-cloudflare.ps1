# Script de Diagnóstico para Error 520 de Cloudflare
# Error 520: Web server is returning an unknown error

Write-Host "=== DIAGNÓSTICO ERROR 520 CLOUDFLARE ===" -ForegroundColor Cyan
Write-Host ""

# 1. Verificar Apache local
Write-Host "1. Verificando Apache local..." -ForegroundColor Yellow
$apache = Get-Process -Name httpd -ErrorAction SilentlyContinue
if ($apache) {
    Write-Host "   ✓ Apache está corriendo (PID: $($apache[0].Id))" -ForegroundColor Green
} else {
    Write-Host "   ✗ Apache NO está corriendo" -ForegroundColor Red
    Write-Host "   → Inicia Apache desde XAMPP Control Panel" -ForegroundColor Yellow
}

# 2. Verificar puerto 80
Write-Host "`n2. Verificando puerto 80..." -ForegroundColor Yellow
$port80 = Test-NetConnection -ComputerName localhost -Port 80 -InformationLevel Quiet -WarningAction SilentlyContinue
if ($port80) {
    Write-Host "   ✓ Puerto 80 está abierto" -ForegroundColor Green
} else {
    Write-Host "   ✗ Puerto 80 NO está abierto" -ForegroundColor Red
}

# 3. Verificar VirtualHost
Write-Host "`n3. Verificando VirtualHost..." -ForegroundColor Yellow
$vhosts = C:\xampp\apache\bin\httpd.exe -S 2>&1 | Select-String -Pattern "demomartinez"
if ($vhosts) {
    Write-Host "   ✓ VirtualHost configurado:" -ForegroundColor Green
    $vhosts | ForEach-Object { Write-Host "     $_" -ForegroundColor White }
} else {
    Write-Host "   ✗ VirtualHost NO encontrado" -ForegroundColor Red
}

# 4. Probar respuesta local
Write-Host "`n4. Probando respuesta local..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://127.0.0.1" -Headers @{"Host"="demomartinez.bsolutions.dev"} -UseBasicParsing -TimeoutSec 5 -ErrorAction Stop
    Write-Host "   ✓ Servidor local responde (Status: $($response.StatusCode))" -ForegroundColor Green
} catch {
    Write-Host "   ✗ Servidor local NO responde" -ForegroundColor Red
    Write-Host "     Error: $($_.Exception.Message)" -ForegroundColor Gray
}

# 5. Verificar configuración Cloudflare
Write-Host "`n5. Configuración de Cloudflare:" -ForegroundColor Yellow
Write-Host "   ⚠ IMPORTANTE: Cloudflare apunta a IP: 62.171.174.191 (VPS)" -ForegroundColor Yellow
Write-Host "   → La aplicación debe estar desplegada en el VPS para que funcione" -ForegroundColor White
Write-Host "   → El servidor local (Windows) solo funciona para desarrollo" -ForegroundColor White

# 6. Verificar logs de Apache
Write-Host "`n6. Últimos errores de Apache:" -ForegroundColor Yellow
$errorLog = "C:\xampp\apache\logs\demomartinez-error.log"
if (Test-Path $errorLog) {
    $errors = Get-Content $errorLog -Tail 5 -ErrorAction SilentlyContinue
    if ($errors) {
        $errors | ForEach-Object { Write-Host "   $_" -ForegroundColor Gray }
    } else {
        Write-Host "   ✓ No hay errores recientes" -ForegroundColor Green
    }
} else {
    Write-Host "   ⚠ Archivo de log no encontrado" -ForegroundColor Yellow
}

# Resumen
Write-Host "`n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Gray
Write-Host "`nDIAGNÓSTICO:" -ForegroundColor Cyan
Write-Host ""
Write-Host "El Error 520 de Cloudflare ocurre porque:" -ForegroundColor White
Write-Host "1. Cloudflare intenta conectarse a: 62.171.174.191 (VPS)" -ForegroundColor Yellow
Write-Host "2. La aplicación Laravel NO está desplegada en el VPS" -ForegroundColor Yellow
Write-Host "3. El servidor local (Windows) solo funciona para desarrollo local" -ForegroundColor Yellow
Write-Host ""
Write-Host "SOLUCIÓN:" -ForegroundColor Green
Write-Host "→ Desplegar la aplicación Laravel en el servidor VPS (62.171.174.191)" -ForegroundColor White
Write-Host "→ Seguir la guía: DESPLIEGUE_DEMOMARTINEZ_VPS.md" -ForegroundColor White
Write-Host "→ O usar el script: deploy.sh en el servidor VPS" -ForegroundColor White
Write-Host ""

