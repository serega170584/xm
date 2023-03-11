FROM php:8.2-fpm-alpine
WORKDIR "/app"

RUN apk add php-cli
RUN apk add postgresql
RUN apk add libpq-dev
RUN docker-php-ext-install pgsql
RUN docker-php-ext-install pdo_pgsql
RUN apk add rabbitmq-c-dev
RUN apk add git

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

RUN docker-php-source extract
RUN mkdir -p /usr/src/php/ext/amqp
RUN cd /usr/src/php/ext/amqp
RUN git clone https://github.com/php-amqp/php-amqp /usr/src/php/ext/amqp
RUN docker-php-ext-install amqp
RUN docker-php-source delete

RUN addgroup -g 1000 app && adduser -u 1000 -G app -s /bin/sh -D app