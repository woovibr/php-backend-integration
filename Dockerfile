FROM php:8.1-cli

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
