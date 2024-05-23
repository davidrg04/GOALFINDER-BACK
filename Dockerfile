# Usar la imagen oficial de PHP con Apache como base
FROM php:7.4-apache

# Instalar las dependencias necesarias y la extensión de MongoDB
RUN apt-get update && \
    apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    git \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# Añadir ServerName en la configuración de Apache para suprimir la advertencia
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copiar los archivos de la aplicación al directorio del servidor
COPY . /var/www/html

# Exponer el puerto 80
EXPOSE 80

# Configurar el punto de entrada del contenedor
CMD ["apache2-foreground"]
