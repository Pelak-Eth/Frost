#!/bin/sh
set -e

# Ensure a .env exists so artisan commands that read it don't choke
if [ ! -f .env ] && [ -f .env.example ]; then
    echo ">> Bootstrapping .env from .env.example"
    cp .env.example .env
fi

# Generate app key if missing
if [ -z "$APP_KEY" ] && ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
    echo ">> Generating APP_KEY"
    php artisan key:generate --force --no-interaction
fi

# Wait for database
if [ -n "$DB_HOST" ]; then
    echo ">> Waiting for database at $DB_HOST:${DB_PORT:-3306}..."
    until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT:-3306}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
        sleep 1
    done
    echo ">> Database is up"
fi

# Run migrations + seed
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo ">> Running migrations"
    php artisan migrate --force --no-interaction
fi

if [ "${RUN_SEEDER:-false}" = "true" ]; then
    echo ">> Seeding database"
    php artisan db:seed --force --no-interaction
fi

# Clear + warm caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ">> Starting: $*"
exec "$@"
