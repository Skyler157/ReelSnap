FROM php:8.2-cli-alpine

WORKDIR /var/www/html

RUN apk add --no-cache bash git curl unzip libzip-dev oniguruma-dev icu-dev postgresql-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring bcmath intl zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

COPY . .

RUN chmod +x docker/start.sh

EXPOSE 10000

CMD ["sh", "docker/start.sh"]
