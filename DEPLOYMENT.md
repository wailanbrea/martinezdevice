# 📦 Guía de Deployment - Martínez Service

## 🚀 Pasos para Subir al Repositorio

### 1. Primera vez (desde tu máquina local)

```bash
# Verificar que estás en la rama main
git branch

# Hacer push al repositorio
git push -u origin main
```

Si el repositorio está vacío, usa:
```bash
git push -u origin main --force
```

### 2. En el servidor (primera vez)

```bash
# Clonar el repositorio
git clone https://github.com/wailanbrea/martinezdevice.git
cd martinezdevice

# Crear archivo .env
cp .env.example .env

# Editar .env con las credenciales del servidor
nano .env  # o usar tu editor preferido

# Generar APP_KEY
php artisan key:generate

# Instalar dependencias
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Crear base de datos
mysql -u root -p
CREATE DATABASE martinez_device CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# Ejecutar migraciones
php artisan migrate --force

# Crear enlace simbólico
php artisan storage:link

# Dar permisos
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Optimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔄 Actualizar en el Servidor

### Opción 1: Script Automático (Recomendado)

**Linux/Mac:**
```bash
./deploy.sh
```

**Windows:**
```cmd
deploy.bat
```

### Opción 2: Manual

```bash
# 1. Activar modo mantenimiento
php artisan down

# 2. Obtener cambios
git pull origin main

# 3. Instalar dependencias
composer install --no-dev --optimize-autoloader
npm install
npm run build

# 4. Ejecutar migraciones
php artisan migrate --force

# 5. Limpiar cachés
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 6. Optimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 7. Desactivar mantenimiento
php artisan up
```

## ⚙️ Configuración del Servidor

### Variables de Entorno Importantes (.env)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=martinez_device
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña_segura

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Permisos de Archivos

```bash
# Permisos para storage y cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Permisos para archivos
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
```

## 🔐 Seguridad

1. **Nunca subir .env a Git** - Ya está en .gitignore
2. **Cambiar APP_KEY** en producción
3. **APP_DEBUG=false** en producción
4. **Usar contraseñas seguras** para la base de datos
5. **Configurar HTTPS** para el servidor

## 📝 Notas Importantes

- El archivo `.env` **NO** se sube al repositorio
- Cada servidor debe tener su propio `.env`
- Las migraciones se ejecutan automáticamente con `--force` en producción
- Los assets (CSS/JS) deben compilarse después de cada actualización

## 🆘 Solución de Problemas

### Error: "No such file or directory"
```bash
# Crear directorios faltantes
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
chmod -R 775 storage
```

### Error: "Permission denied"
```bash
# Dar permisos correctos
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Error: "Class not found"
```bash
# Regenerar autoload
composer dump-autoload
php artisan optimize:clear
php artisan optimize
```

### Base de datos no actualizada
```bash
# Verificar migraciones pendientes
php artisan migrate:status

# Ejecutar migraciones
php artisan migrate --force
```

---

**¡Listo para deployment!** 🚀

