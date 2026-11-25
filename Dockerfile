# Dependencias de PHP sin las de desarrollo.
FROM composer:2 AS dependencias
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

# Apache con la carpeta public como raíz; Render indica el puerto en PORT.
FROM php:8.4-apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN a2enmod rewrite headers \
 && sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf \
 && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
WORKDIR /var/www/html
COPY --from=dependencias /app /var/www/html
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache
EXPOSE 10000
CMD sed -ri "s/Listen 80/Listen ${PORT:-10000}/" /etc/apache2/ports.conf \
 && sed -ri "s/:80>/:${PORT:-10000}>/" /etc/apache2/sites-available/000-default.conf \
 && if [ -z "$APP_KEY" ]; then export APP_KEY="$(php artisan key:generate --show)"; fi  && php artisan config:cache && php artisan route:cache && php artisan view:cache \
 && apache2-foreground
