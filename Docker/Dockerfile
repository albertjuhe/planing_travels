FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip gd intl

RUN pecl install redis xdebug-3.2.0 && docker-php-ext-enable redis xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

ENV DISPLAY_ERRORS="off"
ENV ERROR_REPORTING="E_ALL & ~E_DEPRECATED & ~E_STRICT"

COPY wait_to_start.sh /var/www/html
COPY php.ini /usr/local/etc/php/

EXPOSE 9000
WORKDIR /var/www/html
