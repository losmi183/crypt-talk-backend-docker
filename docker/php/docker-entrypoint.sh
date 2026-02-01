#!/bin/bash

# Wait for database to be ready (opciono)
# while ! mysql -h db -u crypt_user -psecret -e "SELECT 1" >/dev/null 2>&1; do
#     echo "Waiting for database..."
#     sleep 2
# done

# Install Composer dependencies if vendor folder doesn't exist
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Generate Laravel key if not exists
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

if [ -z "$(grep '^APP_KEY=' .env)" ] || [ "$(grep '^APP_KEY=' .env | cut -d= -f2)" = "" ]; then
    echo "Generating Laravel key..."
    php artisan key:generate --force
fi

# Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Run database migrations (opciono)
#php artisan migrate --force
# php artisan db:seed --force

# Run the main command (Apache)
exec "$@"
