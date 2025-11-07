#!/bin/bash

# Script para Solucionar Error 520 después de Login
# Ejecutar en el VPS: sudo bash solucionar-error-520-login.sh

set -e

echo "=== SOLUCIONANDO ERROR 520 DESPUÉS DE LOGIN ==="
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# 1. Verificar que estamos en el directorio correcto
cd /var/www/martinezdevice

# 2. Crear directorios de sesiones si no existen
echo -e "${YELLOW}1. Verificando directorios de sesiones...${NC}"
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/logs
echo -e "${GREEN}✓ Directorios creados/verificados${NC}"

# 3. Limpiar sesiones antiguas
echo -e "\n${YELLOW}2. Limpiando sesiones antiguas...${NC}"
rm -rf storage/framework/sessions/*
echo -e "${GREEN}✓ Sesiones limpiadas${NC}"

# 4. Configurar permisos correctos
echo -e "\n${YELLOW}3. Configurando permisos...${NC}"
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
echo -e "${GREEN}✓ Permisos configurados${NC}"

# 5. Limpiar y regenerar cachés de Laravel
echo -e "\n${YELLOW}4. Limpiando cachés de Laravel...${NC}"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN}✓ Cachés regenerados${NC}"

# 6. Configurar timeout de PHP-FPM
echo -e "\n${YELLOW}5. Configurando timeout de PHP-FPM...${NC}"
PHP_FPM_CONF="/etc/php/8.3/fpm/pool.d/www.conf"

if [ -f "$PHP_FPM_CONF" ]; then
    # Backup del archivo
    cp "$PHP_FPM_CONF" "$PHP_FPM_CONF.backup.$(date +%Y%m%d_%H%M%S)"
    
    # Agregar o modificar request_terminate_timeout
    if grep -q "^request_terminate_timeout" "$PHP_FPM_CONF"; then
        sed -i 's/^request_terminate_timeout.*/request_terminate_timeout = 300/' "$PHP_FPM_CONF"
    else
        echo "request_terminate_timeout = 300" >> "$PHP_FPM_CONF"
    fi
    
    # Aumentar límites de procesos
    sed -i 's/^pm.max_children.*/pm.max_children = 50/' "$PHP_FPM_CONF" || echo "pm.max_children = 50" >> "$PHP_FPM_CONF"
    
    echo -e "${GREEN}✓ Timeout de PHP-FPM configurado${NC}"
else
    echo -e "${YELLOW}⚠ No se encontró el archivo de configuración de PHP-FPM${NC}"
fi

# 7. Configurar timeout de Nginx
echo -e "\n${YELLOW}6. Configurando timeout de Nginx...${NC}"
NGINX_CONF="/etc/nginx/sites-available/demomartinez.bsolutions.dev"

if [ -f "$NGINX_CONF" ]; then
    # Backup del archivo
    cp "$NGINX_CONF" "$NGINX_CONF.backup.$(date +%Y%m%d_%H%M%S)"
    
    # Agregar fastcgi_read_timeout si no existe
    if ! grep -q "fastcgi_read_timeout" "$NGINX_CONF"; then
        # Buscar la sección location ~ \.php$ y agregar el timeout
        sed -i '/location ~ \\\.php\$ {/a\        fastcgi_read_timeout 300;' "$NGINX_CONF"
    fi
    
    # Agregar client_max_body_size si no existe
    if ! grep -q "client_max_body_size" "$NGINX_CONF"; then
        sed -i '/server {/a\    client_max_body_size 20M;' "$NGINX_CONF"
    fi
    
    echo -e "${GREEN}✓ Timeout de Nginx configurado${NC}"
else
    echo -e "${YELLOW}⚠ No se encontró el archivo de configuración de Nginx${NC}"
fi

# 8. Verificar configuración de Nginx
echo -e "\n${YELLOW}7. Verificando configuración de Nginx...${NC}"
if nginx -t 2>&1 | grep -q "successful"; then
    echo -e "${GREEN}✓ Configuración de Nginx correcta${NC}"
else
    echo -e "${RED}✗ Error en la configuración de Nginx${NC}"
    nginx -t
    exit 1
fi

# 9. Reiniciar servicios
echo -e "\n${YELLOW}8. Reiniciando servicios...${NC}"
systemctl restart php8.3-fpm
systemctl reload nginx
echo -e "${GREEN}✓ Servicios reiniciados${NC}"

# 10. Verificar que los servicios están corriendo
echo -e "\n${YELLOW}9. Verificando servicios...${NC}"
if systemctl is-active --quiet php8.3-fpm; then
    echo -e "${GREEN}✓ PHP-FPM corriendo${NC}"
else
    echo -e "${RED}✗ PHP-FPM no está corriendo${NC}"
fi

if systemctl is-active --quiet nginx; then
    echo -e "${GREEN}✓ Nginx corriendo${NC}"
else
    echo -e "${RED}✗ Nginx no está corriendo${NC}"
fi

# 11. Probar conexión
echo -e "\n${YELLOW}10. Probando conexión al servidor...${NC}"
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" -H "Host: demomartinez.bsolutions.dev" http://localhost)
echo "Código de respuesta: $RESPONSE"

if [ "$RESPONSE" = "200" ] || [ "$RESPONSE" = "302" ]; then
    echo -e "${GREEN}✓ Servidor responde correctamente${NC}"
else
    echo -e "${RED}✗ Servidor no responde correctamente${NC}"
fi

# Resumen
echo -e "\n${GREEN}=== CORRECCIONES APLICADAS ===${NC}"
echo ""
echo "✓ Directorios de sesiones creados/verificados"
echo "✓ Sesiones antiguas limpiadas"
echo "✓ Permisos configurados (www-data:www-data 775)"
echo "✓ Cachés de Laravel regenerados"
echo "✓ Timeout de PHP-FPM aumentado a 300 segundos"
echo "✓ Timeout de Nginx configurado"
echo "✓ Servicios reiniciados"
echo ""
echo -e "${YELLOW}Prueba nuevamente el login en:${NC}"
echo "https://demomartinez.bsolutions.dev/login"
echo ""
echo -e "${YELLOW}Si el problema persiste, revisa los logs:${NC}"
echo "  sudo tail -f /var/log/nginx/demomartinez-error.log"
echo "  sudo tail -f /var/www/martinezdevice/storage/logs/laravel.log"
echo ""

