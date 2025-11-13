# ----------------------------
# Stage 0: Build PHP environment
# ----------------------------
FROM php:8.4-fpm-bullseye

# Set working directory
WORKDIR /var/www/html

# ----------------------------
# Install system dependencies
# ----------------------------
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    supervisor \
    nano \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ----------------------------
# Install Composer globally
# ----------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ----------------------------
# Copy project files
# ----------------------------
COPY . .

# ----------------------------
# Set permissions (Laravel-specific)
# ----------------------------
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# ----------------------------
# Expose port for PHP-FPM
# ----------------------------
EXPOSE 9000

# ----------------------------
# Supervisor configuration
# ----------------------------
# Ensure supervisor logs folder exists
RUN mkdir -p /var/log/supervisor

# CMD to start supervisor (for queues, cron, etc.)
CMD ["supervisord", "-c", "/var/www/html/supervisord.conf"]
