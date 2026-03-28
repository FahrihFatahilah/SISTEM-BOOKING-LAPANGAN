#!/bin/bash

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
while ! nc -z mysql 3306; do
  sleep 1
done
echo "MySQL is ready!"

# Generate app key if not exists
if [ ! -f /var/www/.env ]; then
    cp /var/www/.env.example /var/www/.env
fi

# Override .env with Docker environment variables
php /var/www/artisan key:generate --force

# Clear all cache
php /var/www/artisan config:clear
php /var/www/artisan route:clear
php /var/www/artisan view:clear
php /var/www/artisan cache:clear

# Run migrations
php /var/www/artisan migrate --force

# Seed database if needed
php /var/www/artisan db:seed --force

# Create storage link
php /var/www/artisan storage:link 2>/dev/null || true

# Set proper permissions
chown -R www-data:www-data /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
