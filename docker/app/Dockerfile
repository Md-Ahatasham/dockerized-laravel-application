FROM php:8.2-fpm

LABEL maintainer="Md. Ahatasham <ahatasham.csecu@gmail.com>"

ARG HTTP_PROXY=""
ARG HTTPS_PROXY=""
ARG NO_PROXY="localhost,127.0.0.*"
ARG BUILD_MODE="prod"
ARG TIMEZONE="Asia/Dhaka"

ARG APT_DEPENDENCIES="curl git zip unzip g++ gettext libz-dev libpq-dev libssl-dev libzip-dev libicu-dev libfreetype6-dev libjpeg-dev libpng-dev libxml2-dev zlib1g-dev openssl uuid-dev vim nginx supervisor ca-certificates"
ARG APT_DEPENDENCIES_REMOVABLE="g++ libxml2-dev"
ARG PHP_DOCKER_EXTENSIONS="bcmath exif fileinfo gd gettext intl opcache pdo pdo_mysql pcntl zip"
ARG PHP_PECL_EXTENSIONS="redis uuid"

# App Root Path relative to context
ARG HOST_APP_ROOT_DIR="./codes/"
ARG WORK_DIR_PATH="/var/www/html"

# Composer vairables
ARG COMPOSER_HOME="/var/www/.composer"
ARG COMPOSER_VERSION="2.2.23"

# Proxy
ENV http_proxy="${HTTP_PROXY}" \
    https_proxy="${HTTPS_PROXY}" \
    no_proxy="${NO_PROXY}"

USER root

RUN echo "-- Install Dependencies --" \
        && apt-get update -y \
        && apt-get install -y --no-install-recommends ${APT_DEPENDENCIES} \
    && echo "-- Install PHP Extensions --" \
        && if [ ! -z "${HTTP_PROXY}" ]; then \
            pear config-set http_proxy "${HTTP_PROXY}" \
        ;fi \
        && pear update-channels \
        && docker-php-ext-configure gd \
        && docker-php-ext-configure intl \
        && docker-php-ext-configure zip \
        && docker-php-ext-install -j$(nproc) ${PHP_DOCKER_EXTENSIONS} \
        && pecl install ${PHP_PECL_EXTENSIONS} \
        && docker-php-ext-enable ${PHP_PECL_EXTENSIONS} \
        && pear clear-cache \
    && echo "-- Cleanup Junks --" \
        && apt-get autoremove -y ${APT_DEPENDENCIES_REMOVABLE} \
        && apt-get clean -y \
        && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && echo "-- Symlink creating --" \
        && ln -sf /dev/stdout /var/log/nginx/access.log \
        && ln -sf /dev/stderr /var/log/nginx/error.log


RUN curl -L -o /usr/local/bin/composer https://github.com/composer/composer/releases/download/${COMPOSER_VERSION}/composer.phar \
    && mkdir -p ${COMPOSER_HOME} \
    && mkdir /run/php \
    && chown -R www-data:www-data ${COMPOSER_HOME} /run/php \
    && chmod -R ugo+w ${COMPOSER_HOME} \
    && chmod -R g+s ${COMPOSER_HOME} \
    && chmod ugo+x /usr/local/bin/composer \
    && composer --version

COPY ./docker/app/php.ini /usr/local/etc/php/conf.d/app-php.ini

WORKDIR ${WORK_DIR_PATH}

# Composer Packages Installation
COPY --chown=www-data:www-data ${HOST_APP_ROOT_DIR}composer.* ${WORK_DIR_PATH}/

USER www-data
# Composer DumpAutoload
COPY --chown=www-data:www-data ${HOST_APP_ROOT_DIR} ${WORK_DIR_PATH}

RUN chmod -R ug+w bootstrap/ storage/

# ---------- #
# WEB - NGINX
# ---------- #

# Web
ENV NGINX_VHOST_ENABLE_HTTP_TRAFFIC="true" \
    NGINX_VHOST_ENABLE_HTTPS_TRAFFIC="true" \
    NGINX_VHOST_SERVER_NAME="_" \
    NGINX_VHOST_HTTP_SERVER_NAME="_" \
    NGINX_VHOST_CLIENT_MAX_BODY_SIZE="2m" \
    NGINX_VHOST_SSL_CERTIFICATE="/etc/ssl/certs/ssl-cert-snakeoil.pem" \
    NGINX_VHOST_SSL_CERTIFICATE_KEY="/etc/ssl/certs/ssl-cert-snakeoil.key"

COPY --chown=root:root ./docker/app/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY --chown=root:root ./docker/app/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY --chown=root:root ./docker/web/includes/*.conf /etc/nginx/includes/
COPY --chown=root:root ./docker/web/nginx.conf /etc/nginx/nginx.conf

COPY --chown=root:root ./docker/web/conf.d/vhost-http.conf /etc/nginx/conf.d/default.conf
COPY --chown=root:root ./docker/web/conf.d/vhost-https.conf /etc/nginx/conf.d/default-ssl.conf

COPY --chown=root:root ./docker/.commons/certs/* /etc/ssl/certs/
COPY --chown=www-data:www-data ./docker/app/docker-entrypoint.sh /usr/local/bin/entrypoint.sh

USER root

#WORKDIR ${WORK_DIR_PATH}

# --------- #
# SUPERVISOR
# --------- #
COPY ./docker/supervisor/supervisord.conf /etc/supervisor/
COPY ./docker/supervisor/conf.d/* /etc/supervisor/conf.d/


RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 3080 443

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
