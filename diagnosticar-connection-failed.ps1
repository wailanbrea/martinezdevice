# Script de Diagnóstico: Error "Connection failed"
# Para demomartinez.bsolutions.dev

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  DIAGNÓSTICO: Connection Failed" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# 1. Verificar Apache
Write-Host "1. Verificando Apache..." -ForegroundColor Yellow
$apacheProcess = Get-Process -Name httpd -ErrorAction SilentlyContinue
if ($apacheProcess) {
    Write-Host "   ✅ Apache está corriendo (PID: $($apacheProcess.Id))" -ForegroundColor Green
} else {
    Write-Host "   ❌ Apache NO está corriendo" -ForegroundColor Red
    Write-Host "   💡 Solución: Inicia Apache desde XAMPP Control Panel" -ForegroundColor Yellow
}

# 2. Verificar MySQL
Write-Host ""
Write-Host "2. Verificando MySQL..." -ForegroundColor Yellow
$mysqlProcess = Get-Process -Name mysqld -ErrorAction SilentlyContinue
if ($mysqlProcess) {
    Write-Host "   ✅ MySQL está corriendo (PID: $($mysqlProcess.Id))" -ForegroundColor Green
} else {
    Write-Host "   ❌ MySQL NO está corriendo" -ForegroundColor Red
    Write-Host "   💡 Solución: Inicia MySQL desde XAMPP Control Panel" -ForegroundColor Yellow
}

# 3. Verificar archivo hosts
Write-Host ""
Write-Host "3. Verificando archivo hosts..." -ForegroundColor Yellow
$hostsPath = "C:\Windows\System32\drivers\etc\hosts"
$hostsContent = Get-Content $hostsPath -ErrorAction SilentlyContinue
$entry = "127.0.0.1    demomartinez.bsolutions.dev"

if ($hostsContent -match "demomartinez\.bsolutions\.dev") {
    Write-Host "   ✅ Entrada encontrada en archivo hosts" -ForegroundColor Green
    $matchingLine = $hostsContent | Where-Object { $_ -match "demomartinez\.bsolutions\.dev" }
    Write-Host "   📝 Línea: $matchingLine" -ForegroundColor Gray
} else {
    Write-Host "   ❌ NO se encontró entrada en archivo hosts" -ForegroundColor Red
    Write-Host "   💡 Solución: Agrega esta línea al archivo hosts:" -ForegroundColor Yellow
    Write-Host "      $entry" -ForegroundColor White
}

# 4. Verificar VirtualHost
Write-Host ""
Write-Host "4. Verificando VirtualHost de Apache..." -ForegroundColor Yellow
$httpdPath = "C:\xampp\apache\bin\httpd.exe"
if (Test-Path $httpdPath) {
    try {
        $syntaxCheck = & $httpdPath -t 2>&1
        if ($syntaxCheck -match "Syntax OK") {
            Write-Host "   ✅ Sintaxis de Apache correcta" -ForegroundColor Green
        } else {
            Write-Host "   ❌ Error en sintaxis de Apache:" -ForegroundColor Red
            Write-Host "   $syntaxCheck" -ForegroundColor Red
        }
        
        $vhostCheck = & $httpdPath -S 2>&1
        if ($vhostCheck -match "demomartinez") {
            Write-Host "   ✅ VirtualHost de demomartinez encontrado" -ForegroundColor Green
        } else {
            Write-Host "   ⚠️  VirtualHost de demomartinez no encontrado" -ForegroundColor Yellow
        }
    } catch {
        Write-Host "   ❌ Error al verificar VirtualHost: $_" -ForegroundColor Red
    }
} else {
    Write-Host "   ❌ No se encontró httpd.exe" -ForegroundColor Red
}

# 5. Verificar archivo index.php
Write-Host ""
Write-Host "5. Verificando archivos de Laravel..." -ForegroundColor Yellow
$indexPath = "C:\xampp\php\www\martinezdevice\public\index.php"
if (Test-Path $indexPath) {
    Write-Host "   ✅ Archivo index.php existe" -ForegroundColor Green
} else {
    Write-Host "   ❌ Archivo index.php NO existe" -ForegroundColor Red
    Write-Host "   📁 Ruta esperada: $indexPath" -ForegroundColor Gray
}

# 6. Verificar archivo .env
Write-Host ""
Write-Host "6. Verificando archivo .env..." -ForegroundColor Yellow
$envPath = "C:\xampp\php\www\martinezdevice\.env"
if (Test-Path $envPath) {
    Write-Host "   ✅ Archivo .env existe" -ForegroundColor Green
    $envContent = Get-Content $envPath
    if ($envContent -match "APP_URL.*demomartinez") {
        Write-Host "   ✅ APP_URL configurado para demomartinez" -ForegroundColor Green
    } else {
        Write-Host "   ⚠️  APP_URL no está configurado para demomartinez" -ForegroundColor Yellow
    }
} else {
    Write-Host "   ❌ Archivo .env NO existe" -ForegroundColor Red
}

# 7. Probar conexión
Write-Host ""
Write-Host "7. Probando conexión..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://127.0.0.1" -Headers @{"Host"="demomartinez.bsolutions.dev"} -UseBasicParsing -TimeoutSec 5 -ErrorAction Stop
    Write-Host "   ✅ Conexión exitosa! (Status: $($response.StatusCode))" -ForegroundColor Green
    if ($response.Content -match "Laravel|login|Martinez") {
        Write-Host "   ✅ Contenido de Laravel detectado" -ForegroundColor Green
    } else {
        Write-Host "   ⚠️  El contenido no parece ser de Laravel" -ForegroundColor Yellow
    }
} catch {
    Write-Host "   ❌ Error de conexión: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "   💡 Verifica que Apache esté corriendo y el VirtualHost esté configurado" -ForegroundColor Yellow
}

# 8. Verificar caché DNS
Write-Host ""
Write-Host "8. Verificando resolución DNS..." -ForegroundColor Yellow
try {
    $dnsResult = [System.Net.Dns]::GetHostAddresses("demomartinez.bsolutions.dev")
    Write-Host "   ✅ DNS resuelve a: $($dnsResult[0].IPAddressToString)" -ForegroundColor Green
} catch {
    Write-Host "   ⚠️  DNS no resuelve (esto es normal si usas archivo hosts)" -ForegroundColor Yellow
}

# Resumen
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  RESUMEN Y RECOMENDACIONES" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

if (-not $apacheProcess) {
    Write-Host "❌ ACCIÓN REQUERIDA: Inicia Apache desde XAMPP Control Panel" -ForegroundColor Red
}

if (-not ($hostsContent -match "demomartinez\.bsolutions\.dev")) {
    Write-Host "❌ ACCIÓN REQUERIDA: Configura el archivo hosts" -ForegroundColor Red
    Write-Host "   Ejecuta como Administrador:" -ForegroundColor Yellow
    Write-Host "   Add-Content -Path `"$hostsPath`" -Value `"$entry`"" -ForegroundColor White
    Write-Host "   ipconfig /flushdns" -ForegroundColor White
}

Write-Host ""
Write-Host "📖 Para más información, consulta:" -ForegroundColor Cyan
Write-Host "   guias y configuraciones\SOLUCION_ERROR_CONNECTION_FAILED.md" -ForegroundColor White
Write-Host ""

