FROM php:8.2-fpm

COPY docker/php/php82/config/php.ini /usr/local/etc/php/

# Ensure the session directory exists and has correct permissions
RUN mkdir -p /var/lib/php/sessions \
    && chown -R www-data:www-data /var/lib/php/sessions \
    && chmod -R 770 /var/lib/php/sessions

RUN apt-get update && apt-get install -y \
        apt-transport-https \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libmcrypt-dev \
		libpng-dev \
		libxml2-dev \
		libicu-dev \
		libzip-dev \
		libldap-common \
		libldap2-dev \
		locales \
		gnupg2 \
		vim \
		git \
		cron \
		tzdata \
		default-mysql-client \
	&& docker-php-ext-install -j$(nproc) \
	    zip \
	    intl \
	    soap \
	    opcache \
	    pdo_mysql \
	    mysqli \
	    ldap \
	# https://github.com/docker-library/php/issues/912
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-configure ldap --with-libdir=lib/$(uname -m)-linux-gnu/ \
	&& docker-php-ext-install -j$(nproc) gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

SHELL ["/bin/bash", "--login", "-c"]

ENV NODE_VERSION 18.7

RUN cd ~ \
	&& curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | bash \
	&& . $HOME/.nvm/nvm.sh \
	&& nvm install $NODE_VERSION \
	&& nvm alias default $NODE_VERSION \
    && nvm use default \
	&& npm install --global yarn

RUN nvm -v \
	&& node --version \
	&& yarn --version

RUN echo "${TZ}" > /etc/timezone \
    && dpkg-reconfigure --frontend noninteractive tzdata

WORKDIR /var/www

COPY . .

RUN cd /var/www/src/ && composer install

RUN mkdir -p /var/www/src/log \
    && chown -R www-data:www-data /var/www/src/log \
    && chmod -R 775 /var/www/src/log

RUN mkdir -p /var/www/src/cache \
    && chown -R www-data:www-data /var/www/src/cache \
    && chmod -R 775 /var/www/src/cache