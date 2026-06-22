#!/usr/bin/env bash
set -e

# Render inyecta el puerto en $PORT (10000 por defecto). Apache debe escuchar ahí.
PORT="${PORT:-80}"
sed -i "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Descubre paquetes (el autoloader se generó en build con --no-scripts)
php artisan package:discover --ansi || true

# Enlace de almacenamiento público para las portadas subidas
php artisan storage:link || true

# Migraciones (idempotente; --force porque corre en producción sin confirmación)
php artisan migrate --force || true

# Cachés de producción (se generan en runtime, cuando las env vars ya existen)
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec apache2-foreground
