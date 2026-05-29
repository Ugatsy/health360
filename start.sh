#!/bin/bash
set -e

echo "========================================="
echo "Starting Health360 Application on Render"
echo "========================================="

PORT=${PORT:-8000}
echo "Using port: $PORT"

# Update nginx port
sed -i "s/listen 8000;/listen $PORT;/g" /etc/nginx/sites-available/default

# Wait for Supabase connection
echo "Waiting for Supabase PostgreSQL..."
MAX_RETRIES=30
COUNT=0

until pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" 2>/dev/null; do
    COUNT=$((COUNT+1))
    if [ $COUNT -ge $MAX_RETRIES ]; then
        echo "❌ Supabase connection failed after $MAX_RETRIES attempts"
        echo "DB_HOST: $DB_HOST"
        echo "DB_PORT: $DB_PORT"
        echo "DB_USERNAME: $DB_USERNAME"
        exit 1
    fi
    echo "⏳ Waiting for Supabase... ($COUNT/$MAX_RETRIES)"
    sleep 2
done

echo "✅ Supabase is ready!"

# Generate .env file if not exists
if [ ! -f "/var/www/html/.env" ]; then
    echo "Creating .env file..."
    cat > /var/www/html/.env << EOF
APP_NAME=Health360
APP_ENV=${APP_ENV:-production}
APP_DEBUG=${APP_DEBUG:-false}
APP_KEY=${APP_KEY}
APP_URL=${APP_URL}

DB_CONNECTION=pgsql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
PGSSLMODE=require

AI_API_KEY=${AI_API_KEY}
AI_ENDPOINT=${AI_ENDPOINT}
AI_MODEL=${AI_MODEL}
AI_MAX_TOKENS=${AI_MAX_TOKENS}
AI_TEMPERATURE=${AI_TEMPERATURE}

TEXTBEE_API_KEY=${TEXTBEE_API_KEY}
TEXTBEE_DEVICE_ID=${TEXTBEE_DEVICE_ID}
TEXTBEE_API_URL=${TEXTBEE_API_URL}
TEXTBEE_TIMEOUT=${TEXTBEE_TIMEOUT}

LOG_CHANNEL=stderr
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=database
EOF
fi

# Generate app key if not set in .env
if ! grep -q "APP_KEY=base64:" /var/www/html/.env; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Run Laravel setup
echo "Running migrations..."
php artisan migrate --force

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Caching views..."
php artisan view:cache

echo "Creating storage link..."
php artisan storage:link --force

echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx..."
nginx -g 'daemon off;'
