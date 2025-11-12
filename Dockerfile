# 1️⃣ Base PHP-FPM image
FROM php:8.4-fpm

# 2️⃣ Install system dependencies + PHP extensions + Nginx
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    libonig-dev \
    libzip-dev \
    zip \
    curl \
    supervisor \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath ctype tokenizer xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 3️⃣ Set working directory
WORKDIR /var/www/html

# 4️⃣ Copy project files
COPY . .

# 5️⃣ Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer
RUN composer install --optimize-autoloader --no-dev

# 6️⃣ Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# 7️⃣ Configure Nginx for Laravel
RUN rm /etc/nginx/sites-enabled/default
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

# 8️⃣ Copy Supervisor config to run both Nginx + PHP-FPM
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# 9️⃣ Expose port 80
EXPOSE 80

# 10️⃣ Start Supervisor (manages both Nginx & PHP-FPM)
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
