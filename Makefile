.DEFAULT_GOAL := build-phar
.PHONY: clean

install-composer:
	sh build/install-composer.sh

build-phar: install-composer
	build/composer.phar install --no-dev --optimize-autoloader
	bin/create-phar build/ifsc-calendar.phar
	chmod u+x build/ifsc-calendar.phar

dev: install-composer
	build/composer.phar install --dev
	bin/create-phar build/ifsc-calendar.phar
	chmod u+x build/ifsc-calendar.phar

test: install-composer
	build/composer.phar install --dev
	vendor/bin/phpunit

install:
	cp build/ifsc-calendar.phar /usr/local/bin/ifsc-calendar

clean:
	rm build/ifsc-calendar.phar