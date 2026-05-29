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

# Function to check PostgreSQL connection
check_postgres() {
    php -r "
        try {
            \$pdo = new PDO('pgsql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');
            echo 'connected';
        } catch (Exception \$e) {
            echo 'failed';
        }
    " 2>/dev/null
}

# Wait for Supabase PostgreSQL
echo "Waiting for Supabase PostgreSQL connection..."
echo "Host: ${DB_HOST}"
echo "Database: ${DB_DATABASE}"

MAX_RETRIES=30
RETRY_COUNT=0

until [ "$(check_postgres)" = "connected" ]; do
    RETRY_COUNT=$((RETRY_COUNT+1))
    if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
        echo "❌ Supabase database connection failed after $MAX_RETRIES attempts"
        echo "Please check:"
        echo "  - DB_HOST: ${DB_HOST}"
        echo "  - DB_PORT: ${DB_PORT}"
        echo "  - DB_USERNAME: ${DB_USERNAME}"
        echo "  - Supabase credentials are correct"
        exit 1
    fi
    echo "⏳ Supabase unavailable - attempt $RETRY_COUNT/$MAX_RETRIES, retrying in 2 seconds..."
    sleep 2
done

echo "✅ Supabase database is ready!"

# Generate app key if not set
if [ ! -f "/var/www/html/.env" ] || ! grep -q "APP_KEY=base64:" /var/www/html/.env; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Create .env file from environment variables (for Laravel)
if [ ! -f "/var/www/html/.env" ]; then
    echo "Creating .env file from environment..."
    cat > /var/www/html/.env << EOF
APP_NAME=Health360
APP_ENV=${APP_ENV}
APP_DEBUG=${APP_DEBUG}
APP_KEY=${APP_KEY}
APP_URL=${APP_URL}

DB_CONNECTION=${DB_CONNECTION}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
PGSSLMODE=require

LOG_CHANNEL=${LOG_CHANNEL}
SESSION_DRIVER=file
CACHE_STORE=database
QUEUE_CONNECTION=database

AI_API_KEY=${AI_API_KEY}
AI_ENDPOINT=${AI_ENDPOINT}
AI_MODEL=${AI_MODEL}
AI_MAX_TOKENS=${AI_MAX_TOKENS}
AI_TEMPERATURE=${AI_TEMPERATURE}

TEXTBEE_API_KEY=${TEXTBEE_API_KEY}
TEXTBEE_DEVICE_ID=${TEXTBEE_DEVICE_ID}
TEXTBEE_API_URL=${TEXTBEE_API_URL}
TEXTBEE_TIMEOUT=${TEXTBEE_TIMEOUT}
EOF
fi

# Clear and cache configuration
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

# Start Laravel scheduler
echo "Starting Laravel scheduler..."
(
    while true; do
        php /var/www/html/artisan schedule:run --verbose --no-interaction >> /var/www/html/storage/logs/scheduler.log 2>&1
        sleep 60
    done
) &

# Start queue worker
echo "Starting queue worker..."
(
    while true; do
        php /var/www/html/artisan queue:work --sleep=3 --tries=3 --max-time=3600 >> /var/www/html/storage/logs/queue.log 2>&1
        sleep 1
    done
) &

echo "========================================="
echo "✅ Health360 is running on port $PORT"
echo "========================================="

# Start PHP-FPM
php-fpm -D

# Start Nginx (foreground)
nginx -g 'daemon off;'
