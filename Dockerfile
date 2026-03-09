FROM php:8.2-fpm

# Install PostgreSQL extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY app/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html
