FROM php:8.2-apache
# Instalamos las extensiones necesarias para conectar con MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable mysqli

# Activamos mod_rewrite para URLs amigables
RUN a2enmod rewrite

# Copiamos el archivo de configuración de Apache
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Copiamos los archivos PHP
COPY www/ /var/www/html/