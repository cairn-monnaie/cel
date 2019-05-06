FROM php:7.2-fpm

ARG xdebug_enabled=false
ARG xdebug_remote_host=localhost
ARG xdebug_remote_port=9001

LABEL description="Cairn App" \
        maintainer="mazda91 <https://github.com/mazda91>"

RUN apt-get -y update \
    && apt-get install -y git vim curl \
    && apt-get install -y python3.5 python3-pip \
    && apt-get install -y libgmp-dev zlib1g-dev \
    && ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/local/include/ \
    && docker-php-ext-configure gmp \ 
    && pip3 install python-slugify PyYAML datetime requests \
    && docker-php-ext-install gmp pdo pdo_mysql zip \
    && apt-get install -y libxrender1 libfontconfig1

RUN if [ "$xdebug_enabled" = "true" ] ; then echo 'Install xdebug' \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_enable=On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini  \
    && echo "xdebug.remote_autostart=On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini  \
    && echo "xdebug.remote_connect_back=off" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini  \
    && echo "xdebug.remote_port=$xdebug_remote_port" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_host=$xdebug_remote_host" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini ;  fi

COPY . /var/www/Symfony

RUN cd /tmp \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --install-dir=/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

RUN cd /var/www/Symfony \
    && composer install \
    && php bin/console cache:clear --env=dev \
    && echo "chmod -R www-data:www-data /var/www/Symfony" #\
#    && chown -R www-data:www-data /var/www/Symfony

WORKDIR /var/www/Symfony

CMD ["php-fpm"]
