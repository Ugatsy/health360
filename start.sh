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

# FIX: Use only host and port for pg_isready — the dot in DB_USERNAME
# caused pg_isready to fail on some versions, killing the container on boot
until pg_isready -h "$DB_HOST" -p "$DB_PORT" 2>/dev/null; do
    COUNT=$((COUNT+1))
    if [ $COUNT -ge $MAX_RETRIES ]; then
        echo "❌ Supabase connection failed after $MAX_RETRIES attempts"
        echo "DB_HOST: $DB_HOST"
        echo "DB_PORT: $DB_PORT"
        exit 1
    fi
    echo "⏳ Waiting for Supabase... ($COUNT/$MAX_RETRIES)"
    sleep 2
done

echo "✅ Supabase is ready!"

# FIX: Write .env with ALL required variables so nothing is missing.
# Also forces LOG_CHANNEL=stderr (required in containers — 'stack' writes
# to disk which can fail on Render's ephemeral filesystem and cause a 500).
cat > /var/www/html/.env << EOF
APP_NAME=${APP_NAME:-Health360}
APP_ENV=${APP_ENV:-production}
APP_DEBUG=${APP_DEBUG:-false}
APP_KEY=${APP_KEY}
APP_URL=${APP_URL}

DB_CONNECTION=pgsql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE:-postgres}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
PGSSLMODE=require

AI_API_KEY=${AI_API_KEY}
AI_ENDPOINT=${AI_ENDPOINT}
AI_MODEL=${AI_MODEL:-llama-3.3-70b-versatile}
AI_MAX_TOKENS=${AI_MAX_TOKENS:-1024}
AI_TEMPERATURE=${AI_TEMPERATURE:-0.3}

TEXTBEE_API_KEY=${TEXTBEE_API_KEY}
TEXTBEE_DEVICE_ID=${TEXTBEE_DEVICE_ID}
TEXTBEE_API_URL=${TEXTBEE_API_URL}
TEXTBEE_TIMEOUT=${TEXTBEE_TIMEOUT:-30}

LOG_CHANNEL=stderr
LOG_LEVEL=${LOG_LEVEL:-debug}

SESSION_DRIVER=cookie
SESSION_LIFETIME=${SESSION_LIFETIME:-120}
SESSION_ENCRYPT=false

CACHE_STORE=file
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
BROADCAST_CONNECTION=log

MAIL_MAILER=${MAIL_MAILER:-log}
MAIL_HOST=${MAIL_HOST:-127.0.0.1}
MAIL_PORT=${MAIL_PORT:-2525}
MAIL_USERNAME=${MAIL_USERNAME:-null}
MAIL_PASSWORD=${MAIL_PASSWORD:-null}
MAIL_ENCRYPTION=${MAIL_ENCRYPTION:-null}
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS:-hello@example.com}
MAIL_FROM_NAME=${APP_NAME:-Health360}
EOF

# Generate app key if not set
if ! grep -q "APP_KEY=base64:" /var/www/html/.env; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Set correct permissions on storage after writing .env
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Create storage link
echo "Creating storage link..."
php artisan storage:link --force 2>/dev/null || true

# Clear and cache config
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx..."
nginx -g 'daemon off;'
