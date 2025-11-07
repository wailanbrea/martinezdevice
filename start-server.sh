#!/bin/bash
# Script para iniciar Laravel en el puerto 3001
# Uso: ./start-server.sh

cd "$(dirname "$0")"
php artisan serve --host=127.0.0.1 --port=3001

