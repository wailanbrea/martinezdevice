#!/bin/bash

# Script de Diagnóstico para Error 520 después de Login
# Ejecutar en el VPS: sudo bash diagnosticar-vps-520.sh

echo "=== DIAGNÓSTICO ERROR 520 DESPUÉS DE LOGIN ==="
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# 1. Verificar PHP-FPM
echo -e "${YELLOW}1. Verificando PHP-FPM...${NC}"
if systemctl is-active --quiet php8.3-fpm; then
    echo -e "${GREEN}✓ PHP-FPM está corriendo${NC}"
    
    # Ver procesos PHP-FPM
    ps aux | grep php-fpm | grep -v grep | head -n 3
else
    echo -e "${RED}✗ PHP-FPM NO está corriendo${NC}"
    echo "Iniciando PHP-FPM..."
    systemctl start php8.3-fpm
fi

# 2. Verificar Nginx
echo -e "\n${YELLOW}2. Verificando Nginx...${NC}"
if systemctl is-active --quiet nginx; then
    echo -e "${GREEN}✓ Nginx está corriendo${NC}"
else
    echo -e "${RED}✗ Nginx NO está corriendo${NC}"
    echo "Iniciando Nginx..."
    systemctl start nginx
fi

# 3. Verificar logs de Nginx (últimos errores)
echo -e "\n${YELLOW}3. Últimos errores de Nginx:${NC}"
if [ -f /var/log/nginx/demomartinez-error.log ]; then
    echo "Últimas 10 líneas del log de errores:"
    tail -n 10 /var/log/nginx/demomartinez-error.log
else
    echo -e "${YELLOW}No se encontró el archivo de log${NC}"
fi

# 4. Verificar logs de PHP-FPM
echo -e "\n${YELLOW}4. Últimos errores de PHP-FPM:${NC}"
if [ -f /var/log/php8.3-fpm.log ]; then
    echo "Últimas 10 líneas del log de PHP-FPM:"
    tail -n 10 /var/log/php8.3-fpm.log
else
    echo -e "${YELLOW}No se encontró el archivo de log${NC}"
fi

# 5. Verificar logs de Laravel
echo -e "\n${YELLOW}5. Últimos errores de Laravel:${NC}"
if [ -f /var/www/martinezdevice/storage/logs/laravel.log ]; then
    echo "Últimas 20 líneas del log de Laravel:"
    tail -n 20 /var/www/martinezdevice/storage/logs/laravel.log
else
    echo -e "${YELLOW}No se encontró el archivo de log${NC}"
fi

# 6. Verificar permisos de storage y sessions
echo -e "\n${YELLOW}6. Verificando permisos de storage...${NC}"
if [ -d /var/www/martinezdevice/storage/framework/sessions ]; then
    ls -la /var/www/martinezdevice/storage/framework/ | grep sessions
    echo "Contenido de sessions:"
    ls -la /var/www/martinezdevice/storage/framework/sessions/ | head -n 5
else
    echo -e "${RED}✗ Directorio de sesiones no existe${NC}"
fi

# 7. Verificar configuración de PHP-FPM
echo -e "\n${YELLOW}7. Configuración de PHP-FPM:${NC}"
if [ -f /etc/php/8.3/fpm/pool.d/www.conf ]; then
    echo "Configuración actual:"
    grep -E "^pm.max_children|^pm.start_servers|^pm.min_spare_servers|^pm.max_spare_servers|^request_terminate_timeout" /etc/php/8.3/fpm/pool.d/www.conf
fi

# 8. Probar conexión local
echo -e "\n${YELLOW}8. Probando conexión local al servidor...${NC}"
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" -H "Host: demomartinez.bsolutions.dev" http://localhost)
echo "Código de respuesta: $RESPONSE"

if [ "$RESPONSE" = "200" ] || [ "$RESPONSE" = "302" ]; then
    echo -e "${GREEN}✓ Servidor responde correctamente${NC}"
else
    echo -e "${RED}✗ Servidor no responde correctamente${NC}"
fi

# 9. Verificar memoria y recursos
echo -e "\n${YELLOW}9. Uso de recursos:${NC}"
echo "Memoria:"
free -h
echo ""
echo "Uso de disco:"
df -h /var/www/martinezdevice

# 10. Sugerencias
echo -e "\n${YELLOW}=== SUGERENCIAS ===${NC}"
echo ""
echo "Si el error persiste después del login, prueba:"
echo ""
echo "1. Aumentar timeout de PHP-FPM:"
echo "   sudo nano /etc/php/8.3/fpm/pool.d/www.conf"
echo "   Agregar o cambiar: request_terminate_timeout = 300"
echo ""
echo "2. Aumentar timeout de Nginx:"
echo "   sudo nano /etc/nginx/sites-available/demomartinez.bsolutions.dev"
echo "   Agregar en location ~ \.php$: fastcgi_read_timeout 300;"
echo ""
echo "3. Limpiar sesiones antiguas:"
echo "   sudo rm -rf /var/www/martinezdevice/storage/framework/sessions/*"
echo ""
echo "4. Limpiar cachés de Laravel:"
echo "   cd /var/www/martinezdevice"
echo "   sudo php artisan optimize:clear"
echo "   sudo php artisan config:cache"
echo ""
echo "5. Reiniciar servicios:"
echo "   sudo systemctl restart php8.3-fpm"
echo "   sudo systemctl reload nginx"
echo ""

