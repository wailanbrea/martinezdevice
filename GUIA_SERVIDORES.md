# 🚀 Guía de Servidores - Estado Actual

## 📊 Resumen de Servidores Activos

| Servidor | Tecnología | Puerto | URL | Estado |
|----------|------------|--------|-----|--------|
| **Breatech-Landing** | Next.js (Node) | 3000 | http://localhost:3000 | ✅ Activo |
| **MartinezService** | Laravel (Apache) | 80 | http://demomartinez.bsolutions.dev | ✅ Activo |
| **TicomSys Modernizado** | Laravel (Apache) | 80 | http://demoticomsys.bsolutions.dev | ⚠️ Reiniciar Apache |

---

## 🌐 SERVIDOR 1: Breatech-Landing

### **Información General**
- **Ubicación**: `C:\xampp\php\www\breatech-landing`
- **Tecnología**: Next.js 14 + Node.js
- **Puerto**: 3000
- **URL Local**: http://localhost:3000
- **URL Externa**: http://62.171.174.191:3000

### **Cómo Iniciar**

#### Opción 1: Script automático
```powershell
cd C:\xampp\php\www\breatech-landing
.\start-with-images.bat
```

#### Opción 2: Node directo
```powershell
cd C:\xampp\php\www\breatech-landing
node server-with-images.js
```

#### Opción 3: Modo desarrollo
```powershell
cd C:\xampp\php\www\breatech-landing
npm run dev
```

### **Cómo Detener**
```powershell
taskkill /F /IM node.exe
```

### **Verificar Estado**
```powershell
# Ver proceso
Get-Process -Name "node" -ErrorAction SilentlyContinue

# Ver puerto
netstat -ano | findstr ":3000"

# Probar acceso
curl http://localhost:3000 -UseBasicParsing
```

### **Archivos Importantes**
- `server-with-images.js` - Servidor principal
- `start-with-images.bat` - Script de inicio
- `package.json` - Dependencias
- `next.config.js` - Configuración Next.js

---

## 💼 SERVIDOR 2: MartinezService

### **Información General**
- **Ubicación**: `C:\xampp\php\www\martinez`
- **Tecnología**: Laravel 12 + PHP 8.2 + Apache
- **Puerto**: 80 (HTTP) y 443 (HTTPS)
- **Dominio**: demomartinez.bsolutions.dev
- **Base de Datos**: SQLite (`database/database.sqlite`)

### **Configuración DNS (Cloudflare)**
```
Tipo: A
Host: demomartinez
Valor: 62.171.174.191
Proxy: Activado (naranja)
SSL Mode: Flexible
```

### **Configuración Apache VirtualHost**
**Archivo**: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

```apache
<VirtualHost *:80>
    ServerName demomartinez.bsolutions.dev
    ServerAlias www.demomartinez.bsolutions.dev
    
    DocumentRoot "C:/xampp/php/www/martinez/public"
    
    <Directory "C:/xampp/php/www/martinez/public">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
        
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.*)$ index.php [QSA,L]
    </Directory>
    
    ErrorLog "logs/demomartinez-error.log"
    CustomLog "logs/demomartinez-access.log" common
</VirtualHost>
```

### **Archivo .env**
```env
APP_URL=http://demomartinez.bsolutions.dev
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite
```

### **Cómo Iniciar**

#### Apache ya está corriendo (siempre activo):
```powershell
# Verificar estado
Get-Process -Name "httpd"

# Si no está corriendo, iniciar:
C:\xampp\apache\bin\httpd.exe

# O usar XAMPP Control Panel
C:\xampp\xampp-control.exe
```

#### Si haces cambios, reiniciar Apache:
```powershell
Stop-Process -Name "httpd" -Force
Start-Sleep -Seconds 2
C:\xampp\apache\bin\httpd.exe
```

