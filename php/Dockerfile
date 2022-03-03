FROM php:8.0-fpm

RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip -y apt-transport-https \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && pecl install redis \
    && docker-php-ext-enable redis
    
WORKDIR /home/bubu/praetorianExam

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony
RUN git config --global user.email "nexezhood@gmail.com" \ 
    && git config --global user.name "nexezzhood"

RUN apt-get install -y wget libhiredis-dev
RUN wget https://github.com/nrk/phpiredis/archive/v1.1.zip \
    && ( \
        unzip v1.1.zip \
        && cd phpiredis-1.1 \
        && phpize \
        && ./configure --enable-phpiredis --with-hiredis-dir \
        && make \
        && make install \
    ) \
    && rm -r phpiredis-1.1

RUN docker-php-ext-enable phpiredis
    
    
