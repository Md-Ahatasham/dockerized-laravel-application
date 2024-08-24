#!/usr/bin/env bash

# NGINX ENTRYPOINT CONFIGURATION
set -e

vhosts=( "default.conf" "default-ssl.conf" )
for vhost in "${vhosts[@]}"
do
    sed -i "s/\${NGINX_VHOST_SERVER_NAME}/${NGINX_VHOST_SERVER_NAME}/" /etc/nginx/conf.d/$vhost
    sed -i "s/\${NGINX_VHOST_HTTP_SERVER_NAME}/${NGINX_VHOST_HTTP_SERVER_NAME}/" /etc/nginx/conf.d/$vhost
    sed -i "s/\${NGINX_VHOST_CLIENT_MAX_BODY_SIZE}/${NGINX_VHOST_CLIENT_MAX_BODY_SIZE}/" /etc/nginx/conf.d/$vhost

    sed -i "s!\${NGINX_VHOST_SSL_CERTIFICATE}!${NGINX_VHOST_SSL_CERTIFICATE}!" /etc/nginx/conf.d/$vhost
    sed -i "s!\${NGINX_VHOST_SSL_CERTIFICATE_KEY}!${NGINX_VHOST_SSL_CERTIFICATE_KEY}!" /etc/nginx/conf.d/$vhost
done

if [ "${NGINX_VHOST_ENABLE_HTTP_TRAFFIC}" = "false" ]; then
    rm -f /etc/nginx/conf.d/default.conf
fi

if [ "${NGINX_VHOST_ENABLE_HTTPS_TRAFFIC}" = "false" ]; then
    rm -f /etc/nginx/conf.d/default-ssl.conf
fi

# PHP FPM ENTRYPOINT CONFIGURATION
# Run our defined exec if args empty
if [ -z "$1" ]; then
    role=${CONTAINER_ROLE:-app}
    env=${APP_ENV:-production}

    echo "Role ::> $role"
    echo "App Env ::> $env"

#    if [ "$env" != "local" ]; then
#
#        echo "Caching configuration..."
#        (cd /var/www/html && php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear)
#        (cd /var/www/html && php artisan config:cache && php artisan event:cache && php artisan route:cache && php artisan view:cache)
#
#    fi

    if [ "$role" = "app" ]; then

        echo "Running PHP-FPM via Supervisor..."
        exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf
        # exec php-fpm

    elif [ "$role" = "queue" ]; then

        echo "Running Queue via Supervisor..."
        exec php /var/www/html/artisan queue:work -vv --no-interaction --tries=3 --sleep=5 --timeout=300 --delay=10

    else

        echo "Could not match the container role \"$role\""
        exit 1
    fi

else
    exec "$@"
fi
