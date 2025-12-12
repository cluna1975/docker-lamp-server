FROM php:8.2-apache

# Instalamos las extensiones necesarias para conectar con MySQL, XML y OpenSSL
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    libxml2-dev \
    libssl-dev \
    openssl \
    && docker-php-ext-install mysqli pdo pdo_mysql dom xml \
    && docker-php-ext-enable mysqli dom xml

# Activamos mod_rewrite para URLs amigables
RUN a2enmod rewrite

# Instalamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiamos el archivo de configuración de Apache
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Copiamos los archivos PHP
COPY www/ /var/www/html/
RUN chown -R www-data:www-data /var/www/html/

# Configurar permisos para directorios de datos
RUN mkdir -p /var/www/html/php/data/certificados \
    /var/www/html/php/data/xml_generados \
    /var/www/html/php/data/xml_firmados \
    /var/www/html/php/logs \
    /var/www/html/php/temp \
    && chown -R www-data:www-data /var/www/html/php/data \
    && chown -R www-data:www-data /var/www/html/php/logs \
    && chown -R www-data:www-data /var/www/html/php/temp \
    && chmod -R 755 /var/www/html/php/data \
    && chmod -R 755 /var/www/html/php/logs \
    && chmod -R 755 /var/www/html/php/temp