#### Limpiar cachés de Laravel:
```powershell
cd C:\xampp\php\www\martinez
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### **Cómo Detener**
```powershell
Stop-Process -Name "httpd" -Force
```

### **Verificar Estado**
```powershell
# Ver procesos Apache
Get-Process -Name "httpd"

# Ver puertos
netstat -ano | findstr ":80 "
netstat -ano | findstr ":443 "

# Probar acceso
curl http://demomartinez.bsolutions.dev -UseBasicParsing

# Ver logs
Get-Content C:\xampp\apache\logs\demomartinez-error.log -Tail 20
```

### **Credenciales de Acceso**
```
URL: http://demomartinez.bsolutions.dev
Email: admin@argon.com
Password: secret
```

### **Usuarios de Prueba**
- Admin: admin@argon.com / secret
- Técnico 1: carlos.rivas@martinezservice.com / password
- Técnico 2: sofia.lopez@martinezservice.com / password
- Recepción: laura.gomez@martinezservice.com / password

### **Archivos Importantes**
- `.env` - Configuración de entorno
- `routes/web.php` - Rutas web
- `routes/api.php` - Rutas API
- `database/database.sqlite` - Base de datos
- `public/.htaccess` - Reglas de rewrite

---

## 🛠️ SERVIDOR 3: TicomSys Modernizado

### **Información General**
- **Ubicación**: `C:\xampp\php\www\demoticomsys`
- **Tecnología**: Laravel 12 + React estático
- **Puerto**: 80 (vía Apache)
- **Dominio**: demoticomsys.bsolutions.dev
- **Base de Datos**: MySQL (`demoticomsys`, usuario `root` sin contraseña por defecto)

### **Configuración Apache VirtualHost**
_Archivo_: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

```apache
<VirtualHost *:80>
    ServerName demoticomsys.bsolutions.dev
    ServerAlias demoticomsys
    ServerAlias www.demoticomsys.bsolutions.dev

    DocumentRoot "C:/xampp/php/www/demoticomsys/public"

    <Directory "C:/xampp/php/www/demoticomsys/public">
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted

        RewriteEngine On
        RewriteBase /
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.*)$ index.php [QSA,L]
    </Directory>

    ErrorLog "logs/demoticomsys-error.log"
    CustomLog "logs/demoticomsys-access.log" common
    LogLevel warn
</VirtualHost>
```

> 🔄 **Recuerda**: reinicia Apache desde XAMPP Control Panel para que los cambios tomen efecto.

### **Archivo .env**
```env
APP_NAME="Ticomsys-Modernizado"
APP_ENV=local
APP_URL=http://demoticomsys.bsolutions.dev
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=demoticomsys
DB_USERNAME=root
DB_PASSWORD=
```

### **Instalación**
```powershell
# 1. Crear la base de datos MySQL (usa tu contraseña si la tienes configurada)
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS demoticomsys CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Instalar dependencias y preparar la app
cd C:\xampp\php\www\demoticomsys
composer install --no-scripts
php artisan migrate:fresh --seed --force
php artisan storage:link
```

### **Cómo probar**
```powershell
# Reiniciar Apache (si tienes permisos)
taskkill /F /IM httpd.exe
Start-Sleep -Seconds 2
C:\xampp\apache\bin\httpd.exe

# Verificar respuesta
Invoke-WebRequest -Uri http://demoticomsys.bsolutions.dev -UseBasicParsing
```

### **Pendientes**
- Reiniciar Apache manualmente (permisos requeridos).
- Ejecutar `npm install` y `npm run build` en `frontend/` si se necesitan actualizaciones del React dashboard.

---

## 🔧 Comandos Útiles Generales

### **Ver todos los servidores activos:**
```powershell
# Node.js (Breatech)
Get-Process -Name "node" -ErrorAction SilentlyContinue

# Apache (Martinez)
Get-Process -Name "httpd" -ErrorAction SilentlyContinue

