#!/bin/bash

#STYLE_COLOR
RED='\033[0;31m';
LIGHT_GREEN='\e[1;32m';
NC='\033[0m' # No Color

echo -e "${LIGHT_GREEN} Copiando arquivos do /opt/formDin para /var/www/html/formDin ${NC}"
cp -R /opt/formDin /var/www/html/formDin