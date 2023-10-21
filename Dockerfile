FROM php:8.2


RUN pecl install swoole

RUN docker-php-ext-enable swoole

WORKDIR /var/www