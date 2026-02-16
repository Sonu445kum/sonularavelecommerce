# # ----------------------------
# # Stage 0: Build PHP environment
# # ----------------------------

# FROM php:8.4-fpm-bullseye

# # Set working directory
# WORKDIR /var/www/html

# # ----------------------------
# # Install system dependencies
# # ----------------------------

# RUN apt-get update --fix-missing -o Acquire::Retries=5 -o Acquire::http::Timeout="30" \
#     && apt-get install -y --no-install-recommends \
#         git \
#         unzip \
#         libzip-dev \
#         libonig-dev \
#         libxml2-dev \
#         curl \
#         supervisor \
#         nano \
#     && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath \
#     && apt-get clean \
#     && rm -rf /var/lib/apt/lists/*

# # ----------------------------
# # Install Composer globally
# # ----------------------------

# COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# # ----------------------------
# # Copy project files
# # ----------------------------

# COPY . .

# # ----------------------------
# # Set permissions (Laravel-specific)
# # ----------------------------

# RUN chown -R www-data:www-data /var/www/html \
#     && chmod -R 775 storage bootstrap/cache

# # ----------------------------
# # Ensure Laravel storage logs folder and log files exist
# # ----------------------------

# RUN mkdir -p /var/www/html/storage/logs \
#     && touch /var/www/html/storage/logs/php-fpm.log \
#     && touch /var/www/html/storage/logs/queue.log \
#     && chown -R www-data:www-data /var/www/html/storage \
#     && chmod -R 775 /var/www/html/storage

# # ----------------------------
# # Supervisor configuration
# # ----------------------------
# # Ensure supervisor logs folder exists

# RUN mkdir -p /var/log/supervisor

# # ----------------------------
# # Expose port for PHP-FPM
# # ----------------------------

# EXPOSE 9000

# # ----------------------------
# # CMD to start supervisor (for queues, cron, etc.)
# # ----------------------------
    
# CMD ["supervisord", "-c", "/var/www/html/supervisord.conf"]


# new docker file for the fix the cache path

FROM php:8.2-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader

# Create required Laravel folders
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 10000

CMD php artisan cache:clear && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    php artisan serve --host=0.0.0.0 --port=10000