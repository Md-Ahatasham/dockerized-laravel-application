# Use the official PHP image with FPM and necessary extensions
FROM php:8.2-fpm

# Install necessary packages and dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    && docker-php-ext-install pdo pdo_mysql opcache \
    && rm -rf /var/lib/apt/lists/*

# Create necessary directories
RUN mkdir -p /var/run/php && chmod 777 /var/run/php \
    && mkdir -p /var/log/supervisor && mkdir -p /var/www

# Set working directory
WORKDIR /var/www

# Copy the application code
COPY ./codes /var/www

# Copy the Nginx configuration
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf

# Copy the Supervisor configuration
COPY ./docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Permissions
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Expose ports
EXPOSE 80

# Start Supervisord to run Nginx and PHP-FPM
CMD ["/usr/bin/supervisord"]
