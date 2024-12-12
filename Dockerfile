# Usa una imagen base de PHP con FPM
FROM php:8.3-fpm

# Instalar dependencias necesarias para PHP y Laravel
RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip \
    git \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype=/usr/include/freetype2 --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    bcmath \
    intl \
    gd \
    zip

# Instalar Composer globalmente
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Instalar y configurar Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "zend_extension=xdebug.so" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.log=/var/log/xdebug.log" >> /usr/local/etc/php/conf.d/xdebug.ini

# Establecer permisos adecuados
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Exponer los puertos necesarios
EXPOSE 9003

# Establecer el directorio de trabajo
WORKDIR /var/www/html
