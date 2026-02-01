# PHP 8.4 sa Apache
FROM php:8.4-apache

# System dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    libwebp-dev \
    libicu-dev \
    ffmpeg

# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP extensions za Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    curl \
    xml \
    intl

# XDebug za debugging
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Apache modules - SSL dodato
RUN a2enmod rewrite
RUN a2enmod headers
RUN a2enmod ssl
# RUN a2enmod proxy
# RUN a2enmod proxy_fcgi

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Working directory
WORKDIR /var/www/html

# Adjust Apache document root za Laravel public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Kopiraj SSL konfiguraciju
COPY laravel-ssl.conf /etc/apache2/sites-available/000-default.conf

# Set entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]

# Port - dodato 443
EXPOSE 80 443

CMD ["apache2-foreground"]
