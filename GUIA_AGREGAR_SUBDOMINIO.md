# 🌐 Guía para Agregar Nuevos Subdominios

Esta guía te permitirá agregar cualquier subdominio nuevo para proyectos Laravel en este servidor.

---

## 📋 Plantilla para Nuevo Subdominio

### **Ejemplo:** Agregar `demoproject.bsolutions.dev`

---

## PASO 1: Configurar DNS en Cloudflare

1. Ve a: https://dash.cloudflare.com
2. Selecciona dominio: **bsolutions.dev**
3. Click en: **DNS** (menú izquierdo)
4. Click en: **Add record**
5. Configurar:
   ```
   Type: A
   Name: demoproject
   IPv4 address: 62.171.174.191
   Proxy status: Proxied (naranja)
   TTL: Auto
   ```
6. Click: **Save**
7. Esperar: 1-2 minutos

---

## PASO 2: Configurar VirtualHost en Apache

### Editar archivo de VirtualHosts:

```powershell
# Abrir como administrador
notepad C:\xampp\apache\conf\extra\httpd-vhosts.conf
```

### Agregar al final (ANTES del VirtualHost de localhost):

```apache
# VirtualHost para demoproject.bsolutions.dev
<VirtualHost *:80>
    ServerName demoproject.bsolutions.dev
    ServerAlias www.demoproject.bsolutions.dev
    
    DocumentRoot "C:/xampp/php/www/[NOMBRE_CARPETA]/public"
    
    <Directory "C:/xampp/php/www/[NOMBRE_CARPETA]/public">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
        
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.*)$ index.php [QSA,L]
    </Directory>
    
    ErrorLog "logs/demoproject-error.log"
    CustomLog "logs/demoproject-access.log" common
</VirtualHost>
```

**⚠️ IMPORTANTE:** Reemplaza `[NOMBRE_CARPETA]` con el nombre real de tu proyecto.

### Guardar y cerrar

---

## PASO 3: Configurar archivo .env del proyecto

En la carpeta del proyecto Laravel:

```powershell
cd C:\xampp\php\www\[NOMBRE_CARPETA]
notepad .env
```

Actualizar:
```env
APP_URL=http://demoproject.bsolutions.dev
```

Guardar y cerrar.

---

## PASO 4: Limpiar Cachés de Laravel

```powershell
cd C:\xampp\php\www\[NOMBRE_CARPETA]
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

## PASO 5: Reiniciar Apache

```powershell
Stop-Process -Name "httpd" -Force
Start-Sleep -Seconds 2
C:\xampp\apache\bin\httpd.exe
```

O usar XAMPP Control Panel:
1. Stop Apache
2. Start Apache

---

## PASO 6: Configurar SSL en Cloudflare

1. Cloudflare → **SSL/TLS** → **Overview**
2. Encryption mode: **"Flexible"**
3. SSL/TLS → **Edge Certificates**
4. DESACTIVAR: ☐ Always Use HTTPS (para evitar errores HSTS)

---

## PASO 7: Probar el Subdominio

Abrir navegador:
```
http://demoproject.bsolutions.dev
```

Debería cargar tu aplicación Laravel.

---

## ✅ Checklist de Verificación

- [ ] DNS configurado en Cloudflare
- [ ] VirtualHost agregado en httpd-vhosts.conf
- [ ] Ruta DocumentRoot correcta (con /public al final)
- [ ] Archivo .env actualizado con APP_URL
- [ ] Cachés de Laravel limpiadas
- [ ] Apache reiniciado
- [ ] SSL en modo "Flexible" en Cloudflare
- [ ] Sitio accesible desde navegador

---

## 🔧 Plantilla Rápida (Copiar y Pegar)

### Para VirtualHost (reemplazar valores):

```apache
<VirtualHost *:80>
    ServerName [SUBDOMINIO].bsolutions.dev
    ServerAlias www.[SUBDOMINIO].bsolutions.dev
    
    DocumentRoot "C:/xampp/php/www/[CARPETA]/public"
    
    <Directory "C:/xampp/php/www/[CARPETA]/public">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
        
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.*)$ index.php [QSA,L]
    </Directory>
    
    ErrorLog "logs/[SUBDOMINIO]-error.log"
    CustomLog "logs/[SUBDOMINIO]-access.log" common
