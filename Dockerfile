FROM serversideup/php:8.4-fpm-apache

USER root

RUN install-php-extensions intl

COPY --chmod=755 docker/entrypoint.d/ /etc/entrypoint.d/

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
    && chown -R www-data:www-data /var/www/html


ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

USER www-data

EXPOSE 8080