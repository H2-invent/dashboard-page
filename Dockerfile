FROM php:8.3-cli-alpine

RUN apk add --no-cache bash curl icu-dev libzip-dev unzip git \
    && docker-php-ext-install intl \
    && rm -rf /var/cache/apk/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY docker/start.sh /usr/local/bin/start-app
RUN chmod +x /usr/local/bin/start-app

EXPOSE 8000

CMD ["/usr/local/bin/start-app"]
