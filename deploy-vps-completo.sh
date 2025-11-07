#!/bin/bash

# Script de Deployment Completo para VPS
# Martínez Service - Laravel
# Uso: sudo bash deploy-vps-completo.sh

set -e  # Salir si hay algún error

echo "🚀 ========================================="
echo "🚀 DEPLOYMENT COMPLETO - MARTÍNEZ SERVICE"
echo "🚀 ========================================="
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Variables
PROJECT_DIR="/var/www/martinezdevice"
REPO_URL="https://github.com/wailanbrea/martinezdevice.git"
DOMAIN="demomartinez.bsolutions.dev"
DB_NAME="martinez_device"
DB_USER="root"
NGINX_CONFIG="/etc/nginx/sites-available/${DOMAIN}"

# Verificar que se ejecuta como root
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}❌ Este script debe ejecutarse como root (usa sudo)${NC}"
    exit 1
fi

# ============================================
# 1. ACTUALIZAR SISTEMA
# ============================================
echo -e "${YELLOW}📦 Actualizando sistema...${NC}"
apt update -qq
apt upgrade -y -qq

# ============================================
# 2. INSTALAR DEPENDENCIAS
# ============================================
echo -e "${YELLOW}📦 Instalando dependencias del sistema...${NC}"

# Verificar PHP
if ! command -v php &> /dev/null; then
    echo "Instalando PHP 8.3..."
    apt install -y software-properties-common
    add-apt-repository -y ppa:ondrej/php
    apt update -qq
    apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-zip php8.3-gd \
                   php8.3-mbstring php8.3-curl php8.3-xml php8.3-bcmath \
                   php8.3-intl php8.3-readline
else
    PHP_VERSION=$(php -r 'echo PHP_VERSION;')
    echo -e "${GREEN}✓ PHP ya instalado: $PHP_VERSION${NC}"
fi

# Verificar Composer
if ! command -v composer &> /dev/null; then
    echo "Instalando Composer..."
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer
else
    echo -e "${GREEN}✓ Composer ya instalado${NC}"
fi

# Verificar Nginx
if ! command -v nginx &> /dev/null; then
    echo "Instalando Nginx..."
    apt install -y nginx
else
    echo -e "${GREEN}✓ Nginx ya instalado${NC}"
fi

# Verificar MySQL
if ! command -v mysql &> /dev/null; then
    echo "Instalando MySQL..."
    apt install -y mysql-server
else
    echo -e "${GREEN}✓ MySQL ya instalado${NC}"
fi

# ============================================
# 3. CLONAR/ACTUALIZAR PROYECTO
# ============================================
echo -e "${YELLOW}📥 Clonando/Actualizando proyecto...${NC}"

if [ -d "$PROJECT_DIR" ]; then
    echo "El directorio ya existe, actualizando..."
    cd "$PROJECT_DIR"
    git fetch --all
    git reset --hard origin/main
    git pull origin main
else
    echo "Clonando repositorio..."
    mkdir -p /var/www
    cd /var/www
    git clone "$REPO_URL" martinezdevice
    cd "$PROJECT_DIR"
fi

# ============================================
# 4. INSTALAR DEPENDENCIAS DE COMPOSER
# ============================================
echo -e "${YELLOW}📚 Instalando dependencias de Composer...${NC}"
cd "$PROJECT_DIR"
composer install --no-dev --optimize-autoloader --no-interaction

# ============================================
# 5. CONFIGURAR .env
# ============================================
echo -e "${YELLOW}⚙️  Configurando .env...${NC}"

if [ ! -f .env ]; then
    echo "Creando .env desde .env.example..."
    cp .env.example .env
    
    # Generar APP_KEY
    php artisan key:generate --force
    
    echo -e "${YELLOW}⚠️  IMPORTANTE: Edita el archivo .env con las credenciales correctas:${NC}"
    echo "   nano $PROJECT_DIR/.env"
    echo ""
    echo "Variables importantes a configurar:"
    echo "  - APP_ENV=production"
    echo "  - APP_DEBUG=false"
    echo "  - APP_URL=https://${DOMAIN}"
    echo "  - DB_DATABASE=${DB_NAME}"
    echo "  - DB_USERNAME=${DB_USER}"
    echo "  - DB_PASSWORD=(tu contraseña)"
    echo ""
    read -p "Presiona Enter después de configurar el .env..."
else
    echo -e "${GREEN}✓ .env ya existe${NC}"
fi

# ============================================
# 6. CONFIGURAR BASE DE DATOS
# ============================================
echo -e "${YELLOW}🗄️  Configurando base de datos...${NC}"

# Crear base de datos si no existe
mysql -u root <<EOF 2>/dev/null || true
CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EOF

echo -e "${GREEN}✓ Base de datos verificada${NC}"

# Ejecutar migraciones
echo "Ejecutando migraciones..."
php artisan migrate --force

