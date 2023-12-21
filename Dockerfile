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

# Aumentar o tamanho do upload_max_filesize para 100MB
RUN sed -i -e 's/upload_max_filesize = .*/upload_max_filesize = 120M/' /usr/local/etc/php/php.ini
RUN sed -i -e 's/post_max_size = .*/post_max_size = 120M/' /usr/local/etc/php/php.ini
RUN sed -i -e 's/max_execution_time = .*/max_execution_time = 360/' /usr/local/etc/php/php.ini
RUN sed -i -e 's/max_input_time = .*/max_input_time = 360/' /usr/local/etc/php/php.ini

# Reiniciar o servidor Apache para aplicar as alterações
# RUN service apache2 restart
# COPY --chown=www-data:www-data ldap.so /usr/local/lib/php/extensions/no-debug-non-zts-20180731/

# RUN echo "extension=ldap" >> /usr/local/etc/php/php.ini