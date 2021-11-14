#!/bin/bash

ORIGEM=$1
DESTINO=$2

#Cores
RED='\033[0;31m'
LGREEN='\033[0;32m'
YBLUE='\033[1;33;4;44m'
NC='\033[0m' # No Color

#Codigo
ETAPAS=10

echo ''
echo -e "${YBLUE} Script Debian 10 install Environment ${NC}"
echo ''

echo -e "${LGREEN} Etapa 1/${ETAPAS} - Install update ${NC}"
apt-get update
apt-get upgrade -y

echo -e "${LGREEN} Etapa 2/${ETAPAS} - Install facilitators ${NC}"
apt-get -y install locate mlocate wget apt-utils curl apt-transport-https lsb-release ca-certificates software-properties-common zip unzip vim rpl apt-utils sudo gnupg gnupg2


echo -e "${LGREEN} Etapa 3/${ETAPAS} - Install Apache2 + PHP 8.0 ${NC}"
#Thread Safety 	disabled 
#PHP Modules : calendar,Core,ctype,date,exif,fileinfo,filter,ftp,gettext,hash,iconv,json,libxml
#PHP Modules : ,openssl,pcntl,pcre,PDO,Phar,posix,readline,Reflection,session,shmop,sockets,SPL,standard
#PHP Modules : ,sysvmsg,sysvsem,sysvshm,tokenizer,Zend OPcache,zlib

wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list
apt-get update
# Set Timezone

ln -fs /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && apt-get update \
    && apt-get install -y --no-install-recommends tzdata \
    && dpkg-reconfigure --frontend noninteractive tzdata

#intall Apache + PHP
apt-get -y install apache2 libapache2-mod-php8.0 php8.0 php8.0-cli php8.0-common php8.0-opcache

#PHP Install CURl
apt-get -y install curl php8.0-curl

#PHP Intall DOM, Json, XML e Zip
apt-get -y install php8.0-dom php8.0-xml php8.0-zip php8.0-soap php8.0-intl php8.0-xsl

#PHP Install MbString
apt-get -y install php8.0-mbstring

#PHP Install GD
apt-get -y install php8.0-gd

#PHP Install PDO SqLite
apt-get -y install php8.0-pdo php8.0-pdo-sqlite php8.0-sqlite3

#PHP Install PDO MySQL
apt-get -y install php8.0-pdo php8.0-pdo-mysql php8.0-mysql 

#PHP Install PDO PostGress
apt-get -y install php8.0-pdo php8.0-pgsql


echo -e "${LGREEN} Etapa 4/${ETAPAS} - Config Apache ${NC}"
a2dismod mpm_event
a2dismod mpm_worker
a2enmod  mpm_prefork
a2enmod  rewrite
a2enmod  php8.0

# Enable .htaccess reading
LANG="en_US.UTF-8" rpl "AllowOverride None" "AllowOverride All" /etc/apache2/apache2.conf

echo -e "${LGREEN} Etapa 4/${ETAPAS} - Config LDAP no Apache ${NC}"
apt-get -y install php8.0-ldap

#Apache2 enebla LDAP
a2enmod authnz_ldap
a2enmod ldap

echo -e "${LGREEN} Etapa 5/${ETAPAS} - Install PostgreSql 13 ${NC}"
# https://www.osradar.com/how-to-install-postgresql-13-debian-10/
# https://codepre.com/install-postgresql-13-on-debian-10-debian-9.html
# https://computingforgeeks.com/install-postgresql-on-debian-linux/
wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add -
echo "deb http://apt.postgresql.org/pub/repos/apt/ `lsb_release -cs`-pgdg main" |sudo tee  /etc/apt/sources.list.d/pgdg.list
apt-get update; apt-get upgrade -y
apt-get -y install postgresql-13 postgresql-client-13 postgresql-contrib-13


echo -e "${LGREEN} Etapa 6/${ETAPAS} - Install PgAdmin4 ${NC}"
# https://www.pgadmin.org/download/pgadmin-4-apt/
curl https://www.pgadmin.org/static/packages_pgadmin_org.pub | sudo apt-key add
sh -c 'echo "deb https://ftp.postgresql.org/pub/pgadmin/pgadmin4/apt/$(lsb_release -cs) pgadmin4 main" > /etc/apt/sources.list.d/pgadmin4.list && apt update'
apt-get -y install pgadmin4 pgadmin4-web 
#pgadmin4-web 
sudo /usr/pgadmin4/bin/setup-web.sh

echo -e "${LGREEN} Acesse o link para ver o PGADMIN4 http://localhost/pgadmin4 ${NC}"


echo ''
echo -e "${YBLUE} Alteração feita em $DESTINO ${NC}"
echo ''