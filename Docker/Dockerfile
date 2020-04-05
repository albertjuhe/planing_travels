FROM php:7.3-apache

RUN apt-get update &&\
    pecl install xdebug &&\
    docker-php-ext-enable xdebug &&\
    docker-php-ext-install pdo pdo_mysql

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/bin --filename=composer \
    && rm composer-setup.php

ENV DISPLAY_ERRORS = "off"
ENV ERROR_REPORTING = "E_ALL & ~E_DEPRECATED & ~E_STRICT"

COPY wait_to_start.sh /var/www/html
COPY php.ini  /usr/local/etc/php

EXPOSE 9000
WORKDIR /var/www/html

