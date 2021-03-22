FROM php:7.3-apache

RUN apt-get update &&\
    pecl install xdebug &&\
    docker-php-ext-enable xdebug &&\
    docker-php-ext-install pdo pdo_mysql &&\
    curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - &&\
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list &&\
    apt-get update &&\
    apt-get install yarn &&\
    apt install git-all &&\
    apt-get install zip unzip

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/bin --filename=composer \
    && rm composer-setup.php

ENV DISPLAY_ERRORS = "off"
ENV ERROR_REPORTING = "E_ALL & ~E_DEPRECATED & ~E_STRICT"

COPY wait_to_start.sh /var/www/html
COPY php.ini  /usr/local/etc/php

EXPOSE 9000
WORKDIR /var/www/html

