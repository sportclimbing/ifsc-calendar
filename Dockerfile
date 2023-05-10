FROM php:8.2.4-cli

COPY . .

RUN apt update && apt install wget git unzip -y

RUN echo "phar.readonly=0" > /usr/local/etc/php/conf.d/phar.ini
RUN mv /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

RUN make && make install

ENTRYPOINT ["/usr/local/bin/ifsc-calendar"]
