# Base image
FROM php:8.2-apache

# Install necessary extensions
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip pdo pdo_mysql mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project files
COPY . /var/www/html/

# Set permissions for the Apache web server
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Update Apache configuration to allow .htaccess
RUN echo '<Directory /var/www/html/> \n\
    AllowOverride All \n\
    Require all granted \n\
</Directory>' > /etc/apache2/conf-available/override.conf && \
    a2enconf override

# Expose the default Apache port
EXPOSE 80
