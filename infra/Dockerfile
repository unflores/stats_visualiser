FROM php:8.2-fpm AS base

RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev unzip git curl \
    && docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

################################################################################
# Development Setup
################################################################################
FROM base AS dev

RUN apt-get install -y vim

RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Copy application code
COPY . .

RUN composer install --no-scripts --no-interaction

RUN chown -R www-data:www-data /var/www/html

EXPOSE 8000

CMD ["symfony", "serve", "--port=8000", "--no-tls"]

################################################################################
# Production Setup
################################################################################
FROM base AS prod

# Copy application code
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

RUN chown -R www-data:www-data /var/www/html

# Expose the PHP-FPM port
EXPOSE 9000

# Command to run PHP-FPM
CMD ["php-fpm"]
