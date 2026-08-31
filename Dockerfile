FROM php:8.2-cli

WORKDIR /var/www/html

# ------------------------------------------------------------
# System dependencies
# ------------------------------------------------------------

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        pdo_pgsql \
        pgsql \
        mbstring \
        bcmath \
        exif \
        pcntl \
        intl \
        zip \
        gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


# ------------------------------------------------------------
# Composer
# ------------------------------------------------------------

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


# ------------------------------------------------------------
# PHP dependencies
# ------------------------------------------------------------

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts


# ------------------------------------------------------------
# Application
# ------------------------------------------------------------

COPY . .


# ------------------------------------------------------------
# Laravel directories
# ------------------------------------------------------------

RUN mkdir -p \
    storage/app/public \
    storage/app/public/stories \
    storage/app/public/posts \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache


# ------------------------------------------------------------
# Permissions
# ------------------------------------------------------------

RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache


# ------------------------------------------------------------
# Production environment
# ------------------------------------------------------------

ENV APP_ENV=production
ENV APP_DEBUG=false


# ------------------------------------------------------------
# Render port
# ------------------------------------------------------------

EXPOSE 10000


# ------------------------------------------------------------
# Start Laravel
# ------------------------------------------------------------

CMD sh -c '\
    php artisan migrate --force && \
    php artisan optimize && \
    php artisan serve \
        --host=0.0.0.0 \
        --port=${PORT:-10000} \
'