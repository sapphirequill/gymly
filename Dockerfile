FROM mlocati/php-extension-installer:2.9.11 AS php-extension-installer
FROM php:8.4-cli-alpine3.22 AS php-base

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /var/www/.composer

COPY --from=php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=ghcr.io/roadrunner-server/roadrunner:2025.1.4 /usr/bin/rr /usr/local/bin/rr

SHELL ["/bin/ash", "-eo", "pipefail", "-c"]

RUN install-php-extensions @composer-2 bcmath ds decimal intl pdo pdo_pgsql sockets xdebug zip

EXPOSE 80

CMD ["rr", "serve", "--config", "/app/.rr.yaml"]

FROM php-base as php-dev

RUN install-php-extensions xdebug

COPY .docker/php/development.ini /usr/local/etc/php/conf.d/zz-php.ini
COPY . .
