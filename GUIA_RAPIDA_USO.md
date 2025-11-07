# 📖 Guía Rápida de Uso - Martínez Service

## 🌐 URLs Disponibles

### Producción (VPS) ✅ Ya Funcionando
- **URL:** https://demomartinez.bsolutions.dev
- **Estado:** Activo y funcionando
- **Servidor:** VPS (62.171.174.191)
- **Uso:** Para usuarios finales

### Desarrollo Local (Windows)
- **URL (Laravel):** http://localhost:8000
- **URL (Apache):** http://demomartinez.bsolutions.dev
- **Uso:** Para desarrollo y pruebas locales

---

## 🚀 Inicio Rápido

### 1. Servidor de Desarrollo (Laravel)

```powershell
cd C:\xampp\php\www\martinezdevice
php artisan serve
```

Luego abre: http://localhost:8000

### 2. Servidor Apache (XAMPP)

1. Abre **XAMPP Control Panel**
2. Inicia **Apache** y **MySQL**
3. Abre: http://demomartinez.bsolutions.dev

---

## 🔧 Configuración

### Ejecutar Script de Configuración Completa

```powershell
cd C:\xampp\php\www\martinezdevice
.\configuracion-completa.ps1
```

Este script verifica:
- ✅ Archivo `.env`
- ✅ Conexión a base de datos
- ✅ Migraciones
- ✅ Permisos de directorios
- ✅ Cachés
- ✅ Apache y MySQL

---

## 📝 Comandos Útiles

### Limpiar Cachés
```powershell
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### Optimizar para Producción
```powershell
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Verificar Estado
```powershell
php artisan about
php artisan migrate:status
php artisan db:show
```

### Actualizar Base de Datos
```powershell
php artisan migrate --force
php artisan db:seed
```

---

## 🔄 Actualizar desde Git

### En Desarrollo Local
```powershell
cd C:\xampp\php\www\martinezdevice
git pull origin main
composer install
php artisan migrate
php artisan optimize:clear
```

### En Producción (VPS)
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

---

## 🐛 Solución de Problemas

### Servidor No Inicia

1. **Verificar puertos:**
```powershell
netstat -ano | findstr ":8000"
netstat -ano | findstr ":80"
```

2. **Limpiar cachés:**
```powershell
php artisan optimize:clear
```

3. **Verificar logs:**
```powershell
Get-Content storage\logs\laravel.log -Tail 50
```

### Error de Base de Datos

1. **Verificar MySQL:**
   - Abre XAMPP Control Panel
   - Verifica que MySQL esté corriendo

2. **Verificar credenciales en `.env`:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=martinez_device
DB_USERNAME=root
DB_PASSWORD=
```

3. **Probar conexión:**
```powershell
php artisan db:show
```

### Error 500

1. **Ver logs detallados:**
```powershell
Get-Content storage\logs\laravel.log -Tail 100
```

2. **Verificar permisos:**
   - `storage/` debe ser escribible
   - `bootstrap/cache/` debe ser escribible

3. **Limpiar configuración:**
```powershell
php artisan config:clear
php artisan cache:clear
```

---

## 📊 Estado Actual

### Desarrollo Local (Windows)
- ✅ Proyecto clonado
- ✅ Dependencias instaladas
- ✅ Base de datos configurada
- ✅ Migraciones ejecutadas (11 tablas)
- ✅ Apache configurado con VirtualHost

### Producción (VPS)
- ✅ Aplicación desplegada
- ✅ Nginx configurado
- ✅ PHP-FPM corriendo
- ✅ SSL configurado (HTTPS)
- ✅ Base de datos configurada
- ✅ **¡Funcionando en https://demomartinez.bsolutions.dev!**

---

## 🔐 Credenciales de Acceso

### Usuario Administrador
- **Email:** admin@martinezservice.com
- **Password:** password

⚠️ **IMPORTANTE:** Cambia la contraseña después del primer login.

---

## 📞 Información del Servidor

### VPS Producción
- **IP:** 62.171.174.191
- **Dominio:** demomartinez.bsolutions.dev
- **Servidor Web:** Nginx
- **PHP:** 8.3-FPM
- **Base de Datos:** MySQL
- **SSL:** Let's Encrypt

### Desarrollo Local
- **Servidor:** XAMPP (Apache + MySQL)
- **PHP:** 8.2.12
- **Laravel:** 11.46.1
- **Puerto Laravel:** 8000
- **Puerto Apache:** 80

---

## 📚 Documentación Adicional

- `DEPLOYMENT.md` - Guía de deployment
- `INSTRUCCIONES_DEPLOYMENT_VPS.md` - Instrucciones para VPS
- `ESTADO_PROYECTO_SERVIDOR.md` - Estado actual del proyecto
- `deploy-vps-completo.sh` - Script de deployment para VPS

---

**¡Todo configurado y funcionando!** 🚀

