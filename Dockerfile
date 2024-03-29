FROM php:8.3.0-cli-alpine

WORKDIR /calendar

COPY app/run.php app/
COPY bin/console bin/
COPY bin/create-phar bin/
COPY build/*.sh build/
COPY config/autoload_runtime.template config/
COPY config/ .
COPY src/ .
COPY vendor/ .
COPY Makefile .
COPY composer* .

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apk update && apk add wget git unzip make poppler-utils

RUN echo "phar.readonly=0" > /usr/local/etc/php/conf.d/phar.ini && \
    mv /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini && \
    make clean && make && make install

# ENTRYPOINT ["sh"]
ENTRYPOINT ["/usr/local/bin/ifsc-calendar"]
