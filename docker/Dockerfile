FROM composer:2 AS deps
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

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
    && docker-php-ext-install pdo_pgsql intl opcache zip \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && apk del icu-dev postgresql-dev rabbitmq-c-dev libzip-dev

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
