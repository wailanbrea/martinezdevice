# 🔧 Solución: Error 520 después del Login

## ⚠️ Problema

Cuando intentas hacer login en https://demomartinez.bsolutions.dev, obtienes el error 520 de Cloudflare.

**Causa:** El servidor PHP/Nginx está teniendo problemas al procesar la petición POST del login, posiblemente por:
- Timeout de PHP-FPM muy corto
- Problemas con las sesiones
- Falta de permisos en storage
- Configuración de Nginx incorrecta

---

## ✅ Solución Automática (Recomendada)

### Conectarse al VPS

```bash
ssh usuario@62.171.174.191
```

### Ejecutar Script de Solución

```bash
cd /var/www/martinezdevice
sudo bash solucionar-error-520-login.sh
```

Este script automáticamente:
1. ✅ Crea/verifica directorios de sesiones
2. ✅ Limpia sesiones antiguas
3. ✅ Configura permisos correctos (www-data:www-data 775)
4. ✅ Limpia y regenera cachés de Laravel
5. ✅ Aumenta timeout de PHP-FPM a 300 segundos
6. ✅ Configura timeout de Nginx
7. ✅ Reinicia servicios

---

## 🔍 Diagnóstico Manual

Si quieres ver qué está pasando antes de aplicar la solución:

```bash
cd /var/www/martinezdevice
sudo bash diagnosticar-vps-520.sh
```

---

## 🛠️ Solución Manual (Paso a Paso)

### 1. Crear y Limpiar Directorios de Sesiones

```bash
cd /var/www/martinezdevice
sudo mkdir -p storage/framework/sessions
sudo mkdir -p storage/framework/views
sudo mkdir -p storage/framework/cache
sudo rm -rf storage/framework/sessions/*
```

### 2. Configurar Permisos

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 3. Limpiar Cachés de Laravel

```bash
sudo php artisan optimize:clear
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
```

### 4. Aumentar Timeout de PHP-FPM

```bash
sudo nano /etc/php/8.3/fpm/pool.d/www.conf
```

Agrega o modifica estas líneas:

```ini
request_terminate_timeout = 300
pm.max_children = 50
```

Guarda y cierra (Ctrl+X, Y, Enter)

### 5. Configurar Timeout de Nginx

```bash
sudo nano /etc/nginx/sites-available/demomartinez.bsolutions.dev
```

Dentro de `location ~ \.php$`, agrega:

```nginx
fastcgi_read_timeout 300;
```

Ejemplo completo:

```nginx
location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
    fastcgi_hide_header X-Powered-By;
    fastcgi_read_timeout 300;  # <--- Agregar esta línea
}
```

### 6. Verificar Configuración de Nginx

```bash
sudo nginx -t
```

Debe mostrar: `syntax is ok` y `test is successful`

### 7. Reiniciar Servicios

```bash
sudo systemctl restart php8.3-fpm
sudo systemctl reload nginx
```

### 8. Verificar que los Servicios Están Corriendo

```bash
sudo systemctl status php8.3-fpm
sudo systemctl status nginx
```

---

## 🧪 Probar la Solución

### Desde el Servidor

```bash
curl -H "Host: demomartinez.bsolutions.dev" http://localhost
```

Debe responder con código 200 o 302.

### Desde el Navegador

1. Limpia la caché del navegador (Ctrl+Shift+Delete)
2. O usa modo incógnito
3. Ve a: https://demomartinez.bsolutions.dev/login
4. Intenta hacer login con:
   - **Email:** admin@martinezservice.com
   - **Password:** password

---

## 📝 Ver Logs en Tiempo Real

### Logs de Nginx

```bash
sudo tail -f /var/log/nginx/demomartinez-error.log
```

### Logs de Laravel

```bash
sudo tail -f /var/www/martinezdevice/storage/logs/laravel.log
```

### Logs de PHP-FPM

```bash
sudo tail -f /var/log/php8.3-fpm.log
```

---

## 🐛 Si el Problema Persiste

### 1. Verificar Variables de .env

```bash
cd /var/www/martinezdevice
sudo nano .env
```

Asegúrate de que estas variables estén configuradas:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:... (debe estar presente)
APP_URL=https://demomartinez.bsolutions.dev

SESSION_DRIVER=file
SESSION_LIFETIME=120

CACHE_STORE=file
LOG_CHANNEL=stack
```

### 2. Verificar que la Base de Datos Está Conectada

```bash
sudo php artisan db:show
```

### 3. Verificar Migraciones

```bash
sudo php artisan migrate:status
```

### 4. Regenerar Caché de Configuración

```bash
sudo php artisan config:clear
sudo php artisan config:cache
```

### 5. Desactivar Temporalmente Cloudflare

En Cloudflare Dashboard:
1. Ve a DNS
2. Haz clic en el registro A de `demomartinez`
3. Desactiva el proxy (debe aparecer en gris, no naranja)
4. Espera unos minutos
5. Prueba el login directamente: http://62.171.174.191

Si funciona sin Cloudflare, el problema es la configuración de Cloudflare.

---

## ⚙️ Configuración Óptima de PHP-FPM

Para evitar problemas futuros, configura PHP-FPM correctamente:

```bash
sudo nano /etc/php/8.3/fpm/pool.d/www.conf
```

Configuración recomendada:

```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500
request_terminate_timeout = 300
```

Reinicia PHP-FPM:

```bash
sudo systemctl restart php8.3-fpm
```

---

## 📊 Checklist de Verificación

- [ ] Directorios de sesiones existen y tienen permisos correctos
- [ ] storage/ y bootstrap/cache/ tienen permisos 775
- [ ] storage/ y bootstrap/cache/ son propiedad de www-data
- [ ] Timeout de PHP-FPM configurado (300 segundos)
- [ ] Timeout de Nginx configurado (fastcgi_read_timeout 300)
- [ ] PHP-FPM está corriendo
- [ ] Nginx está corriendo
- [ ] MySQL está corriendo
- [ ] .env configurado correctamente
- [ ] APP_KEY presente en .env
- [ ] Migraciones ejecutadas
- [ ] Cachés de Laravel regenerados

---

## ✅ Resultado Esperado

Después de aplicar estas soluciones:
- ✅ El login funciona correctamente
- ✅ No hay error 520
- ✅ La sesión se mantiene después del login
- ✅ Puedes acceder al dashboard

---

**¿Necesitas más ayuda?** Revisa los logs en tiempo real mientras intentas hacer login para identificar el error exacto.

