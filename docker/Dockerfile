FROM composer:2 AS deps
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-amqp

FROM php:8.5-fpm-alpine

RUN apk add --no-cache \
    nginx \
    supervisor \
    icu-libs \
    icu-dev \
    libpq \
    postgresql-dev \
    rabbitmq-c \
    rabbitmq-c-dev \
    libzip \
    libzip-dev \
    git \
    $PHPIZE_DEPS \
    && docker-php-ext-install pdo_pgsql intl zip \
    && apk del icu-dev postgresql-dev libzip-dev

RUN git clone https://github.com/php-amqp/php-amqp.git /tmp/php-amqp \
    && cd /tmp/php-amqp \
    && git fetch origin pull/595/head \
    && git checkout FETCH_HEAD \
    && phpize && ./configure && make && make install \
    && docker-php-ext-enable amqp \
    && rm -rf /tmp/php-amqp \
    && apk del rabbitmq-c-dev git

WORKDIR /var/www/html

COPY . .
COPY --from=deps /app/vendor ./vendor

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh \
    && mkdir -p var/cache var/log \
    && chown -R www-data:www-data var/

EXPOSE 11456

ENTRYPOINT ["/entrypoint.sh"]
