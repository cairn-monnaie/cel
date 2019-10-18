FROM php:7.2-fpm

ARG xdebug_enabled=true

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
    && docker-php-ext-enable xdebug ; fi

COPY . /var/www/Symfony

RUN cd /tmp \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === 'a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --install-dir=/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

RUN cd /var/www/Symfony \
    && composer install \
    && php bin/console cache:clear --env=dev \
    && echo "chmod -R www-data:www-data /var/www/Symfony" #\
    && chown -R www-data:www-data /var/www/Symfony

WORKDIR /var/www/Symfony

CMD ["php-fpm"]
