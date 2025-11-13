# ==========================================================
# 1️⃣ Base Image (PHP 8.4 FPM on Debian Bookworm)
# ==========================================================
FROM php:8.4-fpm-bookworm

# ==========================================================
# 2️⃣ Set Proxy (If Meraki or Office Firewall is Active)
# ==========================================================
# 👉 Comment out these lines if you're using home/hotspot network
# ENV http_proxy="http://wired.meraki.com:8090"
# ENV https_proxy="http://wired.meraki.com:8090"
# ENV no_proxy="localhost,127.0.0.1"

# ==========================================================
# 3️⃣ (FIXED) Ensure apt sources list exists before editing
# ==========================================================
RUN test -f /etc/apt/sources.list || echo "deb http://deb.debian.org/debian bookworm main" > /etc/apt/sources.list

# Optionally, switch to a stable mirror to avoid network issues
RUN sed -i 's|http://deb.debian.org/debian|http://ftp.debian.org/debian|g' /etc/apt/sources.list || true

# ==========================================================
# 4️⃣ Install required packages safely
# ==========================================================
RUN apt-get update -o Acquire::Retries=3 -o Acquire::http::Pipeline-Depth=0 \
 && apt-get install -y --no-install-recommends \
    nginx \
    supervisor \
    git \
    unzip \
    zip \
    curl \
    libonig-dev \
    libzip-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    pkg-config \
 && docker-php-ext-install pdo_mysql mbstring zip bcmath ctype xml \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# ==========================================================
# 5️⃣ Set Working Directory
# ==========================================================
WORKDIR /var/www/html

# ==========================================================
# 6️⃣ Copy Composer from official image
# ==========================================================
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# ==========================================================
# 7️⃣ Copy Laravel project files
# ==========================================================
COPY . .

# ==========================================================
# 8️⃣ Install dependencies (Production Mode)
# ==========================================================
RUN composer self-update --2 \
 && composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist || true

# ==========================================================
# 9️⃣ Set proper permissions
# ==========================================================
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# ==========================================================
# 🔟 Laravel optimization (safe build)
# ==========================================================
RUN php artisan config:clear || true \
 && php artisan cache:clear || true \
 && php artisan route:clear || true \
 && php artisan view:clear || true \
 && php artisan storage:link || true \
 && php artisan config:cache || true \
 && php artisan route:cache || true \
 && php artisan view:cache || true

# ==========================================================
# 11️⃣ Nginx config
# ==========================================================
RUN rm -f /etc/nginx/sites-enabled/default
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

# ==========================================================
# 12️⃣ Supervisor config
# ==========================================================
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ==========================================================
# 13️⃣ Expose port for production
# ==========================================================
EXPOSE 80

# ==========================================================
# 🚀 Start services (Nginx + PHP-FPM)
# ==========================================================
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
