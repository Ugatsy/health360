FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    nginx \
    supervisor \
    cron \
    postgresql-client \
    libpq-dev \
    libicu-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js for Vite
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Set working directory
WORKDIR /var/www/html

# -------------------------------------------------------
# FIX: Copy package files first and install node modules
# BEFORE copying the full app. This ensures node_modules
# is present when npm run build executes, and Docker layer
# caching speeds up rebuilds when only PHP files change.
# -------------------------------------------------------
COPY package.json package-lock.json* ./
RUN npm ci

# Copy application files
COPY . .

# Copy Nginx configuration
COPY nginx.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Copy start script
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# -------------------------------------------------------
# FIX: Build Vite assets AFTER copying all app files so
# resources/css and resources/js are available.
# The built output lands in public/build/manifest.json
# which must exist inside the image before runtime.
# -------------------------------------------------------
RUN npm run build

# Verify the manifest was actually produced — fail fast if not
RUN test -f /var/www/html/public/build/manifest.json \
    || (echo "❌ Vite build failed: manifest.json missing" && exit 1)

# -------------------------------------------------------
# FIX: node_modules are only needed at build time.
# Remove them to keep the final image smaller (~300MB saved).
# -------------------------------------------------------
RUN rm -rf node_modules

# Create necessary directories and set permissions
RUN mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html

EXPOSE 8000

CMD ["/start.sh"]
