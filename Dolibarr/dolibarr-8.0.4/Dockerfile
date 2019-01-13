FROM webdevops/php-apache-dev:ubuntu-15.10
LABEL maintainer="linlanxi7552659@gmail.com"
COPY ./uploads /var/www/dolibarr
ENV WEB_DOCUMENT_ROOT /var/www/dolibarr
RUN chown -R application:application /var/www/dolibarr
