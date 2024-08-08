# Usar la imagen oficial de PHP con Apache
FROM php:7.4-apache

# Copiar el contenido del proyecto al directorio raíz de Apache
COPY . /var/www/html/

# Configurar el ServerName para Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Establecer los permisos correctos para los archivos y carpetas
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Habilitar mod_rewrite si es necesario
RUN a2enmod rewrite

# Exponer el puerto 80
EXPOSE 80

# Comando de inicio para Apache
CMD ["apache2-foreground"]
