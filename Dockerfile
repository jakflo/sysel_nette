FROM php:8.4.22-apache-trixie

WORKDIR /var/www/
ENV APACHE_DOCUMENT_ROOT=/var/www/
ENV ACCEPT_EULA=Y
ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y git unzip libpq-dev libzip-dev \ 
&& docker-php-ext-install pdo pdo_mysql pdo_pgsql zip \ 
&& apt-get clean \ 
&& rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN --mount=type=bind,source=.,target=/var/www/

COPY . /var/www/

RUN composer update && composer install
RUN mkdir -m 777 log && mkdir -m 777 temp && mkdir -m 777 tempForTests

EXPOSE 80
CMD ["php", "-S", "[::]:80", "-t", "www"]