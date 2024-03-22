FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libxml2-dev \
    libpng-dev \
    libzip-dev \
    unzip \
    nano \
    net-tools \
    iputils-ping \
    dnsutils \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install xml gd zip pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN a2enmod rewrite

ADD conf/custom.conf /etc/apache2/conf-enabled/custom.conf
ADD conf/000-default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

COPY . .
COPY .env.example /var/www/html/.env

RUN composer update
RUN composer install
RUN composer update

RUN php artisan key:generate

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

RUN chmod -R 777 storage/app
# EXPOSE 8000

# CMD php artisan serve --port=8000
CMD ["apachectl", "-D", "FOREGROUND"]
EXPOSE 80
