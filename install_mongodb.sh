#!/bin/bash

# Actualizar paquetes y agregar el repositorio de PECL
apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    && pecl install mongodb

# Habilitar la extensión MongoDB
echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini
