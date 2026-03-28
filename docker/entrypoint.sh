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

# Generate application key
php /var/www/artisan key:generate --force

# Clear and cache config
php /var/www/artisan config:clear
php /var/www/artisan config:cache

# Clear and cache routes
php /var/www/artisan route:clear
php /var/www/artisan route:cache

# Clear and cache views
php /var/www/artisan view:clear
php /var/www/artisan view:cache

# Run migrations
php /var/www/artisan migrate --force

# Seed database if needed
php /var/www/artisan db:seed --force

# Create storage link
php /var/www/artisan storage:link

# Set proper permissions
chown -R www-data:www-data /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf