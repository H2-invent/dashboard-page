#!/bin/sh
set -eu

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

php bin/console cache:clear
exec php -S 0.0.0.0:8000 -t public
