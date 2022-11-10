FROM php:8.0-apache
RUN set -eux \
	&& apt-get update -y \
	&& apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        exiftool \
        ffmpeg \
        jq \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libpq-dev \
        libxml2-dev \
        libzip-dev \
        mariadb-client \
        xmlstarlet \
        zip \
    && docker-php-ext-install -j$(nproc) soap \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-configure exif \
    && docker-php-ext-install -j$(nproc) exif \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) zip \
    && docker-php-ext-install -j$(nproc) sockets \
    && docker-php-ext-install -j$(nproc) mysqli \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql \
    && docker-php-ext-enable opcache \
    && a2enmod rewrite \
    && a2enmod headers \
    && ln -s $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini \
    && sed -i 's/display_errors = .*/display_errors = Off/' $PHP_INI_DIR/php.ini

RUN mkdir /var/cache/esrender/ && chmod -R 777 /var/cache/esrender/
