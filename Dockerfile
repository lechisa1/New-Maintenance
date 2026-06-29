# ---- Build Stage ----
FROM php:8.4-fpm-alpine AS builder

WORKDIR /var/www/html

# Install system dependencies for PHP and build tools
RUN apk add --no-cache \
    git curl zip unzip \
    libpng-dev libxml2-dev oniguruma-dev \
    freetype-dev libjpeg-turbo-dev \
    libzip-dev \
    nodejs npm \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy source code
COPY . .

# Install PHP dependencies
RUN if [ "$APP_ENV" = "production" ]; then \
      composer install --no-dev --optimize-autoloader; \
    else \
      composer install; \
    fi \
 && chown -R www-data:www-data storage bootstrap/cache

# Install frontend dependencies and build assets
RUN npm install && npm run build

# ---- Runtime Stage ----
FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

# Runtime PHP dependencies
RUN apk add --no-cache \
    libpng libxml2 oniguruma freetype libjpeg-turbo zip libzip-dev \
 && apk add --no-cache --virtual .build-deps \
    build-base autoconf libpng-dev libxml2-dev oniguruma-dev libjpeg-turbo-dev freetype-dev mariadb-dev libzip-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
 && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
# Copy built PHP + vendor + frontend assets from builder
COPY --from=builder /var/www/html /var/www/html

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set permissions for storage/cache
RUN chown -R www-data:www-data storage bootstrap/cache || true

EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm"]
