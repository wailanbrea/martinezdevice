@echo off
chcp 65001 >nul
echo ========================================
echo   VERIFICACIÓN LOGIN LOCAL
echo ========================================
echo.

cd /d "C:\xampp\php\www\martinezdevice"

echo [1/5] Verificando MySQL...
tasklist | findstr mysqld >nul
if %ERRORLEVEL%==0 (
    echo [✓] MySQL está corriendo
) else (
    echo [!] MySQL NO está corriendo
    echo     Inicia MySQL desde XAMPP Control Panel
    pause
    exit /b 1
)

echo.
echo [2/5] Verificando conexión a base de datos...
php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'OK'; } catch(Exception $e) { echo 'ERROR: ' . $e->getMessage(); }" 2>nul
if %ERRORLEVEL%==0 (
    echo [✓] Conexión a base de datos OK
) else (
    echo [!] Error de conexión a base de datos
    echo     Verifica las credenciales en .env
)

echo.
echo [3/5] Verificando usuario administrador...
php artisan tinker --execute="echo App\Models\User::where('email', 'admin@martinezservice.com')->exists() ? 'EXISTE' : 'NO EXISTE';" 2>nul

echo.
echo [4/5] Limpiando cachés...
php artisan config:clear >nul 2>&1
php artisan cache:clear >nul 2>&1
php artisan route:clear >nul 2>&1
php artisan view:clear >nul 2>&1
echo [✓] Cachés limpiados

echo.
echo [5/5] Verificando permisos de storage...
if exist "storage\framework\sessions" (
    echo [✓] Directorio de sesiones existe
) else (
    echo [!] Creando directorio de sesiones...
    mkdir "storage\framework\sessions" 2>nul
)

if exist "storage\framework\cache" (
    echo [✓] Directorio de caché existe
) else (
    echo [!] Creando directorio de caché...
    mkdir "storage\framework\cache" 2>nul
)

echo.
echo ========================================
echo   VERIFICACIÓN COMPLETADA
echo ========================================
echo.
echo Credenciales:
echo   Email: admin@martinezservice.com
echo   Password: password
echo.
echo URL local: http://localhost:3001
echo.
pause

