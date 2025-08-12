# docker/Dockerfile

FROM php:8.3-fpm-bullseye

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev zlib1g-dev libpq-dev \
&&  docker-php-ext-install zip pdo_pgsql \
&&  rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN docker-php-ext-enable opcache || true

EXPOSE 9000

CMD ["php-fpm", "-F"]