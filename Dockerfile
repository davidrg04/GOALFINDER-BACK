# Usa una imagen base de PHP con Apache
FROM php:7.4-apache

# Instala extensiones necesarias para MongoDB
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Instala la extensión de MongoDB para PHP
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Copia el contenido del proyecto al contenedor
COPY . /var/www/html/

# Instala Composer y las dependencias
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Exponer el puerto 80
EXPOSE 80
