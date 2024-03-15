FROM php:8.3.0-cli-alpine

COPY . .
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apk update && apk add wget git unzip make

RUN echo "phar.readonly=0" > /usr/local/etc/php/conf.d/phar.ini
RUN mv /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

RUN make && make install

ENTRYPOINT ["/usr/local/bin/ifsc-calendar"]
