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

echo -e "${LGREEN} Etapa 1/4 - Clonando Projeto Dados CNPJ do  aphonsoar ${NC}"
cd /var/opt
git clone https://github.com/aphonsoar/Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ.git aphonsoar


echo -e "${LGREEN} Etapa 2/4 - Python install requirements ${NC}"
cd /var/opt/aphonsoar
python3.8 -m pip install --upgrade pip
pip install python-dotenv
pip install -r requirements.txt

echo -e "${LGREEN} Etapa 3/4 - Python install requirements ${NC}"
cd /var/opt/aphonsoar/code;python3.8 ETL_coletar_dados_e_gravar_BD.py

echo ''
echo -e "${YBLUE} Alteração feita em $DESTINO ${NC}"
echo ''