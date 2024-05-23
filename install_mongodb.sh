#!/bin/bash

# Actualizar paquetes y agregar el repositorio de PECL
apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb
