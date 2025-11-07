@echo off
chcp 65001 >nul
echo ========================================
echo   REINICIAR SERVIDOR LOCAL
echo ========================================
echo.

echo Deteniendo servidores PHP existentes...
taskkill /F /IM php.exe /T 2>nul
timeout /t 2 /nobreak >nul

echo.
echo Limpiando cachés...
cd /d "C:\xampp\php\www\martinezdevice"
php artisan optimize:clear >nul 2>&1

echo.
echo Iniciando servidor Laravel en puerto 3001...
echo.
echo URL: http://localhost:3001
echo.
echo Credenciales:
echo   Email: admin@martinezservice.com
echo   Password: password
echo.
echo Presiona Ctrl+C para detener el servidor
echo.

php artisan serve --host=0.0.0.0 --port=3001

