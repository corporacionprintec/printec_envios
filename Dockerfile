# Usar la imagen oficial de PHP con Apache
FROM php:7.4-apache

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Copiar el contenido del proyecto al directorio raíz de Apache
COPY . /var/www/html/

# Configurar el ServerName para Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Establecer los permisos correctos para los archivos y carpetas
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Habilitar mod_rewrite si es necesario
RUN a2enmod rewrite

# Instalar las extensiones necesarias de PHP si es necesario
# RUN docker-php-ext-install mysqli pdo pdo_mysql

# Instalar Node.js
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash - \
    && apt-get install -y nodejs

# Instalar dependencias de Node.js
COPY package*.json /var/www/html/
RUN cd /var/www/html && npm install

# Exponer el puerto 80
EXPOSE 80
