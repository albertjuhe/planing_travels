FROM php:8.1-apache
RUN apt-get update
RUN pecl install redis
RUN pecl install xdebug
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-enable redis xdebug

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/bin --filename=composer
RUN rm composer-setup.php

ENV DISPLAY_ERRORS = "off"
ENV ERROR_REPORTING = "E_ALL & ~E_DEPRECATED & ~E_STRICT"

COPY wait_to_start.sh /var/www/html
COPY php.ini  /usr/local/etc/php

EXPOSE 9000
WORKDIR /var/www/html

