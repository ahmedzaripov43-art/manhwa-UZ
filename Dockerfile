FROM php:8.1-apache

RUN a2enmod rewrite
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's|<Directory /var/www/>|<Directory /var/www/html/>|g' /etc/apache2/apache2.conf

ENV APACHE_DOCUMENT_ROOT /var/www/html

WORKDIR /var/www/html

COPY . /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]