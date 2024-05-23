#!/bin/bash
add-apt-repository ppa:ondrej/php -y
# Actualizar el paquete e instalar las dependencias necesarias
apt-get update && apt-get install -y php-pear php8.2-dev && pecl install mongodb && echo "extension=mongodb.so" > /etc/php/8.2/cli/conf.d/20-mongodb.ini

# Instalar las dependencias de Composer
composer install --ignore-platform-reqs
