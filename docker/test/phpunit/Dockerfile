## build context is at docker/
FROM php:8.1-cli-alpine

# install dependencies
RUN set -ex; \
    apk add --no-cache --virtual \
    .build-deps \
    $PHPIZE_DEPS \
	bash

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN chmod uga+x /usr/local/bin/install-php-extensions && \
    sync && \
    install-php-extensions \
    apcu \
    iconv \
    intl \
    xdebug

# modify xdebug.ini to reflect php version (replaced by the 'docker-php-ext-enable imagick' command, but still
# a pretty nice command to my mind)
COPY ./xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN set -ex; \
    find /usr/local/lib/php/extensions -name "no-debug-non-zts-*" | \
    xargs basename | \
    xargs -I '{}' sed -i 's/{folder_version}/{}/g' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# install composer (needed for PHPUnit updates)
RUN set -ex; \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

VOLUME /var/www/html
WORKDIR /var/www/html
