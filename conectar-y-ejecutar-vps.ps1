# Script para conectar al VPS y ejecutar la solución
# Este script te conectará al VPS y ejecutará el script de solución

Write-Host "=== CONEXIÓN AL VPS - MARTÍNEZ SERVICE ===" -ForegroundColor Cyan
Write-Host ""

# Configuración
$VPS_IP = "62.171.174.191"
$VPS_USER = "root"  # Cambia esto si usas otro usuario

Write-Host "Conectando al VPS..." -ForegroundColor Yellow
Write-Host "IP: $VPS_IP" -ForegroundColor White
Write-Host "Usuario: $VPS_USER" -ForegroundColor White
Write-Host ""

# Comandos que se ejecutarán en el VPS
$comandos = @"
cd /var/www/martinezdevice
echo '=== Actualizando desde GitHub ==='
git pull origin main
echo ''
echo '=== Dando permisos de ejecución ==='
chmod +x solucionar-error-520-login.sh
echo ''
echo '=== Ejecutando script de solución ==='
bash solucionar-error-520-login.sh
"@

Write-Host "Comandos que se ejecutarán:" -ForegroundColor Cyan
Write-Host $comandos -ForegroundColor Gray
Write-Host ""

# Crear archivo temporal con los comandos
$tempFile = [System.IO.Path]::GetTempFileName()
$comandos | Out-File -FilePath $tempFile -Encoding UTF8

Write-Host "OPCIÓN 1: Conexión manual" -ForegroundColor Yellow
Write-Host "Ejecuta estos comandos:" -ForegroundColor White
Write-Host ""
Write-Host "ssh $VPS_USER@$VPS_IP" -ForegroundColor Green
Write-Host "cd /var/www/martinezdevice" -ForegroundColor Green
Write-Host "git pull origin main" -ForegroundColor Green
Write-Host "bash solucionar-error-520-login.sh" -ForegroundColor Green
Write-Host ""

Write-Host "OPCIÓN 2: Conexión con PuTTY" -ForegroundColor Yellow
Write-Host "1. Abre PuTTY" -ForegroundColor White
Write-Host "2. Conecta a: $VPS_IP" -ForegroundColor White
Write-Host "3. Usuario: $VPS_USER" -ForegroundColor White
Write-Host "4. Ejecuta los comandos mostrados arriba" -ForegroundColor White
Write-Host ""

Write-Host "¿Tienes SSH configurado en PowerShell? (S/N)" -ForegroundColor Cyan
$respuesta = Read-Host

if ($respuesta -eq "S" -or $respuesta -eq "s") {
    Write-Host ""
    Write-Host "Intentando conectar..." -ForegroundColor Yellow
    
    # Intentar conectar con SSH
    ssh "$VPS_USER@$VPS_IP" "cd /var/www/martinezdevice && git pull origin main && bash solucionar-error-520-login.sh"
} else {
    Write-Host ""
    Write-Host "Copia y pega estos comandos en tu terminal SSH:" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "ssh $VPS_USER@$VPS_IP" -ForegroundColor Green
    Write-Host "cd /var/www/martinezdevice" -ForegroundColor Green
    Write-Host "git pull origin main" -ForegroundColor Green
    Write-Host "bash solucionar-error-520-login.sh" -ForegroundColor Green
    Write-Host ""
}

Write-Host "Presiona Enter para continuar..." -ForegroundColor Gray
Read-Host

