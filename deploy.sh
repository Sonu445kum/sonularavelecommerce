#!/bin/bash

# -----------------------------
# Config - Change these
# -----------------------------
REPO_URL="https://github.com/username/your-laravel-repo.git"
APP_DIR="/var/www/laravel_ap"
ENV_FILE="$APP_DIR/.env"

# -----------------------------
# Clone repo if not exists
# -----------------------------
if [ ! -d "$APP_DIR" ]; then
    git clone $REPO_URL $APP_DIR
else
    cd $APP_DIR
    git pull origin main
fi

cd $APP_DIR

# -----------------------------
# Ensure .env exists
# -----------------------------
if [ ! -f "$ENV_FILE" ]; then
    echo ".env file not found! Please create it before deploying."
    exit 1
fi

# -----------------------------
# Build and start Docker containers
# -----------------------------
docker-compose down
docker-compose build --no-cache
docker-compose up -d

# -----------------------------
# Run Laravel migrations and seed (optional)
# -----------------------------
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force

# -----------------------------
# Clear caches and optimize
# -----------------------------
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# -----------------------------
# Display logs (optional)
# -----------------------------
echo "Tere app ke logs dekhne ke liye:"
echo "Use: docker-compose logs -f"
