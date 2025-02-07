FROM debian:bookworm-slim

ARG PHP_VERSION=8.2

RUN apt-get update
RUN apt-get -y upgrade

RUN apt-get -y install lsb-release unzip gnupg curl supervisor nginx

RUN apt-get -y install php${PHP_VERSION}-fpm php${PHP_VERSION}-zip php${PHP_VERSION}-sqlite3 php${PHP_VERSION}-curl php${PHP_VERSION}-pdo-sqlite php${PHP_VERSION}-zmq php${PHP_VERSION}-xml php${PHP_VERSION}-pcov php${PHP_VERSION}-xdebug php${PHP_VERSION}-mbstring php${PHP_VERSION-cli}

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN touch /var/log/php${PHP_VERSION}-fpm.log
RUN mkdir -p /var/run/php

# Copy new entry point file
COPY docker-deploy-entrypoint-custom /usr/local/bin/

# php fpm config
COPY ./php/www.conf /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf

# php ini
COPY ./php/local.ini /etc/php/${PHP_VERSION}/fpm/conf.d

#php opcache.ini
#COPY ./php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Copy supervisor config
COPY ./supervisor/app-supervisor.conf /etc/supervisord.conf

WORKDIR /var/www/

ENTRYPOINT ["/usr/local/bin/docker-deploy-entrypoint-custom"]

EXPOSE 80

STOPSIGNAL SIGTERM