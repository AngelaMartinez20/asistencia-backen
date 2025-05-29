# Usa PHP con Apache
FROM php:8.2-apache

# Instala dependencias necesarias del sistema
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    zip \
    nano \
    curl \
    && docker-php-ext-install pdo pdo_pgsql

# Copia el proyecto al contenedor
COPY . /var/www/html/

# Ajusta permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80
