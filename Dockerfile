FROM php:8.3-apache

# Extensoes necessarias para o projeto (PDO + MySQL)
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Rewrite costuma ser util para rotas amigaveis no futuro
RUN a2enmod rewrite

WORKDIR /var/www/html
