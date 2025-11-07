# 🔧 Solución Error ERR_CONNECTION_RESET en Login

## ⚠️ Problema

Cuando intentas hacer login, aparece el error:
```
ERR_CONNECTION_RESET
Se ha restablecido la conexión.
```

## 🔍 Causa

Este error ocurre cuando:
1. El servidor Laravel (`php artisan serve`) se cierra inesperadamente
2. Hay un error fatal en PHP que causa que el servidor se detenga
3. Hay un problema con la configuración de caché o sesiones

## ✅ Solución

### Paso 1: Verificar que el Servidor Está Corriendo

```bash
# Verificar procesos PHP
tasklist | findstr php

# Si no hay procesos, iniciar el servidor
cd C:\xampp\php\www\martinezdevice
php artisan serve --host=0.0.0.0 --port=3001
```

### Paso 2: Limpiar Cachés

```bash
cd C:\xampp\php\www\martinezdevice
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Paso 3: Verificar Base de Datos

```bash
# Verificar que MySQL está corriendo
tasklist | findstr mysqld

# Verificar conexión
php artisan tinker --execute="DB::connection()->getPdo();"

# Verificar usuario admin
php artisan tinker --execute="App\Models\User::where('email', 'admin@martinezservice.com')->exists();"
```

### Paso 4: Verificar Archivo .env

Asegúrate de que el archivo `.env` tenga estas configuraciones:

```env
CACHE_STORE=file
CACHE_DRIVER=file
SESSION_DRIVER=file
LOG_CHANNEL=stack
```

### Paso 5: Reiniciar el Servidor

```bash
# Detener todos los procesos PHP
taskkill /F /IM php.exe /T

# Esperar 2 segundos
timeout /t 2

# Iniciar servidor nuevamente
cd C:\xampp\php\www\martinezdevice
php artisan serve --host=0.0.0.0 --port=3001
```

## 🚀 Scripts Disponibles

### Reiniciar Servidor
```bash
.\reiniciar-servidor-local.bat
```

### Verificar Configuración
```bash
.\verificar-login-local.bat
```

## 🐛 Si el Problema Persiste

### Ver Logs en Tiempo Real

1. Abre una terminal
2. Ejecuta:
```bash
cd C:\xampp\php\www\martinezdevice
Get-Content storage\logs\laravel.log -Wait -Tail 20
```

3. En otra terminal, intenta hacer login
4. Observa los errores que aparecen en tiempo real

### Verificar Errores de PHP

```bash
# Ver configuración de PHP
php -i | findstr error_log

# Verificar límites de PHP
php -i | findstr "max_execution_time\|memory_limit"
```

### Verificar Permisos

```bash
# Verificar que storage tiene permisos de escritura
icacls "C:\xampp\php\www\martinezdevice\storage" /T
```

## 📝 Checklist

- [ ] MySQL está corriendo
- [ ] Servidor Laravel está corriendo en puerto 3001
- [ ] Cachés limpiados
- [ ] Base de datos accesible
- [ ] Usuario admin existe
- [ ] Archivo .env configurado correctamente
- [ ] Permisos de storage correctos

## 🎯 Resultado Esperado

Después de seguir estos pasos:
- ✅ El servidor debe estar corriendo en `http://localhost:3001`
- ✅ El login debe funcionar correctamente
- ✅ No debe aparecer ERR_CONNECTION_RESET

---

**Si el problema persiste, comparte los logs de Laravel para diagnosticar mejor.**

