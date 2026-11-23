FROM node:24-bookworm-slim AS assets
WORKDIR /build
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
RUN npm run build

FROM php:8.3-apache-bookworm AS runtime
RUN apt-get update && apt-get install -y --no-install-recommends git unzip libzip-dev libonig-dev libicu-dev libsqlite3-dev \
    && docker-php-ext-install pdo_mysql pdo_sqlite mbstring zip intl bcmath \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-scripts --no-autoloader --no-interaction --no-progress
COPY . .
COPY --from=assets /build/public/build ./public/build
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && composer dump-autoload --optimize --no-dev --no-interaction \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && printf '<Directory /var/www/html/public>\nAllowOverride All\nRequire all granted\n</Directory>\n' > /etc/apache2/conf-available/eje.conf \
    && a2enconf eje \
    && chmod +x scripts/docker-entrypoint.sh
ARG VCS_REF=local
ENV VCS_REF=$VCS_REF APP_ENV=production APP_DEBUG=false LOG_CHANNEL=stderr SESSION_DRIVER=database CACHE_STORE=database QUEUE_CONNECTION=sync
LABEL org.opencontainers.image.source="https://github.com/patata-ni/eje-del-golfo-devops"
LABEL org.opencontainers.image.revision=$VCS_REF
RUN php scripts/build-manifest.php
EXPOSE 8080
CMD ["sh", "scripts/docker-entrypoint.sh"]
