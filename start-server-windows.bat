@echo off
echo Iniciando servidor Laravel en puerto 3001...
cd /d "C:\xampp\php\www\martinezdevice"
php artisan serve --host=0.0.0.0 --port=3001
pause

