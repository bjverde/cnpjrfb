#!/bin/bash

ORIGEM=$1
DESTINO=$2

#Cores
RED='\033[0;31m'
LGREEN='\033[0;32m'
YBLUE='\033[1;33;4;44m'
NC='\033[0m' # No Color

#Codigo
ETAPAS=7

echo ''
echo -e "${YBLUE} Script Debian 10.11 install PHP 8.1 com Drive SqlServer${NC}"
echo ''

echo -e "${LGREEN} Etapa 1/${ETAPAS} - Install update ${NC}"
apt-get update
apt-get upgrade -y

echo -e "${LGREEN} Etapa 2/${ETAPAS} - Install facilitators ${NC}"
apt-get -y install locate mlocate wget apt-utils curl apt-transport-https lsb-release ca-certificates software-properties-common zip unzip vim rpl apt-utils sudo gnupg gnupg2


echo -e "${LGREEN} Etapa 3/${ETAPAS} - Install Apache2 + PHP 8.1 ${NC}"
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
apt-get -y install apache2 libapache2-mod-php8.1 php8.1 php8.1-cli php8.1-common php8.1-opcache

#PHP Install CURl
apt-get -y install curl php8.1-curl

#PHP Intall DOM, Json, XML e Zip
apt-get -y install php8.1-dom php8.1-xml php8.1-zip php8.1-soap php8.1-intl php8.1-xsl

#PHP Install MbString
apt-get -y install php8.1-mbstring

#PHP Install GD
apt-get -y install php8.1-gd

#PHP Install PDO SqLite
apt-get -y install php8.1-pdo php8.1-pdo-sqlite php8.1-sqlite3

#PHP Install PDO MySQL
apt-get -y install php8.1-pdo php8.1-pdo-mysql php8.1-mysql 

#PHP Install PDO PostGress
apt-get -y install php8.1-pdo php8.1-pgsql


#PHP Install MondoDB Drive
pecl install mongodb


echo -e "${LGREEN} Etapa 4/${ETAPAS} - Config Apache ${NC}"
a2dismod mpm_event
a2dismod mpm_worker
a2enmod  mpm_prefork
a2enmod  rewrite
a2enmod  php8.1

# Enable .htaccess reading
LANG="en_US.UTF-8" rpl "AllowOverride None" "AllowOverride All" /etc/apache2/apache2.conf

echo -e "${LGREEN} Etapa 4/${ETAPAS} - Config LDAP no Apache ${NC}"
apt-get -y install php8.1-ldap

#Apache2 enebla LDAP
a2enmod authnz_ldap
a2enmod ldap


echo -e "${LGREEN} Etapa 5/${ETAPAS} - Install Precondition for Drive SQL Server ${NC}"

apt-get -y install php8.1-dev php8.1-xml php8.1-intl

ENV ACCEPT_EULA=Y

curl -sSL https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
apt-add-repository https://packages.microsoft.com/debian/10/prod

curl -s https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
curl -s https://packages.microsoft.com/config/debian/10/prod.list > /etc/apt/sources.list.d/mssql-release.list

sudo apt-get update
apt-get install -y --no-install-recommends locales apt-transport-https
echo "en_US.UTF-8 UTF-8" > /etc/locale.gen
locale-gen

echo -e "${LGREEN} Etapa 6/${ETAPAS} - install MSODBC 17 ${NC}"

apt-get -y --no-install-recommends install msodbcsql17 mssql-tools

echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bash_profile
echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bashrc
exec bash

apt-get -y install unixodbc unixodbc-dev
apt-get -y install gcc g++ make autoconf libc-dev pkg-config


echo -e "${LGREEN} Etapa 7/${ETAPAS} - Install Drive 5.10.0 for SQL Server ${NC}"

pecl install sqlsrv-5.10.0
pecl install pdo_sqlsrv-5.10.0

echo extension=pdo_sqlsrv.so >> `php --ini | grep "Scan for additional .ini files" | sed -e "s|.*:\s*||"`/30-pdo_sqlsrv.ini
echo extension=sqlsrv.so >> `php --ini | grep "Scan for additional .ini files" | sed -e "s|.*:\s*||"`/20-sqlsrv.ini

echo "extension=pdo_sqlsrv.so" >> /etc/php/8.1/apache2/conf.d/30-pdo_sqlsrv.ini
echo "extension=sqlsrv.so" >> /etc/php/8.1/apache2/conf.d/20-sqlsrv.ini

a2dismod mpm_event
a2enmod mpm_prefork
a2enmod php8.1

apt-get clean
updatedb

#Apache restar
service apache2 restart