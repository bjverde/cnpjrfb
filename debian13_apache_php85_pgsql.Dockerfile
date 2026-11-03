# Source
# https://hub.docker.com/_/php/
# Docker File Source
# https://hub.docker.com/_/debian

#How to build
#sudo docker build -f debian13_apache_php85_pgsql.Dockerfile . -t debian13_apache_php85_pgsql

#How use iterative mode
#sudo docker exec -it debian13_apache_php85_pgsql:last /bin/bash

#How use iterative mode image
#sudo docker run -it debian13_apache_php85_pgsql:last /bin/bash           #only bash
#sudo docker run -p 80:80 -it debian13_apache_php85_pgsql:last /bin/bash
#sudo docker run -d -p 80:80 debian13_apache_php85_pgsql:last

#Stop all containers
#sudo docker stop $(sudo docker ps -a -q)

#Remove all containers
#sudo docker rm $(sudo docker ps -a -q)

#######################################
FROM debian:13
LABEL maintainer="bjverde@yahoo.com.br"

#Uso exclusivo do MPDFT, para agilizar com uso interno no MPDFT
#RUN export http_proxy="http://ss-aptcacher.mpdft.mp.br:3142"

ENV DEBIAN_FRONTEND noninteractive

# Set default environment variables
ENV TIMEZONE America/Sao_Paulo

#Install update
RUN apt-get update
RUN apt-get upgrade -y

#Install facilitators
RUN apt-get -y install plocate wget apt-utils curl apt-transport-https lsb-release \
    ca-certificates zip unzip vim rpl gnupg

## ------------- Install Apache2 + PHP 8.5  x86_64 ------------------
#Thread Safety 	disabled 
#PHP Modules : calendar,Core,ctype,date,exif,fileinfo,filter,ftp,gettext,hash,iconv,json,libxml
#PHP Modules : ,openssl,pcntl,pcre,PDO,Phar,posix,readline,Reflection,session,shmop,sockets,SPL,standard
#PHP Modules : ,sysvmsg,sysvsem,sysvshm,tokenizer,Zend OPcache,zlib

RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list

#Install update
RUN apt-get update


# Set Timezone
RUN ln -fs /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && apt-get update \
    && apt-get install -y --no-install-recommends tzdata \
    && dpkg-reconfigure --frontend noninteractive tzdata

#intall Apache + PHP
RUN apt-get -y install apache2 libapache2-mod-php8.5 php8.5 php8.5-cli php8.5-common

#PHP Install CURl
RUN apt-get -y install curl php8.5-curl

#PHP Intall DOM, Json, XML e Zip
RUN apt-get -y install php8.5-dom php8.5-xml php8.5-zip php8.5-soap php8.5-intl php8.5-xsl

#PHP Install MbString
RUN apt-get -y install php8.5-mbstring

#PHP Install GD
RUN apt-get -y install php8.5-gd

#PHP Install PDO SqLite
RUN apt-get -y install php8.5-pdo php8.5-pdo-sqlite php8.5-sqlite3

#PHP Install PDO MySQL
RUN apt-get -y install php8.5-pdo php8.5-pdo-mysql php8.5-mysql 

#PHP Install PDO PostGress
RUN apt-get -y install php8.5-pdo php8.5-pgsql

## -------- Config Apache ----------------
RUN a2dismod mpm_event
RUN a2dismod mpm_worker
RUN a2enmod  mpm_prefork
RUN a2enmod  rewrite
RUN a2enmod  php8.5

# Enable .htaccess reading
RUN LANG="en_US.UTF-8" rpl "AllowOverride None" "AllowOverride All" /etc/apache2/apache2.conf

## ------------- LDAP ------------------
#PHP Install LDAP
RUN apt-get -y install php8.5-ldap

#Apache2 enebla LDAP
RUN a2enmod authnz_ldap
RUN a2enmod ldap

## ------------- Add-ons ------------------
#Install GIT
RUN apt-get -y install -y git-core

#PHP Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#PHP Install PHPUnit
#https://phpunit.de/announcements/phpunit-13.html
RUN wget -O /usr/local/bin/phpunit-13.phar https://phar.phpunit.de/phpunit-13.phar; chmod +x /usr/local/bin/phpunit-13.phar; \
    ln -s /usr/local/bin/phpunit-13.phar /usr/local/bin/phpunit

## ------------- X-DEBUG 3.X ------------------
#PHP Install X-debug (configurado e controlado via volume em ./config/xdebug.ini)
RUN apt-get -y install php8.5-xdebug


