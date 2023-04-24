FROM php:8.2.4-cli

RUN echo "phar.readonly=0" > /usr/local/etc/php/conf.d/phar.ini

COPY . .
RUN make && make install

ENTRYPOINT ["/usr/local/bin/ifsc-calendar"]
