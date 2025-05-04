FROM php:8.3-fpm

RUN apt update -q;
RUN apt-get install -y libpq-dev;
RUN docker-php-ext-install pdo pdo_pgsql pgsql;