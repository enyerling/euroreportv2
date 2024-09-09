# Utiliza la imagen base de PHP con Apache
FROM php:8.0-apache

# Instala extensiones necesarias para Laravel
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql zip

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configura el directorio de trabajo
WORKDIR /var/www/html

# Copia los archivos de la aplicación al contenedor
COPY . /var/www/html

# Asigna los permisos adecuados a la carpeta de almacenamiento de Laravel
RUN chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage

# Habilita el módulo de reescritura de Apache
RUN a2enmod rewrite

# Ejecuta Composer para instalar las dependencias de Laravel
RUN composer install --optimize-autoloader --no-dev

# Expone el puerto 80 para Apache
EXPOSE 80

# Ejecuta los comandos necesarios al iniciar el contenedor
CMD ["apache2-foreground"]
