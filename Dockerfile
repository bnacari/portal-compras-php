FROM registry.sistemas.cesan.com.br/library/cesan/php:7.3-apache-pdo

COPY --chown=www-data:www-data html /var/www/html
COPY --chown=www-data:www-data TCPDF-main /var/www/TCPDF-main
COPY --chown=www-data:www-data vendor /var/www/vendor

RUN \
    apt-get update && \
    apt-get install libldap2-dev -y && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ && \
    docker-php-ext-install ldap

# COPY --chown=www-data:www-data ldap.so /usr/local/lib/php/extensions/no-debug-non-zts-20180731/

# RUN echo "extension=ldap" >> /usr/local/etc/php/php.ini