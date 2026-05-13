FROM php:8.1-apache

# mod_rewrite yoqish
RUN a2enmod rewrite

# Apache config - AllowOverride All
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/custom.conf \
    && a2enconf custom

# Fayllarni ko'chirish
COPY . /var/www/html/

# Tekshirish
RUN ls -la /var/www/html/data/comics.json

# Ruxsatlar
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
