# Source
# https://hub.docker.com/_/php/
# Docker File Source
# https://github.com/docker-library/php/blob/master/7.2/stretch/apache/Dockerfile

#How to build
#sudo docker build -f apache_php7.2.Dockerfile . -t bjverde/php7.2

#How use iterative mode container
#sudo docker run -it devform:7.2-deb-apache /bin/bash

#How use iterative mode image
#sudo docker run -p 80:80 -it devform:7.2-deb-apache /bin/bash
#sudo docker run -d -p 80:80 devform:7.2-deb-apache

#######################################
FROM php:7.4-apache 
LABEL maintainer="bjverde@yahoo.com.br"
#COPY ./www /var/www/html
#WORKDIR /var/www/html
EXPOSE 80

ENV DEBIAN_FRONTEND noninteractive

#PHP Modules : curl, date, dom, fileinfo, filter, ftp, hash, iconv, json, libxml, libxml, mbstring, openssl, PDO, pdo_sqlite, Phar, posix, SimpleXML

#Change PHP.INI for Desenv
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

#Install facilitators
RUN apt-get update && apt-get install -y locate mlocate curl nano wget rpl apt-utils

#Install GIT
RUN apt-get install -y git-core

#PHP Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#PHP PDO 
#RUN docker-php-ext-install pdo

#PHP PDO MySQL
#RUN docker-php-ext-install pdo_mysql mysqli

#PHP PDO PostgreSql
#RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo_pgsql

#PHP Zip
RUN apt-get install -y libzip-dev && docker-php-ext-install zip

#PHP GD
RUN apt-get install -y libpng-dev
RUN docker-php-ext-install gd

#Python 
RUN apt-get install -y python3 python3-pip
RUN python3 -m pip install --upgrade pip

## ------ Install Python Requirements ------------

COPY --chown=www-data:www-data requirements.txt /var/www/requirements.txt
RUN pip3 install -r /var/www/requirements.txt

#COPY --chown=www-data:www-data install_base_formdin.sh /var/www/install_base_formdin.sh
#RUN chmod 711 /var/www/install_base_formdin.sh
#RUN /bin/bash /var/www/install_base_formdin.sh

#COPY --chown=www-data:www-data install_base_formdin_cp.sh /var/www/install_base_formdin_cp.sh
#RUN chmod 711 /var/www/install_base_formdin_cp.sh

## ------------- Finishing ------------------
RUN apt-get clean
RUN apt-get autoremove -y

#Creating index of files
RUN updatedb