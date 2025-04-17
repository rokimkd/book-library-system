# PHP Alpine image
FROM php:8.4.4-alpine3.20

# Assign group and user
ENV PHPGROUP=book_library_system
ENV PHPUSER=book_library_system

RUN adduser -g ${PHPGROUP} -s /bin/sh -D ${PHPUSER}

RUN mkdir -p /var/www/html/public

# Required dependencies
RUN set -eux;
RUN apk update;
RUN apk upgrade;
RUN apk add libxml2-dev;
RUN apk add php-soap;
RUN apk add ghostscript ghostscript-fonts
RUN apk add zlib zlib-dev libxpm-dev
RUN apk add freetype libpng libjpeg-turbo
RUN apk add libgomp
RUN apk add --no-cache zip libzip-dev;

# Install 'gd' extension and Redis
RUN apk add --no-cache ${PHPIZE_DEPS} linux-headers pcre-dev freetype-dev libpng-dev libjpeg-turbo-dev libwebp-dev git openssl-dev \
    && docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp \
    NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) \
    && docker-php-ext-install -j$(nproc) gd \
    && pecl install igbinary-3.2.16 \
    && echo "extension=igbinary.so" >> /usr/local/etc/php/conf.d/igbinary.ini \
    && pecl install --onlyreqdeps --configureoptions='enable-redis-igbinary="yes"' redis-6.1.0 \
    && echo "extension=redis.so" >> /usr/local/etc/php/conf.d/redis.ini \
    && pecl install \
      --onlyreqdeps \
      --configureoptions='enable-openssl="yes"' \
      swoole-6.0.1 \
    && echo "extension=swoole.so" >> /usr/local/etc/php/conf.d/swoole.ini \
    && apk del linux-headers pcre-dev freetype-dev libpng-dev libjpeg-turbo-dev git openssl-dev ${PHPIZE_DEPS}

# Install and enable process control extension for swoole
RUN docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-install pcntl

# Enable other extensions
RUN docker-php-ext-configure zip;
RUN docker-php-ext-install pdo pdo_mysql soap zip;
RUN docker-php-ext-enable soap;
RUN docker-php-ext-enable gd;

# Install mysql-client
RUN apk add mysql-client
RUN apk add mariadb-connector-c

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

EXPOSE 8000