##------------ Install Precondition for Drive SQL Server -----------
# The installation of Drive SQL Server for PHP on Linux is not so simple.
# You should combine the PHP version with Drive PDO version with the ODBC version
# with the SQL Server version. Complete information on:
# https://docs.microsoft.com/pt-br/sql/connect/php/installation-tutorial-linux-mac?view=sql-server-2017#installing-on-debian
#
# This installation works with Debian 13, PHP 8.5, Drive PDO_SQLSRV 5.13.0, Microsoft ODBC Driver 18 for SQL Server , MS SQL Server 2008 R2 or higher

RUN apt-get -y install php8.5-dev php8.5-xml php8.5-intl

ENV ACCEPT_EULA=Y

# apt-key foi removido no Debian 12+ — usar gpg dearmor
RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
    && echo "deb [arch=amd64,armhf,arm64 signed-by=/usr/share/keyrings/microsoft-prod.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" > /etc/apt/sources.list.d/mssql-release.list

RUN apt-get update

RUN apt-get install -y --no-install-recommends \
    locales \
    apt-transport-https \
    && echo "en_US.UTF-8 UTF-8" > /etc/locale.gen \
    && locale-gen

# install MS ODBC 18
# https://docs.microsoft.com/pt-br/sql/connect/odbc/linux-mac/installing-the-microsoft-odbc-driver-for-sql-server?view=sql-server-2017#debian18
RUN apt-get -y install msodbcsql18 mssql-tools18

RUN echo 'export PATH="$PATH:/opt/mssql-tools18/bin"' >> ~/.bash_profile
RUN echo 'export PATH="$PATH:/opt/mssql-tools18/bin"' >> ~/.bashrc
RUN exec bash

RUN apt-get install -y unixodbc-dev libgssapi-krb5-2

##------------ Install Drive 5.13.0 for SQL Server -----------
# List version drive PDO https://pecl.php.net/package/pdo_sqlsrv
# Install Drive: https://docs.microsoft.com/pt-br/sql/connect/php/installation-tutorial-linux-mac?view=sql-server-2017

RUN pecl install sqlsrv-5.13.0
RUN pecl install pdo_sqlsrv-5.13.0

#For PHP CLI
RUN mkdir -p /etc/php/8.5/cli/conf.d \
    && echo "extension=pdo_sqlsrv.so" > /etc/php/8.5/cli/conf.d/30-pdo_sqlsrv.ini \
    && echo "extension=sqlsrv.so" > /etc/php/8.5/cli/conf.d/20-sqlsrv.ini

#For PHP WEB
RUN mkdir -p /etc/php/8.5/apache2/conf.d \
    && echo "extension=pdo_sqlsrv.so" > /etc/php/8.5/apache2/conf.d/30-pdo_sqlsrv.ini \
    && echo "extension=sqlsrv.so" > /etc/php/8.5/apache2/conf.d/20-sqlsrv.ini

RUN phpenmod -v 8.5 sqlsrv pdo_sqlsrv
#RUN apt-get install libapache2-mod-php8.5 apache2
RUN a2dismod mpm_event
RUN a2enmod mpm_prefork
RUN a2enmod php8.5


#PHP Install Mongodb ext
RUN apt-get -y install php8.5-mongodb

#Drive mongo via pecl
#RUN pecl -vvv install mongodb-1.13.0
#RUN echo "; configuration for php MongoDb module" >> /etc/php/8.4/mods-available/mongodb.ini
#RUN echo "; priority=20" >> /etc/php/8.4/mods-available/mongodb.ini
#RUN echo "extension=mongodb.so" >> /etc/php/8.4/mods-available/mongodb.ini
#RUN ln -s /etc/php/8.4/mods-available/mongodb.ini /etc/php/8.4/apache2/conf.d/20-mongodb.ini

## ------------- Python 3 para o CNPJ-full ------------------
# https://github.com/bjverde/CNPJ-full
# O CNPJ-full faz a carga, as consultas e o grafo em Python. O cnpjrfb chama o
# consulta.py com "python3" (GeraGrafoController).
#
# O Debian 13 traz o Python 3.13 e não deixa usar o pip no Python do sistema
# (PEP 668, "externally-managed-environment"). Por isso as bibliotecas ficam num
# ambiente virtual em /opt/cnpj-full-venv, que entra no PATH: assim "python3"
# e "pip" já são os do venv, no terminal e no PHP executado pelo Apache.
#
# Bibliotecas = requirements.txt do CNPJ-full (pandas, numpy, numexpr, networkx,
# python-dotenv) + pytest para rodar os testes do CNPJ-full dentro do container.
RUN apt-get -y install python3 python3-venv python3-pip


## ------------- Finishing ------------------
RUN apt-get clean

#Creating index of files
RUN updatedb

EXPOSE 80
EXPOSE 443
CMD apachectl -D FOREGROUND
