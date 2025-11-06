# Martínez Service - Sistema de Gestión y Reparación de Equipos

Sistema completo de gestión interna para taller de reparación de equipos electrónicos, desarrollado con Laravel 12.

## 🚀 Características

- ✅ **Gestión de Clientes**: Registro y administración de clientes
- ✅ **Entradas de Equipos**: Registro de equipos con foto y código QR
- ✅ **Historial de Reparaciones**: Seguimiento detallado de reparaciones con piezas y costos
- ✅ **Facturación**: Generación de facturas detalladas en PDF
- ✅ **Contabilidad**: Reportes financieros y estadísticas
- ✅ **Usuarios y Roles**: Sistema de permisos (Administrador, Técnico, Recepción, Contabilidad)
- ✅ **Tutorial Guiado**: Sistema de ayuda interactiva
- ✅ **Modo Oscuro**: Interfaz adaptable con modo claro/oscuro
- ✅ **Diseño Responsive**: Optimizado para tablets y dispositivos móviles

## 📋 Requisitos

- PHP >= 8.3
- MySQL >= 8.0
- Composer
- Node.js y NPM (para assets)
- Extensión PHP: GD, PDO, MySQL, OpenSSL, Mbstring, Tokenizer, XML, Ctype, JSON

## 📦 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/wailanbrea/martinezdevice.git
cd martinezdevice
```

### 2. Instalar dependencias

```bash
composer install
npm install
```

### 3. Configurar el entorno

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar la base de datos

Edita el archivo `.env` y configura tu base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=martinez_device
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 5. Crear la base de datos

```sql
CREATE DATABASE martinez_device CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Ejecutar migraciones y seeders

```bash
php artisan migrate:fresh --seed
```

Esto creará:
- Las tablas de la base de datos
- Roles del sistema (Administrador, Técnico, Recepción, Contabilidad)
- Usuario administrador por defecto
- Datos de prueba

### 7. Crear enlace simbólico para storage

```bash
php artisan storage:link
```

### 8. Compilar assets (opcional para desarrollo)

```bash
npm run build
```

### 9. Iniciar el servidor

```bash
php artisan serve
```

El sistema estará disponible en: `http://localhost:8000`

## 🔑 Credenciales por Defecto

**Usuario Administrador:**
- Email: `admin@martinezservice.com`
- Contraseña: `password`

> ⚠️ **IMPORTANTE**: Cambia la contraseña después del primer inicio de sesión.

## 🚀 Deployment en Servidor

### Pasos para actualizar en producción

1. **Conectar al servidor y navegar al directorio del proyecto**

```bash
cd /ruta/al/proyecto
```

2. **Activar modo mantenimiento**

```bash
php artisan down
```

3. **Obtener los últimos cambios**

```bash
git pull origin main
```

4. **Instalar/actualizar dependencias**

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

5. **Ejecutar migraciones**

```bash
php artisan migrate --force
```

6. **Limpiar cachés**

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

7. **Optimizar para producción**

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

8. **Desactivar modo mantenimiento**

```bash
php artisan up
```

### Script de Deployment Automático

Puedes crear un script `deploy.sh` para automatizar el proceso:

```bash
#!/bin/bash

echo "🚀 Iniciando deployment..."

# Activar mantenimiento
php artisan down

# Obtener cambios
git pull origin main

# Instalar dependencias
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Ejecutar migraciones
php artisan migrate --force

# Limpiar y optimizar
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize

# Cachear para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Desactivar mantenimiento
php artisan up

echo "✅ Deployment completado!"
```

Hacer ejecutable: `chmod +x deploy.sh`

## 📁 Estructura del Proyecto

```
martinezdevice/
├── app/
│   ├── Console/Commands/     # Comandos Artisan personalizados
│   ├── Http/
│   │   ├── Controllers/Web/  # Controladores web
│   │   └── Middleware/       # Middleware personalizado
│   └── Models/               # Modelos Eloquent
├── database/
│   ├── migrations/           # Migraciones de base de datos
│   └── seeders/             # Seeders para datos iniciales
├── public/
│   ├── css/                 # Estilos CSS
│   ├── js/                  # JavaScript
│   └── storage/             # Archivos subidos (enlazado)
├── resources/
│   └── views/               # Vistas Blade
└── routes/
    ├── web.php              # Rutas web
    └── api.php              # Rutas API
```

## 🔧 Comandos Útiles

### Crear un usuario administrador

```bash
php artisan user:assign-admin admin@example.com
```

### Reiniciar base de datos con datos de prueba

```bash
php artisan migrate:fresh --seed
```

### Limpiar todos los cachés

```bash
php artisan optimize:clear
```

### Generar código QR para equipos existentes

```bash
php artisan tinker
>>> App\Models\Equipo::all()->each->generarQR();
```

## 📝 Notas de Desarrollo

- El sistema usa **Laravel 12** con PHP 8.3+
- Base de datos: **MySQL 8+**
- Autenticación: **Laravel Sanctum**
- Generación de PDF: **DomPDF**
- Generación de QR: **SimpleSoftwareIO/QRCode**
- Frontend: **Blade Templates** con diseño Argon 2

## 🛡️ Seguridad

- Las contraseñas se hashean con bcrypt
- Protección CSRF en todos los formularios
- Validación de datos en servidor
- Middleware de autenticación y roles
- Variables sensibles en `.env` (no incluir en git)

## 📄 Licencia

Este proyecto es privado y de uso exclusivo para Martínez Service.

## 👨‍💻 Desarrollo

Desarrollado con Laravel Framework.

---

**Martínez Service** - Sistema de Gestión y Reparación de Equipos

