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
    libxml2-dev \
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
# 4️⃣ Copy Composer Binary
# ==========================================================
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# ==========================================================
# 5️⃣ Copy Project Files (after composer to use cache properly)
# ==========================================================
COPY . .

# ==========================================================
# 6️⃣ Install Composer Dependencies (Production Mode)
# ==========================================================
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# ==========================================================
# 7️⃣ Set Correct Permissions for Laravel
# ==========================================================
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ==========================================================
# 8️⃣ Laravel Optimizations & Caching
# ==========================================================
RUN php artisan config:clear \
    && php artisan cache:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && php artisan storage:link \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache || true
# (added `|| true` so build won’t fail if artisan commands need env)

# ==========================================================
# 9️⃣ Configure Nginx
# ==========================================================
RUN rm -f /etc/nginx/sites-enabled/default
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

# ==========================================================
# 🔟 Configure Supervisor to Run Nginx + PHP-FPM
# ==========================================================
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ==========================================================
# 11️⃣ Expose Port 80 for Render / Production
# ==========================================================
EXPOSE 80

# ==========================================================
# 🚀 Start Supervisor (Handles Nginx + PHP-FPM)
# ==========================================================
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
