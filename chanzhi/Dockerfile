FROM webdevops/php-apache-dev
LABEL version="0.1"
RUN apt-get update&&apt-get install -y make unzip&&git clone https://gitee.com/wwccss/chanzhieps /tmp/app 
WORKDIR  /tmp/app
RUN  make && unzip chanzhi*.zip && mv chanzhieps /app  && mkdir -p /app/chanzhieps/www/data/ &&  chmod -R 777 /app && rm -rf /tmp/app
ENV WEB_DOCUMENT_ROOT /app/chanzhieps/www



