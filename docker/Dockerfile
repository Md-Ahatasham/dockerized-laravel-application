# user php image
FROM php:8.2-fpm

# set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    unzip \
    git \
    curl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        gd \
        zip \
        exif \
        pcntl

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*


# Install Composer
COPY --from=composer:2.1 /usr/bin/composer /usr/bin/composer

# Copy the Nginx configuration and SSL certificates
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/nginx/certs /etc/nginx/certs

# Copy the application directory contents
COPY ./codes /var/www

# Set the correct permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Expose port 80 and 443
EXPOSE 80 443

# Start PHP-FPM and Nginx
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]