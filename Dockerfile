# 1️⃣ Base PHP image
FROM php:8.4-fpm

# 2️⃣ Install system dependencies + Nginx
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    libonig-dev \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-install pdo_mysql mbstring zip \
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

# 7️⃣ Configure Nginx
RUN rm /etc/nginx/sites-enabled/default
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

# 8️⃣ Expose port 80 for Render
EXPOSE 80

# 9️⃣ Start both services
CMD ["sh", "-c", "service nginx start && php-fpm -F"]
