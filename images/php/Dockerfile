FROM php:7.3-fpm

RUN apt-get update && apt-get install -y \
        libzip-dev \
        zip \
        sox \
        iproute2 \
	&& docker-php-ext-configure zip --with-libzip \
	&& docker-php-ext-install zip \
	&& docker-php-ext-install mysqli

WORKDIR /usr/share/nginx/html
ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini
EXPOSE 9000

CMD ["php-fpm"]