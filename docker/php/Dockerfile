FROM php:8

#WORKDIR /app

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# More extensions at https://github.com/mlocati/docker-php-extension-installer#supported-php-extensions
RUN install-php-extensions pgsql pdo_pgsql bcmath zip xdebug tidy

#RUN  apt-get update \
#  && apt-get install -y wget \
#  && rm -rf /var/lib/apt/lists/*

# Use the default development configuration
#RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
ADD ./docker/php/custom-development.php.ini "$PHP_INI_DIR/conf.d/"

#CMD ["php-fpm"]
