FROM php:8.3-fpm

# Системные пакеты
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev libicu-dev libpq-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mbstring pdo_mysql zip intl opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Опционально: повышаем производительность FPM немного
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
 && echo "opcache.enable=1\nopcache.enable_cli=1\nopcache.validate_timestamps=0\nopcache.jit_buffer_size=64M\nopcache.jit=1255" >> $PHP_INI_DIR/conf.d/opcache.ini

# Права
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data
