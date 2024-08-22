# Step 1: Use the official PHP image with FPM
FROM php:8.2-fpm

# Step 2: Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
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
    nano \
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

# Step 3: Configure Nginx
RUN rm /etc/nginx/sites-enabled/default

#COPY ./docker/nginx/nginx.conf /etc/nginx/conf.d/nginx.conf
# Configure PHP-FPM to listen on a Unix socket
RUN sed -i "s|^listen = .*|listen = /var/run/php/php8.2-fpm.sock|" /usr/local/etc/php-fpm.d/www.conf
# Copy Nginx configuration files
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

# Step 4: Add Supervisor configuration file
COPY ./docker/supervisor/supervisord.conf /etc/supervisor/
COPY ./docker/supervisor/conf.d/*  /etc/supervisor/conf.d/

# Step 5: Set the working directory
WORKDIR /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir /var/run/php
RUN chown root:www-data /var/run/php
RUN chmod 0777 /var/run/php
# Step 6: Copy the application code to the container
COPY ./codes /var/www

# Copy existing application directory permissions
COPY --chown=www-data:www-data ./codes /var/www

# Set ownership and permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache


USER root
# Step 8: Expose ports
EXPOSE 80 443

# Step 9: Start Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]