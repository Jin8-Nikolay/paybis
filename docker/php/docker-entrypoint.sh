#!/bin/sh
set -e

echo "Waiting for database..."
until php -r "new PDO('mysql:host=db;dbname=symfony', 'symfony', 'symfony_pass');" >/dev/null 2>&1; do
  sleep 2
done

echo "Database ready, running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

echo "Starting cron..."
cron -f &

php-fpm
