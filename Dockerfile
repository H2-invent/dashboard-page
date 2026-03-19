FROM php:8.3-cli-alpine

RUN apk add --no-cache bash curl icu-dev libzip-dev unzip git \
    && docker-php-ext-install intl \
    && rm -rf /var/cache/apk/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

EXPOSE 8000

CMD ["sh", "-lc", "composer install && php bin/console cache:clear && php -S 0.0.0.0:8000 -t public"]
