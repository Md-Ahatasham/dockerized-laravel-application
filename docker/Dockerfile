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

# Step 4: Configure Nginx
RUN rm /etc/nginx/sites-enabled/default
COPY ./nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./nginx/certs /etc/nginx/certs


# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www

# Set ownership and permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache
# Change current user to www
USER www-data

# Step 7: Expose port 80
EXPOSE 80

# Step 8: Start PHP-FPM and Nginx
CMD service php8.2-fpm start && nginx -g "daemon off;"