</VirtualHost>
```

**Reemplazar:**
- `[SUBDOMINIO]` → Nombre del subdominio (ej: demoproject)
- `[CARPETA]` → Nombre de la carpeta del proyecto (ej: mi-proyecto)

---

## 📝 Ejemplo Real - Agregar "democlientes.bsolutions.dev"

### 1. Cloudflare DNS:
```
Type: A
Name: democlientes
IPv4: 62.171.174.191
```

### 2. VirtualHost:
```apache
<VirtualHost *:80>
    ServerName democlientes.bsolutions.dev
    ServerAlias www.democlientes.bsolutions.dev
    DocumentRoot "C:/xampp/php/www/clientes-app/public"
    <Directory "C:/xampp/php/www/clientes-app/public">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.*)$ index.php [QSA,L]
    </Directory>
    ErrorLog "logs/democlientes-error.log"
    CustomLog "logs/democlientes-access.log" common
</VirtualHost>
```

### 3. .env del proyecto:
```env
APP_URL=http://democlientes.bsolutions.dev
```

### 4. Reiniciar Apache y limpiar cachés

### 5. Probar: http://democlientes.bsolutions.dev

---

## 🐛 Troubleshooting Común

### Error 403 Forbidden
- **Causa**: Ruta incorrecta en DocumentRoot
- **Solución**: Verificar que la ruta exista y termine en `/public`

### Error 404 Not Found
- **Causa**: mod_rewrite no activo o .htaccess faltante
- **Solución**: Verificar que `AllowOverride All` esté configurado

### Error: DNS no resuelve
- **Causa**: Configuración DNS no propagada
- **Solución**: Esperar 5-15 minutos, limpiar caché DNS: `ipconfig /flushdns`

### Error: ERR_CERT_AUTHORITY_INVALID
- **Causa**: SSL/TLS no en modo "Flexible"
- **Solución**: Cloudflare → SSL/TLS → Flexible

### Cambios no se aplican
- **Causa**: Cachés de Laravel activas
- **Solución**: Ejecutar comandos de limpieza de caché

---

## 💡 Tips Importantes

1. **Siempre agrega VirtualHosts ANTES del de localhost** (si existe)
2. **Verifica que la carpeta tenga `/public` al final** en DocumentRoot
3. **Reinicia Apache después de cada cambio** en httpd-vhosts.conf
4. **Limpia cachés de Laravel** después de cambiar .env
5. **Usa modo "Flexible" en Cloudflare** para proyectos sin SSL local
6. **Haz backup** de httpd-vhosts.conf antes de editar

---

## 🔍 Verificación de Configuración

### Comando para verificar VirtualHost:
```powershell
C:\xampp\apache\bin\httpd.exe -t
```

Debe mostrar: `Syntax OK`

### Comando para ver todos los VirtualHosts configurados:
```powershell
C:\xampp\apache\bin\httpd.exe -S
```

---

## 📊 Estructura de Archivos

```
C:\xampp\
├── apache\
│   ├── bin\
│   │   └── httpd.exe
│   ├── conf\
│   │   ├── httpd.conf
│   │   └── extra\
│   │       └── httpd-vhosts.conf ← EDITAR AQUÍ
│   └── logs\
│       ├── [subdominio]-error.log
│       └── [subdominio]-access.log
└── php\
    └── www\
        ├── breatech-landing\
        ├── martinez\
        └── [tu-nuevo-proyecto]\
            └── public\
                └── index.php
```

---

## ⏱️ Tiempo Total

- Configuración DNS: 2 minutos
- VirtualHost: 3 minutos
- Configurar .env: 1 minuto
- Limpiar cachés: 1 minuto
- Reiniciar Apache: 1 minuto
- **TOTAL: ~10 minutos**

---

## 🎓 Referencias

- Cloudflare DNS: https://dash.cloudflare.com
- Apache VirtualHost Docs: https://httpd.apache.org/docs/2.4/vhosts/
- Laravel Configuration: https://laravel.com/docs/configuration

---

Fecha: Noviembre 2025
Versión: 1.0


