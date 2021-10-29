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
echo -e "${YBLUE} Script START BANCO PostgreSQL ${NC}"
echo ''

echo -e "${LGREEN} Etapa 1/3 - Clonando Projeto Dados CNPJ do  aphonsoar ${NC}"
cd /var/opt
git clone https://github.com/aphonsoar/Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ.git aphonsoar


echo -e "${LGREEN} Etapa 2/3 - Python install requirements ${NC}"
cd /var/opt/aphonsoar
pip install -r requirements.txt

echo ''
echo -e "${YBLUE} Alteração feita em $DESTINO ${NC}"
echo ''