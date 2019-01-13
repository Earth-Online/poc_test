FROM webdevops/php-apache-dev:ubuntu-15.10
LABEL maintainer="linlanxi7552659@gmail.com"
COPY ./yunucms /var/www/yunucms
ENV WEB_DOCUMENT_ROOT /var/www/yunucms
RUN chown -R application:application /var/www/yunucms
