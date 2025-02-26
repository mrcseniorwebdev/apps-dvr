# Set the base image
FROM php:8.0-apache

# Install necessary extensions and tools
RUN apt-get update && \
    apt-get install -y \
        freetds-dev \
        unixodbc \
        unixodbc-dev \
        git \
        zip \
        unzip && \
    ln -s /usr/lib/x86_64-linux-gnu/libsybdb.so /usr/lib/libsybdb.so && \
    docker-php-ext-install pdo_dblib && \
    docker-php-ext-enable pdo_dblib && \
    a2enmod rewrite headers

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable CORS by updating Apache configuration (optional)
# RUN echo '<IfModule mod_headers.c>' >> /etc/apache2/apache2.conf && \
#     echo '    Header set Access-Control-Allow-Origin "*"' >> /etc/apache2/apache2.conf && \
#     echo '    Header set Access-Control-Allow-Methods "GET,POST,OPTIONS,DELETE,PUT"' >> /etc/apache2/apache2.conf && \
#     echo '    Header set Access-Control-Allow-Headers "Content-Type, Authorization"' >> /etc/apache2/apache2.conf && \
#     echo '</IfModule>' >> /etc/apache2/apache2.conf

# Update PHP configuration to enable error reporting and output errors to stdout/stderr
RUN echo 'error_reporting = E_ALL' >> /usr/local/etc/php/php.ini && \
    echo 'display_errors = On' >> /usr/local/etc/php/php.ini && \
    echo 'log_errors = On' >> /usr/local/etc/php/php.ini && \
    echo 'error_log = /dev/stderr' >> /usr/local/etc/php/php.ini

# Copy your application code to the container
COPY ./src /var/www/html/

# Set the working directory
WORKDIR /var/www/html/

# Install PHP dependencies with Composer
RUN composer require google/apiclient:^2.0