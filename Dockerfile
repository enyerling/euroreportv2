# Usa una imagen base de PHP con Apache
FROM php:8.1-apache

# Instala dependencias necesarias
RUN apt-get update && \
    apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev libzip-dev unzip git nano && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd zip pdo pdo_mysql && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug
# Habilita el módulo SSL
RUN a2enmod ssl

# Configura el directorio de trabajo
WORKDIR /var/www/html

# Copia el código fuente de la aplicación
COPY . /var/www/html
# Copiar el archivo de configuración de Apache
COPY ./docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
# Copia los certificados al contenedor
COPY ./docker/ssl/euroreporte.crt /etc/ssl/certs/euroreporte.crt
COPY ./docker/ssl/clave.key /etc/ssl/private/clave.key

# Habilitar el módulo de reescritura de Apache
RUN a2enmod rewrite


# Ajusta permisos para las carpetas de almacenamiento
RUN chown -R www-data:www-data /var/www/html/storage && \
    chmod -R 777 /var/www/html/storage && \
    chown -R www-data:www-data /var/www/html/bootstrap/cache && \
    chmod -R 777 /var/www/html/bootstrap/cache

# Copia el archivo .env si lo tienes en tu contexto de construcción
COPY .env /var/www/html/.env

# Instala Composer y dependencias de PHP
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    composer install --no-dev --optimize-autoloader

# Expon el puerto 80
EXPOSE 80

# Inicia el servidor web
CMD ["apache2-foreground"]