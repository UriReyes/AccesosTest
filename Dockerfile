# Etapa 1: Builder de Node.js para compilar frontend
FROM node:20-bullseye AS node-builder

WORKDIR /app

# Copiar package.json y package-lock.json para cachear npm install
COPY package*.json ./

RUN npm install

# Copiar todo el código para poder compilar assets
COPY . .

RUN npm run build

# Etapa 2: Imagen base PHP con extensiones y composer
FROM php:8.3-fpm-bullseye

# Instalar dependencias del sistema necesarias y extensiones PHP
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    git \
    nginx \
    && docker-php-ext-install pdo pdo_pgsql zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear directorio de la app
WORKDIR /var/www/html

# Copiar todo el código (excepto node_modules) desde la build local
COPY . .

# Copiar los assets compilados del frontend desde la etapa node-builder
COPY --from=node-builder /app/public/build ./public/build

# Instalar dependencias PHP sin dev (correr composer install)
RUN composer install --no-dev --optimize-autoloader

# Copiar configuración nginx (supongo que tienes el archivo conf/nginx-site.conf)
COPY conf/nginx-site.conf /etc/nginx/sites-available/default

# Ajustes de permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer puertos
EXPOSE 80

# Script de arranque para iniciar php-fpm y nginx juntos
CMD service nginx start && php-fpm
