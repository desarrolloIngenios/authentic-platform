FROM php:8.1-fpm-alpine

# Instalar dependencias de sistema y extensiones para Moodle
RUN apk add --no-cache \
    bash \
    nginx \
    supervisor \
    curl \
    git \
    icu-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    libxml2-dev \
    oniguruma-dev \
    postgresql-dev \
    mysql-client \
    nodejs \
    npm \
    && docker-php-ext-configure gd \
        --with-freetype=/usr/include/ \
        --with-jpeg=/usr/include/ \
    && docker-php-ext-install \
        intl \
        gd \
        xml \
        zip \
        soap \
        opcache \
        mysqli \
        pdo \
        pdo_mysql \
        pdo_pgsql

# Configuración PHP recomendada para producción Moodle
COPY ./php.ini /usr/local/etc/php/php.ini

# Descargar Moodle (última versión estable 4.5)
WORKDIR /var/www/html
RUN git clone -b MOODLE_405_STABLE https://github.com/moodle/moodle.git . \
    && mkdir -p /var/www/moodledata \
    && chown -R www-data:www-data /var/www/html /var/www/moodledata

# Copiar configuración de nginx
COPY ./nginx.conf /etc/nginx/nginx.conf

# Copiar configuración Supervisor
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Exponer puertos
EXPOSE 80

# Comando de inicio
CMD ["/usr/bin/supervisord", "-n"]


