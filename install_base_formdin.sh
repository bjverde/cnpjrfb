#!/bin/bash

#STYLE_COLOR
RED='\033[0;31m';
LIGHT_GREEN='\e[1;32m';
NC='\033[0m' # No Color

echo -e "${LIGHT_GREEN} Download FormDin com Git Clone para /opt/formDin ${NC}"
echo -e "${LIGHT_GREEN} essa etapa pode ser demorada !! :-( Vai depender da velocidade da sua internet ${NC}"
cd /opt/
git clone https://github.com/bjverde/formDin.git

echo -e "${LIGHT_GREEN} Baixando SysGen (gerador de sistemas) do github e colocando em /opt/formDin/sysgen ${NC}"
cd formDin;
git clone https://github.com/bjverde/sysgen.git

echo -e "${RED} Criando TMP formDin ${NC}"
mkdir -p base/tmp/
chmod 777 base/tmp/

echo -e "${LIGHT_GREEN} Alterando permissão da pasta /opt/formDin ${NC}"
chown -R www-data:www-data /opt/formDin