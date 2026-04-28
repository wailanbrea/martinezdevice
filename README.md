# 🚀 MartinezService - Sistema de Gestión de Reparaciones

Sistema completo de gestión de reparaciones de equipos electrónicos desarrollado en Laravel 12.

---

## 📚 Documentación

### 📖 **Guías Disponibles**

1. **[GUIA_SERVIDORES.md](GUIA_SERVIDORES.md)** 
   - Estado actual de los servidores
   - Cómo iniciar/detener Breatech-Landing y MartinezService
   - Comandos de verificación y troubleshooting
   - Credenciales de acceso

2. **[GUIA_AGREGAR_SUBDOMINIO.md](GUIA_AGREGAR_SUBDOMINIO.md)**
   - Cómo agregar nuevos subdominios para proyectos Laravel
   - Plantillas y ejemplos
   - Configuración DNS en Cloudflare
   - Configuración VirtualHost en Apache

---

## 🌐 Acceso Rápido

### **MartinezService**
- **URL**: http://demomartinez.bsolutions.dev
- **Admin**: martinez@martinezservice.com
- **Password**: martinez123

### **Breatech-Landing**
- **URL**: http://localhost:3000
- **URL Externa**: http://62.171.174.191:3000

---

## ⚡ Comandos Rápidos

### Iniciar MartinezService (Apache):
```powershell
C:\xampp\apache\bin\httpd.exe
```

### Iniciar Breatech-Landing (Node.js):
```powershell
cd C:\xampp\php\www\breatech-landing
node server-with-images.js
```

### Verificar estado de ambos:
```powershell
Get-Process -Name "httpd","node" -ErrorAction SilentlyContinue
```

---

## 🛠️ Tecnologías

- **Laravel 12** - Framework PHP
- **PHP 8.2.12** - Lenguaje backend
- **Bootstrap 5** + **Argon Dashboard 2** - Frontend
- **SQLite** - Base de datos
- **Apache 2.4** - Servidor web
- **Responsive Design** - Mobile-first

---

## 📦 Características

- ✅ CRUD completo de clientes, equipos y reparaciones
- ✅ Sistema de roles (Admin, Técnico, Recepción, Contabilidad)
- ✅ Gestión de estados de reparación
- ✅ Historial y notas por reparación
- ✅ Módulo de contabilidad
- ✅ Diseño 100% responsivo (móvil y desktop)
- ✅ API RESTful con Laravel Sanctum
- ✅ Dashboard con estadísticas

---

## ⚙️ Instalación

```powershell
cd C:\xampp\php\www\martinez
composer run setup
php artisan serve
```

El comando `composer run setup` crea `database/database.sqlite` si no existe, ejecuta todas las migraciones, siembra los datos demo y crea el enlace de `storage`.

---

## 📂 Estructura

```
martinez/
├── app/
│   ├── Http/Controllers/
│   └── Models/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── database.sqlite
├── resources/views/
│   ├── reparaciones/
│   ├── clientes/
│   ├── equipos/
│   └── contabilidad/
├── public/
│   └── assets/
├── routes/
│   ├── web.php
│   └── api.php
├── GUIA_SERVIDORES.md ⭐
└── GUIA_AGREGAR_SUBDOMINIO.md ⭐
```

---

## 🔐 Usuarios Demo

| Email | Password | Rol |
|-------|----------|-----|
| martinez@martinezservice.com | martinez123 | Administrador + Técnico |
| carlos@martinezservice.com | carlos123 | Técnico |
| ismael@martinezservice.com | ismael123 | Técnico |

---

## 📝 Licencia

Proyecto desarrollado para demostración.

Noviembre 2025 - Versión 1.0
