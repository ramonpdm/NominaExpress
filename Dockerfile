# Use the official PHP image with Apache
FROM php:8.4-apache

# Install system dependencies and PHP extensions
RUN apt-get update  \
    && apt-get install -y \
        libzip-dev \
        zip \
        git \
        curl \
    && docker-php-ext-install mysqli pdo pdo_mysql  \
    && docker-php-ext-enable pdo_mysql

# Enable Apache mod_rewrite and SSL
RUN a2enmod rewrite

# Install Xdebug
RUN pecl install -o -f xdebug-3.4.2 \
    && docker-php-ext-enable xdebug

# Download and install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy application source
COPY . /var/www/html

# Copy php.ini
COPY ./php.ini /usr/local/etc/php/

# Set the correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
