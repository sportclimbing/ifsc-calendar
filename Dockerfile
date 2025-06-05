FROM php:8.3.4-cli-alpine AS builder

WORKDIR /calendar

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV APP_DEBUG 0
ENV APP_ENV prod

COPY . .

RUN apk update && apk add --no-cache wget git unzip make

RUN echo "phar.readonly=0" > /usr/local/etc/php/conf.d/phar.ini && \
    mv /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini && \
    make clean && make

FROM php:8.3.4-cli-alpine

WORKDIR /calendar
VOLUME /calendar/

ENV APP_DEBUG 0
ENV APP_ENV prod

RUN apk update && \
    apk add --no-cache poppler-utils && \
    mv /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

COPY --from=builder /calendar/build/ifsc-calendar.phar /bin/ifsc-calendar

CMD ifsc-calendar
