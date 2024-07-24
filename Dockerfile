FROM php:8.2-cli

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN pecl install redis && docker-php-ext-enable redis
RUN apt-get update && apt-get install -y libmemcached-dev libssl-dev zlib1g-dev libpng-dev libzip-dev git
RUN apt-get update && apt-get install -y libc-client-dev libkrb5-dev && rm -r /var/lib/apt/lists/*
RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install imap
RUN docker-php-ext-install bcmath gd sockets zip pdo pdo_mysql

RUN cd /usr/local/etc/php/conf.d/ && \
  echo 'memory_limit = -1' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini

  
RUN apt-get update && apt-get upgrade -y && \
apt-get install -y nodejs \
npm 

COPY . /usr/src/mautic
WORKDIR /usr/src/mautic
RUN cd /usr/src/mautic

RUN composer install --prefer-dist

CMD [ "php", "-S", "0.0.0.0:8000" ]

