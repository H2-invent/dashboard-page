#!/bin/sh
set -eu

echo "Clearing Symfony cache..."
php bin/console cache:clear