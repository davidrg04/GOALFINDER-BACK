#!/bin/bash

# Actualizar paquetes
apt-get update

# Instalar las dependencias necesarias
apt-get install -y libcurl4-openssl-dev pkg-config libssl-dev

# Instalar la extensión MongoDB
pecl install mongodb

# Habilitar la extensión MongoDB
echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini
