#!/bin/bash

ORIGEM=$1
DESTINO=$2

#Cores
RED='\033[0;31m'
LGREEN='\033[0;32m'
YBLUE='\033[1;33;4;44m'
NC='\033[0m' # No Color

#Codigo

echo ''
echo -e "${YBLUE} Script de Deploy com sudo incluido ${NC}"
echo ''

echo -e "${LGREEN} Etapa 1/3 - Removendo pasta .git ${NC}"

if [ -d "$ORIGEM" ]; then
	cd $ORIGEM
	rm -fr .git
	rm     .gitlab-ci.yml
	cd ..
fi

if [ -d "$DESTINO" ]; then
   echo -e "${LGREEN} Etapa 2/3 - Atualizando TODA a pasta $DESTINO com o conteudo da pasta $ORIGEM ${NC}"
   sudo -u www-data rm -fr $DESTINO
   sudo -u www-data cp -fr $ORIGEM $DESTINO
   rm -fr $ORIGEM
else
   echo -e "${LGREEN} Etapa 2/3 - Copiando a pasta $ORIGEM pasta $DESTINO ${NC}"
   sudo -u www-data cp -fr $ORIGEM $DESTINO
   rm -fr $ORIGEM
fi

if [ -d "$DESTINO" ]; then
   echo -e "${LGREEN} Etapa 3/3 - Criando Link simbólico da pasta BASE na pasta do projeto  ${NC}"
   cd $DESTINO
   sudo -u www-data ln -s ../base base
fi

echo ''
echo -e "${YBLUE} Alteração feita em $DESTINO ${NC}"
echo ''