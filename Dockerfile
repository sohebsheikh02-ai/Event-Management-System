# FROM php:8.2-apache

# RUN apt-get update \
#     && docker-php-ext-install mysqli \
#     && a2enmod rewrite headers \
#     && rm -rf /var/lib/apt/lists/*

# WORKDIR /var/www/html

# COPY . /var/www/html
# COPY docker/start-apache.sh /usr/local/bin/start-apache.sh

# RUN chmod +x /usr/local/bin/start-apache.sh \
#     && chown -R www-data:www-data /var/www/html

# EXPOSE 10000

# CMD ["/usr/local/bin/start-apache.sh"]


FROM php:8.2-cli

WORKDIR /app

COPY . .

RUN apt-get update \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

EXPOSE 8080

CMD php -S 0.0.0.0:$PORT