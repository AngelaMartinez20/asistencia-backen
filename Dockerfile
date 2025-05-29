# Usa la imagen oficial de PHP con Apache
FROM php:8.2-apache

# Habilita extensiones necesarias (opcional)
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Copia el contenido del proyecto al servidor web
COPY . /var/www/html/

# Ajusta permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expone el puerto 80 para HTTP
EXPOSE 80
