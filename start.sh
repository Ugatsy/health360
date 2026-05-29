#!/bin/bash
set -e

echo "========================================="
echo "Starting Health360 Application on Render"
echo "========================================="

# Get port from Render or default to 8000
PORT=${PORT:-8000}
echo "Using port: $PORT"

# Update nginx to use Render's port
sed -i "s/listen 8000;/listen $PORT;/g" /etc/nginx/sites-available/default

# Wait for database (using Render's environment variables)
echo "Waiting for database connection..."
MAX_RETRIES=30
RETRY_COUNT=0
until php artisan db:show > /dev/null 2>&1; do
    RETRY_COUNT=$((RETRY_COUNT+1))
    if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
        echo "Database connection failed after $MAX_RETRIES attempts"
        echo "Database host: ${DB_HOST:-not set}"
        exit 1
    fi
    echo "Database unavailable - attempt $RETRY_COUNT/$MAX_RETRIES, retrying in 2 seconds..."
    sleep 2
done
echo "Database is ready!"

# Generate app key if not set
if [ ! -f "/var/www/html/.env" ] || ! grep -q "APP_KEY=base64:" /var/www/html/.env; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Clear and cache config
echo "Caching configuration..."
php artisan config:clear
php artisan config:cache

# Clear and cache routes
echo "Caching routes..."
php artisan route:cache

# Clear and cache views
echo "Caching views..."
php artisan view:cache

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Create storage link
echo "Creating storage link..."
php artisan storage:link --force

# Start Laravel scheduler (for emergency alerts, notifications)
echo "Starting Laravel scheduler..."
(
    while true; do
        php /var/www/html/artisan schedule:run --verbose --no-interaction >> /var/www/html/storage/logs/scheduler.log 2>&1
        sleep 60
    done
) &

# Start queue worker (for SMS sending, email notifications)
echo "Starting queue worker..."
(
    while true; do
        php /var/www/html/artisan queue:work --sleep=3 --tries=3 --max-time=3600 >> /var/www/html/storage/logs/queue.log 2>&1
        sleep 1
    done
) &

echo "========================================="
echo "Health360 is running on port $PORT"
echo "========================================="

# Start PHP-FPM
php-fpm -D

# Start Nginx (foreground)
nginx -g 'daemon off;'
