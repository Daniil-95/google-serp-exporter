# =============================================================================
# Dockerfile - Google SERP Exporter
# =============================================================================

FROM php:8.4-cli-alpine

ENV COMPOSER_ALLOW_SUPERUSER=1

# System dependencies + PHP extensions
RUN apk add --no-cache \
        curl \
        git \
        unzip \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
    && docker-php-ext-install \
        intl \
        mbstring \
        zip

# Composer
COPY --from=composer/composer:2-bin /composer /usr/bin/composer

# Working directory
WORKDIR /var/www/html

# Copy source code
COPY . .

# Runtime directories
RUN mkdir -p temp log storage \
    && chmod -R 777 temp log storage

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader

# Expose application port
EXPOSE 8000

# Start built-in PHP server
CMD ["php", "-S", "0.0.0.0:8000", "-t", "www"]