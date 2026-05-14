# BFP Inventory — deploy on Render (or any Docker host). Listens on $PORT.
FROM php:8.3-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libzip-dev libpng-dev libonig-dev libsqlite3-dev \
    && docker-php-ext-install -j$(nproc) pdo_sqlite zip gd mbstring pcntl opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction

COPY . .
RUN composer dump-autoload --optimize --no-dev --no-interaction \
    && mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && npm ci \
    && npm run build \
    && rm -rf node_modules \
    && apt-get purge -y nodejs \
    && rm -rf /var/lib/apt/lists/*

RUN echo "upload_max_filesize=64M\npost_max_size=64M\nmemory_limit=256M" > /usr/local/etc/php/conf.d/zz-bfp.ini

RUN chown -R www-data:www-data storage bootstrap/cache database public/build

USER www-data

EXPOSE 10000

CMD ["/bin/sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