# ============================================
# 7. CREAR ENLACE SIMBÓLICO DE STORAGE
# ============================================
echo -e "${YELLOW}🔗 Creando enlace simbólico de storage...${NC}"
php artisan storage:link || true

# ============================================
# 8. CONFIGURAR PERMISOS
# ============================================
echo -e "${YELLOW}🔐 Configurando permisos...${NC}"
chown -R www-data:www-data "$PROJECT_DIR"
chmod -R 775 storage bootstrap/cache
find "$PROJECT_DIR" -type f -exec chmod 644 {} \;
find "$PROJECT_DIR" -type d -exec chmod 755 {} \;

# ============================================
# 9. CONFIGURAR NGINX
# ============================================
echo -e "${YELLOW}🌐 Configurando Nginx...${NC}"

# Crear configuración de Nginx
cat > "$NGINX_CONFIG" <<EOF
server {
    listen 80;
    server_name ${DOMAIN};
    return 301 https://\$server_name\$request_uri;
}

server {
    listen 443 ssl http2;
    server_name ${DOMAIN};

    # SSL Certificate (ajustar según tu configuración)
    ssl_certificate /etc/letsencrypt/live/bsolutions.dev/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/bsolutions.dev/privkey.pem;

    # SSL Configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    # Logs
    access_log /var/log/nginx/${DOMAIN}-access.log;
    error_log /var/log/nginx/${DOMAIN}-error.log;

    # Root directory
    root ${PROJECT_DIR}/public;
    index index.php index.html index.htm;

    # Tamaño máximo de upload
    client_max_body_size 20M;

    # Laravel routing
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # PHP-FPM configuration
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 300;
    }

    # Archivos estáticos
    location ~* \.(jpg|jpeg|gif|png|css|js|ico|xml|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Denegar acceso a archivos ocultos
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    server_tokens off;
}
EOF

# Habilitar sitio
if [ ! -L "/etc/nginx/sites-enabled/${DOMAIN}" ]; then
    ln -s "$NGINX_CONFIG" "/etc/nginx/sites-enabled/${DOMAIN}"
fi

# Verificar configuración
echo "Verificando configuración de Nginx..."
nginx -t

# ============================================
# 10. OPTIMIZAR LARAVEL
# ============================================
echo -e "${YELLOW}⚡ Optimizando Laravel...${NC}"
cd "$PROJECT_DIR"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# ============================================
# 11. INICIAR/RECARGAR SERVICIOS
# ============================================
echo -e "${YELLOW}🔄 Iniciando servicios...${NC}"

# PHP-FPM
systemctl start php8.3-fpm
systemctl enable php8.3-fpm

# Nginx
systemctl reload nginx
systemctl enable nginx

# ============================================
# 12. CONFIGURAR FIREWALL
# ============================================
echo -e "${YELLOW}🔥 Configurando firewall...${NC}"
if command -v ufw &> /dev/null; then
    ufw allow 80/tcp
    ufw allow 443/tcp
    echo -e "${GREEN}✓ Firewall configurado${NC}"
fi

# ============================================
# 13. VERIFICACIÓN FINAL
# ============================================
echo ""
echo -e "${GREEN}✅ ========================================="
echo -e "✅ DEPLOYMENT COMPLETADO"
echo -e "✅ =========================================${NC}"
echo ""

# Verificar servicios
echo "Verificando servicios..."
systemctl is-active --quiet php8.3-fpm && echo -e "${GREEN}✓ PHP-FPM está corriendo${NC}" || echo -e "${RED}✗ PHP-FPM NO está corriendo${NC}"
systemctl is-active --quiet nginx && echo -e "${GREEN}✓ Nginx está corriendo${NC}" || echo -e "${RED}✗ Nginx NO está corriendo${NC}"

# Probar respuesta
echo ""
echo "Probando respuesta del servidor..."
if curl -s -o /dev/null -w "%{http_code}" -H "Host: ${DOMAIN}" http://localhost | grep -q "200\|301\|302"; then
    echo -e "${GREEN}✓ Servidor responde correctamente${NC}"
else
    echo -e "${YELLOW}⚠️  El servidor puede no estar respondiendo correctamente${NC}"
fi

echo ""
echo -e "${GREEN}🎉 ¡Deployment completado exitosamente!${NC}"
echo ""
echo "Próximos pasos:"
echo "1. Verifica que el dominio ${DOMAIN} apunta a este servidor"
echo "2. Configura SSL con Let's Encrypt si es necesario:"
echo "   sudo certbot --nginx -d ${DOMAIN}"
echo "3. Verifica los logs si hay problemas:"
echo "   tail -f /var/log/nginx/${DOMAIN}-error.log"
echo "   tail -f ${PROJECT_DIR}/storage/logs/laravel.log"
echo ""
echo "URL: https://${DOMAIN}"

