FROM php:8.2.4-cli

COPY . .

RUN echo "phar.readonly=0" > /usr/local/etc/php/conf.d/phar.ini
RUN make && make install

ENTRYPOINT ["/usr/local/bin/ifsc-calendar"]
