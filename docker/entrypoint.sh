#!/bin/sh
set -e

#php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
php bin/console cache:warmup --env=prod

exec supervisord -c /etc/supervisord.conf
