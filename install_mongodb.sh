#!/bin/bash

# Instalar software-properties-common para poder usar add-apt-repository
apt-get update && apt-get install -y software-properties-common

# Añadir el repositorio para PHP 8.2
add-apt-repository ppa:ondrej/php -y

# Actualizar el paquete e instalar las dependencias necesarias
apt-get update && apt-get install -y php-pear php8.2-dev

# Instalar la extensión de MongoDB
pecl install mongodb && echo "extension=mongodb.so" > /etc/php/8.2/cli/conf.d/20-mongodb.ini

# Instalar las dependencias de Composer
composer install --ignore-platform-reqs

