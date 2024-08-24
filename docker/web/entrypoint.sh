#!/bin/bash

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

# sed -i "s/\${NGINX_VHOST_DNS_RESOLVER_IP}/${NGINX_VHOST_DNS_RESOLVER_IP}/" /etc/nginx/includes/loc-phpfpm.conf

# sed -i "s/\${NGINX_VHOST_UPSTREAM_APP_SERVICE_HOST_PORT}/${NGINX_VHOST_UPSTREAM_APP_SERVICE_HOST_PORT}/" /etc/nginx/includes/loc-phpfpm.conf


if [ "${NGINX_VHOST_ENABLE_HTTP_TRAFFIC}" = "false" ]; then
    rm -f /etc/nginx/conf.d/default.conf
fi

if [ "${NGINX_VHOST_ENABLE_HTTPS_TRAFFIC}" = "false" ]; then
    rm -f /etc/nginx/conf.d/default-ssl.conf
fi

exec "$@"
