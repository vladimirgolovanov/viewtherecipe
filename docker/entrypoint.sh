#!/bin/sh
set -e

php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
php bin/console cache:warmup --env=prod
chown -R www-data:www-data var/cache var/log var/share

exec supervisord -c /etc/supervisord.conf
