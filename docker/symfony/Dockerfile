FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
        git \
        unzip \
        icu-dev \
        oniguruma-dev \
        libzip-dev \
        bash \
        $PHPIZE_DEPS \
    && docker-php-ext-install \
        pdo_mysql \
        intl \
        opcache \
        zip \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/tmp/composer

WORKDIR /var/www/symfony

EXPOSE 9000
CMD ["php-fpm", "-F"]
