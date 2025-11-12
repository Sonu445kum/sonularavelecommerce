# 1️ Base PHP image
FROM php:8.4-fpm

# 2️ Working directory
WORKDIR /var/www/html

# 3️ Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libonig-dev \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-install pdo_mysql mbstring zip

# 4️ Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# 5️ Copy project files
COPY . .

# 6️ Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# 7️ Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# 8️ Expose port for Render
EXPOSE 1000

# 9️ Start PHP-FPM
CMD ["php-fpm", "-F", "-R"]
