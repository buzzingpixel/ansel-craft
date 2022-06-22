FROM php:7.4.2-fpm

COPY ./.profile /root/.profile
COPY ./.profile /root/.bashrc

RUN apt-get update && \
    apt-get install -my --no-install-recommends libmagick++-dev && \
    apt-get install -my --no-install-recommends libmagickwand-dev && \
    apt-get install -my --no-install-recommends libonig-dev && \
    apt-get install -my --no-install-recommends libssl-dev && \
    apt-get install -my --no-install-recommends libwebp-dev && \
    apt-get install -my --no-install-recommends libxml2 && \
    apt-get install -my --no-install-recommends libxml2-dev && \
    apt-get install -my --no-install-recommends libxpm-dev && \
    apt-get install -my --no-install-recommends libzip-dev && \
    apt-get install -my --no-install-recommends openssl && \
    apt-get install -my --no-install-recommends unzip && \
    apt-get install -my --no-install-recommends libjpeg-dev && \
    apt-get install -my --no-install-recommends libpng-dev && \
    apt-get install -my --no-install-recommends git && \
    apt-get clean && \
    docker-php-ext-configure gd --with-jpeg --with-freetype --with-xpm --with-webp && \
    docker-php-ext-install -j$(nproc) gd && \
    docker-php-ext-install bcmath && \
    docker-php-ext-install bz2 && \
    docker-php-ext-install exif && \
    docker-php-ext-install iconv && \
    docker-php-ext-install mbstring && \
    docker-php-ext-install opcache && \
    docker-php-ext-install pdo && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install zip && \
    docker-php-ext-install mysqli && \
    pecl install imagick && \
    docker-php-ext-enable imagick && \
    pecl install xdebug-3.1.4 && \
    docker-php-ext-enable xdebug

COPY ./php74/php-custom-config.ini /usr/local/etc/php/conf.d/php-custom-config.ini
COPY ./php74/php-config-dev.ini /usr/local/etc/php/conf.d/php-env-config.ini
COPY ./php74/fpm-www.conf /usr/local/etc/php-fpm.d/www.conf

COPY --from=composer:2.3.5 /usr/bin/composer /usr/bin/composer
