FROM registry.cesan.com.br/cesan/php:7.3-apache-pdo

COPY --chown=www-data:www-data html /var/www/html
COPY --chown=www-data:www-data vendor /var/www/vendor

RUN \
    apt-get update && \
    apt-get install libldap2-dev -y && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ && \
    docker-php-ext-install ldap

RUN sed -i -e 's/upload_max_filesize = .*/upload_max_filesize = 500M/' /usr/local/etc/php/php.ini
RUN sed -i -e 's/post_max_size = .*/post_max_size = 500M/' /usr/local/etc/php/php.ini
RUN sed -i -e 's/max_execution_time = .*/max_execution_time = 560/' /usr/local/etc/php/php.ini
RUN sed -i -e 's/max_input_time = .*/max_input_time = 560/' /usr/local/etc/php/php.ini
