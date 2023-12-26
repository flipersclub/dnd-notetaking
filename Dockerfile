FROM thecodingmachine/php:8.2-v4-apache
ENV APACHE_RUN_USER=www-data \
APACHE_RUN_GROUP=www-data \
APACHE_DOCUMENT_ROOT=public/ \
PHP_EXTENSIONS="bcmath tidy ldap redis xdebug gd imagick" \
DOCKER_USER=root \
PHP_INI_XDEBUG__MODE="develop,debug" \
PHP_INI_XDEBUG__START_WITH_REQUEST="yes" \
PHP_INI_XDEBUG__DISCOVER_CLIENT_HOST="yes" \
PHP_INI_XDEBUG__IDEKEY="docker" \
PHP_INI_XDEBUG__CLIENT_HOST="host.docker.internal" \
PHP_INI_XDEBUG__CLIENT_PORT="9003" \
PHP_INI_XDEBUG__LOG="/dev/stdout" \
PHP_INI_XDEBUG__LOG_LEVEL="0"

USER root
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y mysql-client && rm -rf /var/lib/apt

COPY boot.sh /usr/local/bin/
RUN chmod 755 /usr/local/bin/boot.sh
CMD ["/usr/local/bin/boot.sh"]
