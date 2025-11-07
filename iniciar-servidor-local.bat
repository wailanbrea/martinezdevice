@echo off
echo ========================================
echo   Iniciando Servidor Laravel Local
echo ========================================
echo.
cd /d "C:\xampp\php\www\martinezdevice"
echo Directorio: %CD%
echo.
echo Servidor iniciandose en: http://127.0.0.1:8000
echo.
echo Presiona Ctrl+C para detener el servidor
echo.
php artisan serve --host=127.0.0.1 --port=8000
pause

