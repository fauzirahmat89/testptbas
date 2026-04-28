FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    gnupg

# Install Node.js (LTS version)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd \
    && pecl install redis \
    && docker-php-ext-enable redis

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install dependencies (Composer & NPM) and Build Assets
RUN composer install --no-interaction --optimize-autoloader \
    && npm install \
    && npm run build

# Copy custom FPM configuration
COPY .docker/php/php-fpm.d/zzz-custom.conf /usr/local/etc/php-fpm.d/zzz-custom.conf

# Fix permissions
RUN chown -R www-data:www-data /var/www/html /tmp \
    && chmod -R 775 storage bootstrap/cache

USER www-data
