@echo off
chcp 65001 >nul
echo ========================================
echo   Iniciando Servidor demomartinez.bsolutions.dev
echo ========================================
echo.

REM Verificar que XAMPP esté instalado
if not exist "C:\xampp\apache\bin\httpd.exe" (
    echo [ERROR] XAMPP no encontrado en C:\xampp
    echo Por favor, verifica la instalación de XAMPP.
    pause
    exit /b 1
)

echo [1/5] Verificando servicios de XAMPP...
echo.

REM Verificar si Apache está corriendo
tasklist /FI "IMAGENAME eq httpd.exe" 2>NUL | find /I /N "httpd.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [✓] Apache está corriendo
) else (
    echo [!] Apache NO está corriendo, iniciando...
    cd /d "C:\xampp"
    call apache_start.bat
    timeout /t 3 /nobreak >nul
)

REM Verificar si MySQL está corriendo
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [✓] MySQL está corriendo
) else (
    echo [!] MySQL NO está corriendo, iniciando...
    cd /d "C:\xampp"
    call mysql_start.bat
    timeout /t 3 /nobreak >nul
)

echo.
echo [2/5] Verificando configuración del VirtualHost...
echo.

REM Verificar que el archivo index.php existe
if exist "C:\xampp\php\www\martinezdevice\public\index.php" (
    echo [✓] Archivo index.php encontrado
) else (
    echo [ERROR] No se encontró el archivo index.php
    echo Ruta esperada: C:\xampp\php\www\martinezdevice\public\index.php
    pause
    exit /b 1
)

echo.
echo [3/5] Reiniciando Apache para aplicar cambios...
echo.

REM Detener Apache
echo Deteniendo Apache...
taskkill /F /IM httpd.exe /T 2>nul
timeout /t 3 /nobreak >nul

REM Iniciar Apache
echo Iniciando Apache...
cd /d "C:\xampp\apache\bin"
start "" /MIN httpd.exe -k start
timeout /t 3 /nobreak >nul

echo [✓] Apache reiniciado
echo.

echo [4/5] Verificando configuración del VirtualHost...
echo.

REM Verificar sintaxis de Apache
cd /d "C:\xampp\apache\bin"
httpd.exe -t >nul 2>&1
if "%ERRORLEVEL%"=="0" (
    echo [✓] Sintaxis de Apache correcta
) else (
    echo [ERROR] Error en la sintaxis de Apache
    echo Ejecutando verificación detallada...
    httpd.exe -t
    pause
    exit /b 1
)

echo.
echo [5/5] Verificando VirtualHost activo...
echo.

REM Verificar que el VirtualHost está activo
httpd.exe -S | findstr /C:"demomartinez.bsolutions.dev" >nul
if "%ERRORLEVEL%"=="0" (
    echo [✓] VirtualHost demomartinez.bsolutions.dev está activo
    echo.
    echo Mostrando configuración de VirtualHosts:
    httpd.exe -S
) else (
    echo [!] VirtualHost no encontrado en la configuración
    echo Verificando configuración...
    httpd.exe -S
)

echo.
echo ========================================
echo   Servidor iniciado correctamente!
echo ========================================
echo.
echo URLs de acceso:
echo   - http://demomartinez.bsolutions.dev
echo   - http://demomartinez
echo.
echo Credenciales de administrador:
echo   Email: admin@martinezservice.com
echo   Password: password
echo.
echo Presiona cualquier tecla para abrir en el navegador...
pause >nul

REM Abrir en el navegador
start http://demomartinez.bsolutions.dev

echo.
echo Servidor corriendo. Presiona Ctrl+C para detener.
echo.

