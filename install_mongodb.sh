#!/bin/bash

# Instalar software-properties-common para poder usar add-apt-repository
apt-get update && apt-get install -y software-properties-common lsb-release ca-certificates apt-transport-https

# Añadir el repositorio para PHP 8.2
add-apt-repository ppa:ondrej/php -y

# Actualizar el paquete e instalar las dependencias necesarias, incluyendo openssl y librerías de desarrollo
apt-get update && apt-get install -y php-pear php8.2-dev php8.2-openssl build-essential

# Instalar la extensión de MongoDB desde los repositorios de Ubuntu
apt-get install -y php-mongodb

# Instalar las dependencias de Composer
composer install --ignore-platform-reqs



