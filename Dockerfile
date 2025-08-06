FROM php:8.2.12-apache

# Install mysqli and other useful extensions
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Set working directory (optional but recommended)
WORKDIR /var/www/html

# Copy source code
COPY . .

# Set proper permissions (optional, tergantung use case)
RUN chown -R www-data:www-data /var/www/html
