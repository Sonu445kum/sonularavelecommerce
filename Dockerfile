# ==========================================================
# 1️⃣ Base Image (PHP 8.4 with FPM)
# ==========================================================
FROM php:8.4-fpm

# ==========================================================
# 2️⃣ Install System Dependencies + PHP Extensions + Nginx
# ==========================================================
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    libonig-dev \
    libzip-dev \
    zip \
    curl \
    supervisor \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath ctype xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ==========================================================
# 3️⃣ Set Working Directory
# ==========================================================
WORKDIR /var/www/html

# ==========================================================
# 4️⃣ Copy Project Files
# ==========================================================
COPY . .

# ==========================================================
# 5️⃣ Install Composer Dependencies (Production Mode)
# ==========================================================
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# ==========================================================
# 6️⃣ Set Correct Permissions for Laravel
# ==========================================================
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ==========================================================
# 7️⃣ Laravel Optimizations & Caching
# ==========================================================
RUN php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear \
    && php artisan storage:link \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# ==========================================================
# 8️⃣ Configure Nginx
# ==========================================================
RUN rm -f /etc/nginx/sites-enabled/default
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

# ==========================================================
# 9️⃣ Configure Supervisor to Run Nginx + PHP-FPM
# ==========================================================
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ==========================================================
# 🔟 Expose Port 80 for Render
# ==========================================================
EXPOSE 80

# ==========================================================
# 🚀 Start Supervisor (Handles Nginx + PHP-FPM)
# ==========================================================
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
