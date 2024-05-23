FROM php:8.0-cli

# Instalar las dependencias necesarias
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev

# Configurar el directorio de trabajo
WORKDIR /app

# Copiar el script de instalación
COPY install_mongodb.sh /usr/local/bin/install-mongodb.sh

# Dar permisos de ejecución al script
RUN chmod +x /usr/local/bin/install-mongodb.sh

# Instalar la extensión de MongoDB
RUN /usr/local/bin/install-mongodb.sh

# Copiar los archivos del proyecto al contenedor
COPY . /app

# Instalar las dependencias de Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && composer install --ignore-platform-reqs

# Comando por defecto
CMD ["php", "-S", "0.0.0.0:8000", "-t", "API"]