# Todos los puertos en uso
netstat -ano | findstr "LISTENING"
```

### **Detener todo:**
```powershell
# Detener Node.js
taskkill /F /IM node.exe

# Detener Apache
Stop-Process -Name "httpd" -Force
```

### **Iniciar todo:**
```powershell
# Iniciar Breatech
cd C:\xampp\php\www\breatech-landing
Start-Process node -ArgumentList "server-with-images.js" -WindowStyle Hidden

# Iniciar Apache (Martinez)
C:\xampp\apache\bin\httpd.exe
```

---

## 🐛 Troubleshooting

### **Breatech-Landing no carga**
```powershell
# Ver si Node está corriendo
Get-Process -Name "node"

# Ver logs en consola
cd C:\xampp\php\www\breatech-landing
node server-with-images.js
# (Los errores aparecerán en consola)
```

### **MartinezService da 403 Forbidden**
```powershell
# Verificar ruta en VirtualHost
Get-Content C:\xampp\apache\conf\extra\httpd-vhosts.conf | Select-String "DocumentRoot.*martinez"

# Debe mostrar: DocumentRoot "C:/xampp/php/www/martinez/public"
# Si muestra "martinezdevice", editar el archivo y corregir
```

### **MartinezService da 500 Internal Server Error**
```powershell
# Ver logs
Get-Content C:\xampp\apache\logs\demomartinez-error.log -Tail 20

# Ver logs de Laravel
Get-Content C:\xampp\php\www\martinez\storage\logs\laravel.log -Tail 20
```

### **Puerto 80 ocupado**
```powershell
# Ver qué está usando el puerto
netstat -ano | findstr ":80 "

# Detener Apache
Stop-Process -Name "httpd" -Force
```

---

## 📝 Checklist de Inicio Rápido

### **Al iniciar el día:**
- [ ] Verificar Apache: `Get-Process -Name "httpd"`
- [ ] Si no está, iniciar: `C:\xampp\apache\bin\httpd.exe`
- [ ] Verificar Breatech: `Get-Process -Name "node"`
- [ ] Si no está, iniciar: `cd C:\xampp\php\www\breatech-landing; node server-with-images.js &`
- [ ] Probar accesos:
  - [ ] http://localhost:3000 (Breatech)
  - [ ] http://demomartinez.bsolutions.dev (Martinez)

---

## 🎯 URLs de Acceso Rápido

### **Desarrollo Local:**
- Breatech: http://localhost:3000
- Martinez: http://localhost/martinez/public
- XAMPP Dashboard: http://localhost

### **Producción (Subdominios):**
- Breatech: http://bsolutions.dev (si está configurado)
- Martinez: http://demomartinez.bsolutions.dev

### **Por IP (desde otros dispositivos):**
- Breatech: http://62.171.174.191:3000
- Server: http://62.171.174.191

---

## 💡 Notas Importantes

1. **Apache siempre debe estar corriendo** para que MartinezService funcione
2. **Node.js se debe ejecutar manualmente** cada vez que reinicias el servidor
3. **Los puertos no pueden duplicarse** (80, 443, 3000)
4. **Cloudflare debe estar en modo "Flexible"** para MartinezService
5. **La ruta DEBE ser "martinez"** no "martinezdevice" en VirtualHost

---

## 📞 Comandos de Emergencia

```powershell
# Reiniciar todo
taskkill /F /IM node.exe
Stop-Process -Name "httpd" -Force
Start-Sleep -Seconds 3
C:\xampp\apache\bin\httpd.exe
cd C:\xampp\php\www\breatech-landing; Start-Process node -ArgumentList "server-with-images.js" -WindowStyle Hidden

# Ver estado de todo
Get-Process -Name "httpd","node" -ErrorAction SilentlyContinue | Format-Table
netstat -ano | findstr "LISTENING" | findstr ":80 :443 :3000"
```

---

Fecha: Noviembre 2025
Versión: 1.0


