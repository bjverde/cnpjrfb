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
echo -e "${YBLUE} Script Debian 10 install Environment - Parte 01 de 02${NC}"
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


echo -e "${LGREEN} Etapa 5/${ETAPAS} - Install Python 3.8 ${NC}"
#Install Python 3.8 on Debian 10
# https://tecnstuff.net/how-to-install-python-3-8-on-debian-10/
# https://linuxize.com/post/how-to-install-python-3-8-on-debian-10/
# https://stackoverflow.com/questions/62830862/how-to-install-python3-8-on-debian-10
apt-get -y install build-essential zlib1g-dev libncurses5-dev libgdbm-dev libnss3-dev \
                        libssl-dev libsqlite3-dev libreadline-dev libffi-dev curl libbz2-dev \
                        libpq-dev lzma liblzma-dev

cd /var/opt
wget -c https://www.python.org/ftp/python/3.8.2/Python-3.8.2.tgz
tar -xf Python-3.8.2.tgz
cd /var/opt/Python-3.8.2
./configure --enable-optimizations
make -j 4
make altinstall


echo -e "${LGREEN} Etapa 6/${ETAPAS} - Install PostgreSql 13 ${NC}"
# https://www.osradar.com/how-to-install-postgresql-13-debian-10/
# https://codepre.com/install-postgresql-13-on-debian-10-debian-9.html
# https://computingforgeeks.com/install-postgresql-on-debian-linux/
wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add -
echo "deb http://apt.postgresql.org/pub/repos/apt/ `lsb_release -cs`-pgdg main" |sudo tee  /etc/apt/sources.list.d/pgdg.list
apt-get update; apt-get upgrade -y
apt-get -y install postgresql-13 postgresql-client-13 postgresql-contrib-13


echo -e "${LGREEN} Etapa 6/${ETAPAS} - Install PgAdmin4 ${NC}"
# https://www.pgadmin.org/download/pgadmin-4-apt/
# https://www.rosehosting.com/blog/install-pgadmin-4-on-debian-10/
curl https://www.pgadmin.org/static/packages_pgadmin_org.pub | sudo apt-key add
sh -c 'echo "deb https://ftp.postgresql.org/pub/pgadmin/pgadmin4/apt/$(lsb_release -cs) pgadmin4 main" > /etc/apt/sources.list.d/pgadmin4.list && apt update'
apt-get -y install pgadmin4 pgadmin4-web 
#pgadmin4-web 
sudo /usr/pgadmin4/bin/setup-web.sh

echo ''
echo -e "${LGREEN} Etapa 7/${ETAPAS} Execute os comandos abaixo na sequencia para alterar a senha e criar o banco de dados dados_rfb. INCLUIR os ponto e virgula ${NC}"
# https://stackoverflow.com/questions/24917832/how-connect-postgres-to-localhost-server-using-pgadmin-on-ubuntu
echo -e "${LGREEN} --------- ${NC}"
echo -e "${YBLUE} sudo -u postgres psql postgres ${NC}"
echo -e "${YBLUE} alter user postgres with password 'postgres'; ${NC}"
echo -e "${YBLUE} DROP DATABASE IF EXISTS dados_rfb; ${NC}"
echo -e "${YBLUE} CREATE DATABASE dados_rfb; ${NC}"
echo -e "${YBLUE} \q ${NC}"
echo -e "${LGREEN} --------- ${NC}"
echo ''
echo -e "${LGREEN} Depois de executar os comandos acima executar vbox_environment_2.sh ${NC}"