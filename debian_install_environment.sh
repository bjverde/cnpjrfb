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


echo -e "${LGREEN} Etapa 3/${ETAPAS} - Install PostgreSql 13 ${NC}"
wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add -
echo "deb http://apt.postgresql.org/pub/repos/apt/ `lsb_release -cs`-pgdg main" |sudo tee  /etc/apt/sources.list.d/pgdg.list
apt-get update; apt-get upgrade -y
apt-get -y install postgresql-13 postgresql-client-13 postgresql-contrib-13


echo ''
echo -e "${YBLUE} Alteração feita em $DESTINO ${NC}"
echo ''