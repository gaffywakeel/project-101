ARG PHP_VERSION

FROM php:${PHP_VERSION}-fpm

LABEL maintainer="NeoScrypts"

ARG APP_ENV
ARG PHP_VERSION
ARG COMPOSER_VERSION
ARG NODE_VERSION
ARG DEBIAN_FRONTEND=noninteractive

ENV TZ=UTC
ENV COMPOSER_HOME=/tmp/.composer
ENV NPM_CONFIG_LOGLEVEL=warn
ENV NPM_CONFIG_CACHE=/tmp/.npm
ENV INSTALLED_FILE=/var/app.installed

ENV APP_ENV=$APP_ENV

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update && apt-get install -y curl gnupg2 ca-certificates lsb-release debian-archive-keyring

# Setup nodesource repository
RUN curl -sS https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor \
        | tee /usr/share/keyrings/nodesource-keyring.gpg >/dev/null

RUN echo "deb [signed-by=/usr/share/keyrings/nodesource-keyring.gpg] https://deb.nodesource.com/node_$NODE_VERSION.x nodistro main" \
        | tee /etc/apt/sources.list.d/nodesource.list

RUN echo "Package: nodejs\nPin: origin deb.nodesource.com\nPin-Priority: 900" \
        | tee /etc/apt/preferences.d/nodesource

# Install system dependencies
RUN apt-get update && apt-get install -y sqlite3 cron supervisor gosu mariadb-client git nodejs

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions mysqli sqlite3 curl opcache swoole tokenizer \
    xml pcntl bcmath igbinary gd ctype redis mbstring intl \
    fileinfo json pdo pdo_mysql @composer-${COMPOSER_VERSION}

RUN if [ "$APP_ENV" = "local" -o "$APP_ENV" = "testing" ]; then \
    mv "${PHP_INI_DIR}/php.ini-development" "${PHP_INI_DIR}/php.ini"; install-php-extensions xdebug; else \
    mv "${PHP_INI_DIR}/php.ini-production" "${PHP_INI_DIR}/php.ini"; fi

RUN mkdir -p /var/scripts

COPY scripts/*.sh /var/scripts/
COPY scripts/start-container /usr/local/bin/start-container
COPY scripts/healthcheck /usr/local/bin/healthcheck
COPY conf.d/*.ini ${PHP_INI_DIR}/conf.d/
COPY supervisord.conf /var/supervisord.conf
COPY schedule.cron /var/schedule.cron

RUN useradd -ms /bin/bash server

RUN chmod -R +x /var/scripts
RUN chmod u+x /usr/local/bin/start-container
RUN chmod u+x /usr/local/bin/healthcheck
RUN chmod 0644 /var/schedule.cron
RUN chmod 0644 /var/supervisord.conf

WORKDIR /var/www/html

EXPOSE 8080

# Image cleanup
RUN apt-get -y autoremove && apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ENTRYPOINT ["start-container"]
