FROM php:8.1

RUN mkdir /app
WORKDIR /app

RUN apt update
RUN apt install -y libzip-dev wget
RUN docker-php-ext-install zip

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock /app/

RUN composer install --no-scripts

COPY . /app

RUN composer update
