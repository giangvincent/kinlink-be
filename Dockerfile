FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libmagickwand-dev \
    libicu-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && rm -rf /var/lib/apt/lists/*

# Install Composer (from official Composer image)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Install PHP dependencies (no dev)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Ensure correct permissions
RUN chown -R www-data:www-data /var/www/html

# Render sets $PORT. Use it, defaulting to 8000 for local runs.
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8000} -t public"]
