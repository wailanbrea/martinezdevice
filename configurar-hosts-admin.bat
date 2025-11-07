@echo off
REM Script para configurar el archivo hosts de Windows
REM Debe ejecutarse como Administrador

echo ========================================
echo   Configurar Archivo Hosts
echo   demomartinez.bsolutions.dev
echo ========================================
echo.

REM Verificar permisos de administrador
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] Este script debe ejecutarse como Administrador
    echo.
    echo Por favor:
    echo 1. Cierra esta ventana
    echo 2. Haz clic derecho en este archivo
    echo 3. Selecciona "Ejecutar como administrador"
    echo.
    pause
    exit /b 1
)

echo [OK] Ejecutando como Administrador
echo.

set HOSTS_PATH=C:\Windows\System32\drivers\etc\hosts
set ENTRY=127.0.0.1    demomartinez.bsolutions.dev

echo Verificando archivo hosts...
echo.

REM Verificar si ya existe la entrada
findstr /C:"demomartinez.bsolutions.dev" %HOSTS_PATH% >nul 2>&1
if %errorLevel% equ 0 (
    echo [OK] La entrada ya existe en el archivo hosts
    echo.
    echo Entrada encontrada:
    findstr /C:"demomartinez.bsolutions.dev" %HOSTS_PATH%
    echo.
) else (
    echo Agregando entrada al archivo hosts...
    echo %ENTRY% >> %HOSTS_PATH%
    if %errorLevel% equ 0 (
        echo [OK] Entrada agregada exitosamente
        echo.
        echo Entrada agregada: %ENTRY%
        echo.
    ) else (
        echo [ERROR] No se pudo agregar la entrada
        echo.
        pause
        exit /b 1
    )
)

echo Limpiando cache DNS...
ipconfig /flushdns >nul 2>&1
echo [OK] Cache DNS limpiada
echo.

echo ========================================
echo   Configuracion Completada!
echo ========================================
echo.
echo Ahora puedes acceder a:
echo   http://demomartinez.bsolutions.dev
echo.
echo Credenciales:
echo   Email: admin@martinezservice.com
echo   Password: password
echo.
pause

