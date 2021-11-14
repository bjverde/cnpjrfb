# Source
# https://hub.docker.com/_/php/
# Docker File Source
# https://hub.docker.com/_/debian

#How to build
#sudo docker build -f Dockerfile . -t debian10_apache_php8

#How use iterative mode
#sudo docker exec -it debian10_apache_php8:last /bin/bash

#How use iterative mode image
#sudo docker run -it debian10_apache_php8:last /bin/bash           #only bash
#sudo docker run -p 80:80 -it debian10_apache_php8:last /bin/bash
#sudo docker run -d -p 80:80 debian10_apache_php8:last

#Stop all containers
#sudo docker stop $(sudo docker ps -a -q)

#Remove all containers
#sudo docker rm $(sudo docker ps -a -q)

#######################################
FROM debian:10
LABEL maintainer="bjverde@yahoo.com.br"

EXPOSE 80

ENV DEBIAN_FRONTEND noninteractive

#Install update
RUN apt-get update
RUN apt-get upgrade -y

#Install facilitators
RUN apt-get -y install locate mlocate wget apt-utils curl apt-transport-https lsb-release \
             ca-certificates software-properties-common zip unzip vim rpl apt-utils sudo gnupg gnupg2

#Install PostgreSQL 13 on Debian 10
# https://www.osradar.com/how-to-install-postgresql-13-debian-10/
# https://codepre.com/install-postgresql-13-on-debian-10-debian-9.html
# https://computingforgeeks.com/install-postgresql-on-debian-linux/
#RUN wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add -
#RUN echo "deb http://apt.postgresql.org/pub/repos/apt/ `lsb_release -cs`-pgdg main" |sudo tee  /etc/apt/sources.list.d/pgdg.list
#RUN apt-get update; apt-get upgrade -y
#RUN apt-get -y install postgresql-13 postgresql-client-13 postgresql-contrib-13

#Install pgAdmin 4 on Debian 10
#RUN apt-get -y install pgadmin4  pgadmin4-apache2



#Install Python 3.8 on Debian 10
# https://tecnstuff.net/how-to-install-python-3-8-on-debian-10/
# https://linuxize.com/post/how-to-install-python-3-8-on-debian-10/
# https://stackoverflow.com/questions/62830862/how-to-install-python3-8-on-debian-10
RUN  apt-get -y install build-essential zlib1g-dev libncurses5-dev libgdbm-dev libnss3-dev \
                        libssl-dev libsqlite3-dev libreadline-dev libffi-dev curl libbz2-dev \
                        libpq-dev lzma liblzma-dev

WORKDIR /var/opt
RUN wget -c https://www.python.org/ftp/python/3.8.2/Python-3.8.2.tgz
RUN tar -xf Python-3.8.2.tgz
WORKDIR /var/opt/Python-3.8.2
RUN ./configure --enable-optimizations
RUN make -j 4
RUN make altinstall


## ------------- Install Apache2 + PHP 8.0  x86_64 ------------------
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
RUN apt-get -y install apache2 libapache2-mod-php8.0 php8.0 php8.0-cli php8.0-common php8.0-opcache

#PHP Install CURl
RUN apt-get -y install curl php8.0-curl

#PHP Intall DOM, Json, XML e Zip
RUN apt-get -y install php8.0-dom php8.0-xml php8.0-zip php8.0-soap php8.0-intl php8.0-xsl

#PHP Install MbString
RUN apt-get -y install php8.0-mbstring

#PHP Install GD
RUN apt-get -y install php8.0-gd

#PHP Install PDO SqLite
RUN apt-get -y install php8.0-pdo php8.0-pdo-sqlite php8.0-sqlite3

#PHP Install PDO MySQL
RUN apt-get -y install php8.0-pdo php8.0-pdo-mysql php8.0-mysql 

#PHP Install PDO PostGress
RUN apt-get -y install php8.0-pdo php8.0-pgsql

## -------- Config Apache ----------------
RUN a2dismod mpm_event
RUN a2dismod mpm_worker
RUN a2enmod  mpm_prefork
RUN a2enmod  rewrite
RUN a2enmod  php8.0

# Enable .htaccess reading
RUN LANG="en_US.UTF-8" rpl "AllowOverride None" "AllowOverride All" /etc/apache2/apache2.conf

## ------------- LDAP ------------------
#PHP Install LDAP
RUN apt-get -y install php8.0-ldap

#Apache2 enebla LDAP
RUN a2enmod authnz_ldap
RUN a2enmod ldap

## ------------- Add-ons ------------------
#Install GIT
RUN apt-get -y install -y git-core

#PHP Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#PHP Install PHPUnit
#https://phpunit.de/announcements/phpunit-9.html
RUN wget -O /usr/local/bin/phpunit-9.phar https://phar.phpunit.de/phpunit-9.phar; chmod +x /usr/local/bin/phpunit-9.phar; \
ln -s /usr/local/bin/phpunit-9.phar /usr/local/bin/phpunit

## ------------- Finishing ------------------
RUN apt-get clean
RUN apt-get autoremove -y

#Creating index of files
RUN updatedb

EXPOSE 80
CMD apachectl -D FOREGROUND