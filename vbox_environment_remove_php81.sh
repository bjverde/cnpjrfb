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
echo -e "${YBLUE} Script Debian 10.11 remover Apache e PHP 8.1${NC}"
echo ''

apt-get -y remove --purge apache2* libapache2* php* msodbcsql17* mssql*

apt-get autoremove
apt-get autoclean
apt-get clean
updatedb