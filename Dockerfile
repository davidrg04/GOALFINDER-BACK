# Usa una imagen base oficial de PHP con Apache
FROM php:7.4-apache

# Instala las dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    libonig-dev \
    libxml2-dev \
    libssl-dev \
    pkg-config \
    libcurl4-openssl-dev

# Habilita y configura extensiones PHP necesarias
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd && \
    docker-php-ext-install zip && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install curl

# Instala y habilita la extensión mongodb
RUN pecl install mongodb && \
    echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini

# Copia los archivos de la aplicación al contenedor sin cambiar el directorio de trabajo
COPY . /app

# Establece el directorio de trabajo de Apache
WORKDIR /app

# Instala las dependencias de Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');" && \
    composer install --no-dev --prefer-dist

# Expone el puerto 80
EXPOSE 80

# Comando para iniciar el servidor
# CMD ["apache2-foreground"]
