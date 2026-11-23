# ---- Tahap 1: install dependency PHP ----
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-scripts --no-progress \
    --optimize-autoloader --ignore-platform-reqs

# ---- Tahap 2: image aplikasi ----
FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libicu-dev \
    && docker-php-ext-install intl mysqli \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Pastikan hanya satu MPM (prefork) yang aktif, wajib untuk mod_php.
RUN rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* \
    && a2enmod mpm_prefork

# Document root mengarah ke public/ dan .htaccess diizinkan
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf \
    && sed -ri "s!/var/www/!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && printf '<Directory %s>\n    AllowOverride All\n    Require all granted\n</Directory>\n' "${APACHE_DOCUMENT_ROOT}" \
       > /etc/apache2/conf-available/ci4.conf \
    && a2enconf ci4

WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN mkdir -p writable/cache writable/logs writable/session writable/uploads writable/debugbar \
    && chown -R www-data:www-data writable \
    && sed -i 's/\r$//' docker/start.sh \
    && chmod +x docker/start.sh

ENV CI_ENVIRONMENT=production
EXPOSE 8080
CMD ["docker/start.sh"]
