FROM php:7.4-apache

# Configurar el ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copiar el contenido del proyecto al directorio raíz de Apache
COPY . /var/www/html/

# Instalar las extensiones necesarias de PHP si es necesario
# RUN docker-php-ext-install mysqli pdo pdo_mysql
