# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# Stage 1 — Compila los assets de frontend (Vite + Tailwind) con Node
# ---------------------------------------------------------------------------
FROM node:20-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build


# ---------------------------------------------------------------------------
# Stage 2 — Runtime PHP 8.4 + Apache
# ---------------------------------------------------------------------------
FROM php:8.4-apache AS app

# Dependencias del sistema y extensiones PHP necesarias para Laravel + MySQL
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libpng-dev \
        libonig-dev \
        libicu-dev \
    && docker-php-ext-install pdo_mysql zip gd bcmath intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Habilita mod_rewrite (rutas de Laravel)
RUN a2enmod rewrite

# Composer (copiado desde la imagen oficial)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Instala dependencias PHP de producción (cacheable: solo cambia con los locks)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Copia el código de la aplicación
COPY . .

# Trae los assets ya compilados del stage anterior
COPY --from=assets /app/public/build ./public/build

# Genera el autoloader optimizado de producción
RUN composer dump-autoload --optimize --no-dev --no-scripts

# Apache sirve desde /public
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Permisos para storage y cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Entrypoint: ajusta el puerto, cachea config y migra antes de arrancar Apache
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["entrypoint.sh"]
