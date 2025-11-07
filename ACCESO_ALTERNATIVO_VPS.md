# 🔧 Métodos Alternativos para Acceder al VPS

## ⚠️ Problema: SSH Connection Refused

El puerto 22 (SSH) está cerrado o bloqueado. Necesitas usar un método alternativo para acceder al VPS.

---

## 🌐 Método 1: Panel de Control del VPS (Recomendado)

### Paso 1: Acceder al Panel de Control

Tu VPS probablemente tiene un panel de control web. Accede desde:

**Opciones comunes:**
- **Panel del proveedor VPS** (OVH, Hetzner, DigitalOcean, etc.)
- **URL directa:** https://62.171.174.191:8443 (Plesk)
- **URL directa:** https://62.171.174.191:2087 (cPanel)
- O el panel que te proporcionó tu proveedor

### Paso 2: Buscar "Terminal" o "Consola"

Una vez en el panel:
1. Busca una opción llamada: **"Terminal"**, **"SSH Terminal"**, **"Console"**, o **"Shell"**
2. Abre el terminal web
3. Ya estás conectado al servidor

### Paso 3: Ejecutar los Comandos

En el terminal web, ejecuta:

```bash
cd /var/www/martinezdevice
git pull origin main
bash solucionar-error-520-login.sh
```

---

## 🖥️ Método 2: Panel del Proveedor VPS

### Si usas OVH:
1. Ve a https://www.ovh.com/manager/
2. Selecciona tu VPS
3. Busca "KVM" o "Consola VNC"
4. Se abrirá una consola virtual

### Si usas DigitalOcean:
1. Ve a https://cloud.digitalocean.com/
2. Selecciona tu Droplet
3. Click en "Access" → "Launch Console"

### Si usas Hetzner:
1. Ve a https://console.hetzner.cloud/
2. Selecciona tu servidor
3. Click en "Console"

### Si usas Vultr:
1. Ve a https://my.vultr.com/
2. Selecciona tu servidor
3. Click en "View Console"

---

## 📱 Método 3: Crear Script PHP para Ejecutar Comandos

Si tienes acceso FTP o al gestor de archivos, crea este archivo:

### Archivo: `/var/www/martinezdevice/public/fix-520.php`

```php
<?php
// Script temporal para solucionar el error 520
// BORRAR después de usarlo por seguridad

// Clave de seguridad (cámbiala por una aleatoria)
$clave = 'mi-clave-secreta-123';

if (!isset($_GET['key']) || $_GET['key'] !== $clave) {
    die('Acceso denegado');
}

echo "<h1>Solucionando Error 520</h1>";
echo "<pre>";

// Cambiar al directorio del proyecto
chdir('/var/www/martinezdevice');

// 1. Actualizar desde GitHub
echo "=== Actualizando desde GitHub ===\n";
system('git pull origin main 2>&1');
echo "\n";

// 2. Limpiar sesiones
echo "=== Limpiando sesiones ===\n";
system('rm -rf storage/framework/sessions/* 2>&1');
echo "✓ Sesiones limpiadas\n\n";

// 3. Configurar permisos
echo "=== Configurando permisos ===\n";
system('chown -R www-data:www-data storage bootstrap/cache 2>&1');
system('chmod -R 775 storage bootstrap/cache 2>&1');
echo "✓ Permisos configurados\n\n";

// 4. Limpiar cachés de Laravel
echo "=== Limpiando cachés ===\n";
system('php artisan optimize:clear 2>&1');
echo "\n";

// 5. Cachear configuración
echo "=== Cacheando configuración ===\n";
system('php artisan config:cache 2>&1');
system('php artisan route:cache 2>&1');
system('php artisan view:cache 2>&1');
echo "\n";

// 6. Reiniciar PHP-FPM
echo "=== Reiniciando PHP-FPM ===\n";
system('systemctl restart php8.3-fpm 2>&1');
echo "✓ PHP-FPM reiniciado\n\n";

// 7. Recargar Nginx
echo "=== Recargando Nginx ===\n";
system('systemctl reload nginx 2>&1');
echo "✓ Nginx recargado\n\n";

echo "=== ✅ COMPLETADO ===\n";
echo "Prueba el login nuevamente: https://demomartinez.bsolutions.dev/login\n";

echo "</pre>";

// IMPORTANTE: Borrar este archivo después de usarlo
?>
```

### Cómo usar:

1. Sube el archivo `fix-520.php` a `/var/www/martinezdevice/public/`
2. Accede desde tu navegador: `https://demomartinez.bsolutions.dev/fix-520.php?key=mi-clave-secreta-123`
3. **IMPORTANTE:** Borra el archivo después de usarlo

---

## 🔧 Método 4: FTP + Edición Manual

Si tienes acceso FTP:

### 1. Conectar por FTP
- Host: 62.171.174.191
- Usuario: tu usuario FTP
- Puerto: 21 (o el que uses)

### 2. Navegar a `/var/www/martinezdevice/`

### 3. Editar manualmente los archivos de configuración

#### Archivo: `/etc/nginx/sites-available/demomartinez.bsolutions.dev`

Busca la sección `location ~ \.php$` y agrega:

```nginx
fastcgi_read_timeout 300;
```

#### Archivo: `/etc/php/8.3/fpm/pool.d/www.conf`

Agrega al final:

```ini
request_terminate_timeout = 300
pm.max_children = 50
```

### 4. Reiniciar servicios (necesitas acceso al panel)

---

## 🆘 Método 5: Contactar al Proveedor del VPS

Si ninguno de los métodos anteriores funciona:

1. **Contacta a tu proveedor de VPS**
2. Pídeles que:
   - Habiliten SSH en el puerto 22, o
   - Te den acceso a una consola web, o
   - Ejecuten los comandos por ti

---

## 📋 Comandos Completos para Ejecutar

Cuando consigas acceso, ejecuta estos comandos:

```bash
# Ir al directorio
cd /var/www/martinezdevice

# Actualizar código
git pull origin main

# Limpiar sesiones
rm -rf storage/framework/sessions/*

# Configurar permisos
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Limpiar cachés
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Configurar timeout de PHP-FPM
sed -i 's/^request_terminate_timeout.*/request_terminate_timeout = 300/' /etc/php/8.3/fpm/pool.d/www.conf
echo "request_terminate_timeout = 300" >> /etc/php/8.3/fpm/pool.d/www.conf

# Reiniciar servicios
systemctl restart php8.3-fpm
systemctl reload nginx
```

---

## 🔍 Averiguar tu Panel de Control

Para saber qué panel tienes, prueba estas URLs en tu navegador:

- https://62.171.174.191:8443 (Plesk)
- https://62.171.174.191:2087 (cPanel)
- https://62.171.174.191:10000 (Webmin)
- O revisa el email de bienvenida de tu proveedor VPS

---

## ⚠️ IMPORTANTE: Habilitar SSH (Opcional)

Si quieres habilitar SSH para el futuro, desde el panel ejecuta:

```bash
# Habilitar SSH
systemctl start sshd
systemctl enable sshd

# Abrir puerto en firewall
ufw allow 22/tcp

# O si usas firewalld
firewall-cmd --permanent --add-service=ssh
firewall-cmd --reload
```

---

**¿Cuál es tu proveedor de VPS?** Dime y te ayudo con las instrucciones específicas.

