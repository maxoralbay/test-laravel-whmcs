#
#--------------------------------------------------------------------------
# Image Setup
#--------------------------------------------------------------------------
#

FROM php:8.1-fpm

# Set Environment Variables
ENV COMPOSER_ALLOW_SUPERUSER="1"
ENV DEBIAN_FRONTEND noninteractive

#
#--------------------------------------------------------------------------
# Software's Installation
#--------------------------------------------------------------------------
#
# Installing tools and PHP extentions using "apt", "docker-php", "pecl",
#

# Install "curl", "libmemcached-dev", "libpq-dev", "libjpeg-dev",
#         "libpng-dev", "libfreetype6-dev", "libssl-dev", "libmcrypt-dev",
RUN set -eux; \
    apt-get update; \
    # apt-get upgrade -y; \
    apt-get install -y --no-install-recommends \
            curl \
            libmemcached-dev \
            libz-dev \
            libpq-dev \
            libjpeg-dev \
            libpng-dev \
            libfreetype6-dev \
            libssl-dev \
            libwebp-dev \
            libxpm-dev \
            libmcrypt-dev \
            libonig-dev \
            libzip-dev; \
            # zip \
    rm -rf /var/lib/apt/lists/*
# install nodejs
RUN curl -fsSL https://deb.nodesource.com/setup_18.x |  bash - \
    && apt install -y nodejs
RUN set -eux; \
    # Install the PHP pdo_mysql extention
    docker-php-ext-install pdo_mysql pdo_pgsql exif gd zip; \
    docker-php-ext-configure exif --enable-exif > /dev/null; \
    docker-php-ext-configure gd > /dev/null;


COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY ./docker/entrypoint.sh /etc/entrypoint.sh
COPY ./backend /app
RUN apt install micro -y
ENV DB_CONNECTION pgsql

WORKDIR /app
# Laravel dir and file permissions define
RUN chown -R root:www-data /app/storage
RUN chown -R root:www-data /app/bootstrap/cache
RUN chmod -R 777 /app/storage
RUN chmod -R 777 /app/bootstrap
# Link storage to public
#RUN php artisan storage:link
RUN set -e && composer install -n --no-ansi
RUN chmod +x /etc/entrypoint.sh
# RUN chmod +x /docker-entrypoint.sh


EXPOSE 5003

ENTRYPOINT ["/etc/entrypoint.sh"]