.DEFAULT_GOAL := build-phar
.PHONY: clean

install-composer:
	sh build/install-composer.sh

build-phar: install-composer
	build/composer.phar install --no-dev --optimize-autoloader --ignore-platform-req=ext-gd
	build/composer.phar remove sportclimbing/ifsc-youtube-videos
	build/composer.phar require sportclimbing/ifsc-youtube-videos --ignore-platform-req=ext-gd
	bin/create-phar build/ifsc-calendar.phar
	chmod u+x build/ifsc-calendar.phar

dev: install-composer
	build/composer.phar install --dev --quiet --ignore-platform-req=ext-gd
	bin/create-phar build/ifsc-calendar.phar
	chmod u+x build/ifsc-calendar.phar

docker:
	docker build --tag ifsc-calendar . --no-cache

test: install-composer
	build/composer.phar install --dev --quiet --ignore-platform-req=ext-gd
	vendor/bin/phpunit

install:
	cp build/ifsc-calendar.phar /bin/ifsc-calendar

clean:
	rm -f build/ifsc-calendar.phar

shell:
	docker run -it --tty -v ".:/app" php:8.3.4-cli-alpine sh