FROM debian:latest
RUN apt-get update && apt-get install -y git libxaw7-dev libpcre3-dev build-essential libdb-dev libopendmarc-dev python-setuptools default-libmysqlclient-dev libspf2-dev libsasl2-dev libldap2-dev libdkim-dev libgnutls28-dev pkg-config libidn11-dev libpam-dev && \
    cd /opt && git clone -b 'exim-4.90.0.27' https://github.com/exim/exim && mkdir -p exim/src/Local && useradd exim4 
ADD Makefile /opt/exim/src/Local
ADD eximon.conf /opt/exim/src/Local
ADD exim.conf /etc/exim.conf
RUN cd /opt/exim/src && make && make install && mkdir -p /var/spool/exim && mkdir -p /usr/lib/exim/lookups 
VOLUME /var/spool/sa-exim
EXPOSE 25 465 587
CMD ["bash"]

