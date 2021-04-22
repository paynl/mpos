FROM thecodingmachine/php:7.2-v2-apache

ENV PHP_EXTENSION_EXIF=1
ENV PHP_EXTENSION_GD=1
ENV PHP_EXTENSION_INTL=1
ENV PHP_EXTENSION_XMLRPC=1
ENV PHP_EXTENSION_XSL=1
ENV PHP_EXTENSION_YAML=1
ENV PHP_EXTENSION_MEMCACHED=1
ENV TEMPLATE_PHP_INI=production
ENV APACHE_DOCUMENT_ROOT=public/

ENV APACHE_EXTENSIONS='remoteip, access_compat, alias, auth_basic, authn_core, authn_file, authz_core, authz_host, authz_user, autoindex, deflate, dir, env, filter, mime, mpm_prefork, negotiation, php7, reqtimeout, rewrite, setenvif, status, socache_shmcb, ssl, headers'

COPY 000-default.conf /etc/apache2/sites-enabled/

ENV APACHE_LOG_DIR="/var/www/html"