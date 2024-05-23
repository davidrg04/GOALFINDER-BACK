# Usa una imagen base de PHP con Apache
FROM php:7.4-apache

# Instala las dependencias necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    git \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Instala la extensión de MongoDB para PHP
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Establecer el directorio de trabajo en /app
WORKDIR /app

# Copia el contenido del proyecto al contenedor
COPY . .

# Instala Composer y las dependencias
RUN composer install --no-dev --optimize-autoloader

# Exponer el puerto 80
EXPOSE 80

# Iniciar Apache en el primer plano
CMD ["apache2-foreground"]
