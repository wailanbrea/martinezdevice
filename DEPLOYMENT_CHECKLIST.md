# ✅ Checklist de Deployment - Martínez Service

Usa este checklist para asegurarte de que todo esté configurado correctamente antes y después del deployment.

## 📋 Pre-Deployment

### Requisitos del Sistema
- [ ] PHP >= 8.3 instalado
- [ ] Extensiones PHP necesarias instaladas (pdo, pdo_mysql, mbstring, xml, openssl, json, gd, zip, etc.)
- [ ] Composer instalado
- [ ] Node.js y NPM instalados (para compilar assets)
- [ ] MySQL/MariaDB instalado y corriendo
- [ ] Servidor web (Nginx o Apache) instalado y configurado
- [ ] Git instalado

### Verificación de Requisitos
```bash
# Ejecutar script de verificación
chmod +x check-requirements.sh
./check-requirements.sh
```

### Configuración Inicial
- [ ] Repositorio clonado en el servidor
- [ ] Archivo `.env` creado desde `.env.example`
- [ ] Variables de entorno configuradas en `.env`:
  - [ ] `APP_ENV=production`
  - [ ] `APP_DEBUG=false`
  - [ ] `APP_URL` configurado con tu dominio
  - [ ] `DB_*` configuradas correctamente
  - [ ] `APP_KEY` generado (`php artisan key:generate`)

### Base de Datos
- [ ] Base de datos creada: `martinez_device`
- [ ] Usuario de base de datos creado con permisos
- [ ] Conexión a base de datos verificada (`php artisan db:show`)

### Permisos
- [ ] Permisos de `storage` configurados (775)
- [ ] Permisos de `bootstrap/cache` configurados (775)
- [ ] Propietario correcto (www-data o el usuario del servidor web)

## 🚀 Deployment

### Primera Vez
- [ ] Ejecutar `composer install --no-dev --optimize-autoloader`
- [ ] Ejecutar `npm install` (si hay assets)
- [ ] Ejecutar `npm run build` (si hay assets)
- [ ] Ejecutar `php artisan migrate --force`
- [ ] Ejecutar `php artisan db:seed` (opcional, para datos iniciales)
- [ ] Ejecutar `php artisan storage:link`
- [ ] Ejecutar `php artisan config:cache`
- [ ] Ejecutar `php artisan route:cache`
- [ ] Ejecutar `php artisan view:cache`

### Actualización
- [ ] Ejecutar `./deploy.sh` o seguir los pasos manuales
- [ ] Verificar que no hay errores durante el deployment

## 🌐 Configuración del Servidor Web

### Nginx
- [ ] Archivo de configuración creado en `/etc/nginx/sites-available/`
- [ ] Enlace simbólico creado en `/etc/nginx/sites-enabled/`
- [ ] `server_name` configurado con tu dominio
- [ ] `root` apunta al directorio `public` del proyecto
- [ ] `fastcgi_pass` configurado correctamente (PHP-FPM)
- [ ] Configuración verificada (`sudo nginx -t`)
- [ ] Nginx recargado (`sudo systemctl reload nginx`)

### Apache
- [ ] Archivo de configuración creado en `/etc/apache2/sites-available/`
- [ ] Módulo `rewrite` habilitado (`sudo a2enmod rewrite`)
- [ ] Módulo `ssl` habilitado si usas HTTPS (`sudo a2enmod ssl`)
- [ ] `ServerName` configurado con tu dominio
- [ ] `DocumentRoot` apunta al directorio `public` del proyecto
- [ ] Configuración verificada (`sudo apache2ctl configtest`)
- [ ] Apache recargado (`sudo systemctl reload apache2`)

## 🔒 Seguridad

- [ ] `APP_DEBUG=false` en producción
- [ ] `APP_ENV=production` en producción
- [ ] Contraseñas de base de datos seguras
- [ ] Permisos de archivos correctos (644 para archivos, 755 para directorios)
- [ ] `.env` no accesible públicamente
- [ ] HTTPS configurado (recomendado)
- [ ] Firewall configurado (puertos 80, 443 abiertos)

## ✅ Post-Deployment

### Verificaciones
- [ ] Sitio web accesible en el navegador
- [ ] Login funciona correctamente
- [ ] Base de datos conectada y funcionando
- [ ] Archivos se pueden subir (storage funcionando)
- [ ] Rutas funcionan correctamente
- [ ] No hay errores 500 en los logs

### Logs
- [ ] Revisar `storage/logs/laravel.log` para errores
- [ ] Revisar logs del servidor web (Nginx/Apache)
- [ ] No hay errores críticos

### Performance
- [ ] Cachés configurados correctamente
- [ ] Optimizaciones de Laravel aplicadas
- [ ] Assets compilados y funcionando

## 🆘 Si Hay Problemas

1. **Revisar logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Verificar permisos:**
   ```bash
   sudo chmod -R 775 storage bootstrap/cache
   sudo chown -R www-data:www-data storage bootstrap/cache
   ```

3. **Limpiar cachés:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

4. **Verificar configuración:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   ```

5. **Verificar base de datos:**
   ```bash
   php artisan db:show
   php artisan migrate:status
   ```

## 📝 Notas

- Guarda este checklist y márcalo cada vez que hagas un deployment
- Documenta cualquier problema encontrado y su solución
- Mantén backups regulares de la base de datos
- Considera usar un sistema de CI/CD para automatizar deployments

---

**Última actualización:** $(date)

