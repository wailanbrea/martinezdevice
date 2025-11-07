# 🚀 Instrucciones de Deployment en VPS

## ⚡ Deployment Rápido (Automático)

### Paso 1: Conectarse al VPS

```bash
ssh usuario@62.171.174.191
```

### Paso 2: Ejecutar el Script de Deployment

```bash
# Descargar el script
cd /tmp
wget https://raw.githubusercontent.com/wailanbrea/martinezdevice/main/deploy-vps-completo.sh

# O si ya tienes el repositorio clonado:
cd /var/www/martinezdevice
sudo bash deploy-vps-completo.sh
```

El script hará automáticamente:
- ✅ Actualizar el sistema
- ✅ Instalar PHP 8.3, Composer, Nginx, MySQL
- ✅ Clonar/Actualizar el proyecto
- ✅ Instalar dependencias de Composer
- ✅ Configurar .env
- ✅ Crear base de datos
- ✅ Ejecutar migraciones
- ✅ Configurar Nginx
- ✅ Configurar permisos
- ✅ Optimizar Laravel
- ✅ Iniciar servicios

### Paso 3: Configurar .env

Durante el deployment, el script te pedirá que edites el `.env`. Configura:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://demomartinez.bsolutions.dev

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=martinez_device
DB_USERNAME=root
DB_PASSWORD=tu_contraseña_mysql
```

### Paso 4: Configurar SSL (Opcional pero Recomendado)

```bash
sudo certbot --nginx -d demomartinez.bsolutions.dev
```

## 📋 Deployment Manual (Paso a Paso)

Si prefieres hacerlo manualmente:

### 1. Instalar Dependencias

```bash
sudo apt update
sudo apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-zip \
    php8.3-gd php8.3-mbstring php8.3-curl php8.3-xml php8.3-bcmath \
    nginx mysql-server composer git
```

### 2. Clonar Proyecto

```bash
cd /var/www
sudo git clone https://github.com/wailanbrea/martinezdevice.git
cd martinezdevice
```

### 3. Instalar Dependencias de Composer

```bash
sudo composer install --no-dev --optimize-autoloader
```

### 4. Configurar .env

```bash
sudo cp .env.example .env
sudo nano .env
# Configurar las variables necesarias
sudo php artisan key:generate
```

### 5. Configurar Base de Datos

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE martinez_device CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

```bash
sudo php artisan migrate --force
```

### 6. Configurar Permisos

```bash
sudo chown -R www-data:www-data /var/www/martinezdevice
sudo chmod -R 775 storage bootstrap/cache
```

### 7. Configurar Nginx

```bash
sudo nano /etc/nginx/sites-available/demomartinez.bsolutions.dev
```

Copiar la configuración de `nginx-demomartinez.conf` o usar la generada por el script.

```bash
sudo ln -s /etc/nginx/sites-available/demomartinez.bsolutions.dev /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 8. Optimizar Laravel

```bash
cd /var/www/martinezdevice
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
sudo php artisan optimize
```

## 🔍 Verificación

### Verificar Servicios

```bash
sudo systemctl status php8.3-fpm
sudo systemctl status nginx
sudo systemctl status mysql
```

### Verificar Respuesta del Servidor

```bash
curl -H "Host: demomartinez.bsolutions.dev" http://localhost
```

### Verificar Logs

```bash
# Logs de Nginx
sudo tail -f /var/log/nginx/demomartinez.bsolutions.dev-error.log

# Logs de Laravel
sudo tail -f /var/www/martinezdevice/storage/logs/laravel.log
```

## 🐛 Solución de Problemas

### Error 502 Bad Gateway

```bash
# Verificar que PHP-FPM esté corriendo
sudo systemctl status php8.3-fpm

# Reiniciar PHP-FPM
sudo systemctl restart php8.3-fpm
```

### Error de Permisos

```bash
sudo chown -R www-data:www-data /var/www/martinezdevice
sudo chmod -R 775 storage bootstrap/cache
```

### Error de Base de Datos

```bash
# Verificar conexión
sudo php artisan db:show

# Verificar migraciones
sudo php artisan migrate:status
```

### Limpiar Cachés

```bash
cd /var/www/martinezdevice
sudo php artisan optimize:clear
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
```

## 🔄 Actualizar el Proyecto

Para actualizar el proyecto después del deployment inicial:

```bash
cd /var/www/martinezdevice
sudo git pull origin main
sudo composer install --no-dev --optimize-autoloader
sudo php artisan migrate --force
sudo php artisan optimize:clear
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
sudo systemctl reload php8.3-fpm
```

## 📞 Información del Servidor

- **IP del VPS:** 62.171.174.191
- **Dominio:** demomartinez.bsolutions.dev
- **Ruta del Proyecto:** /var/www/martinezdevice
- **Usuario Web:** www-data
- **PHP Version:** 8.3
- **Servidor Web:** Nginx

---

**¡Listo para producción!** 🚀

