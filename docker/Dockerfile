# docker/Dockerfile

# Stage 1: Build assets using Node.js
FROM node:18 as build-stage

WORKDIR /var/www

# Copy package.json and package-lock.json for Node.js dependencies
COPY ../codes/package*.json ./

# Install Node.js dependencies
RUN npm install

# Copy the rest of the application code
COPY ../codes .

# Build assets using Vite
RUN npm run build

# Stage 2: Setup PHP, Composer, and Nginx
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    nginx

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application code
COPY ../codes .

# Copy built assets from the build stage
COPY --from=build-stage /var/www/public ./public

# Copy Nginx configuration file
COPY nginx/default.conf /etc/nginx/default.conf

# Expose port 80
EXPOSE 80

# Start PHP-FPM and Nginx
CMD service nginx start && php-fpm