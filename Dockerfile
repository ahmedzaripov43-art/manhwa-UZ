FROM php:8.1-apache

RUN a2enmod rewrite
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY . .

EXPOSE 80

CMD ["apache2-foreground"]