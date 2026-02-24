FROM php:8.2-cli

# Install system deps
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libzip-dev zip

# Enable required PHP extensions
RUN docker-php-ext-install gd zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader

CMD php -S 0.0.0.0:$PORT -t public