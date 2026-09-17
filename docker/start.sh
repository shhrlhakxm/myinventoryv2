#!/usr/bin/env sh

set -eu

echo "Running pending database migrations..."
php artisan migrate --force --no-interaction

echo "Seeding demo data..."
php artisan db:seed --force --no-interaction

echo "Caching Laravel configuration, routes, events, and views..."
php artisan optimize

echo "Starting Apache..."
exec apache2-foreground