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
echo -e "${YBLUE} Script Debian 10 install Environment - Parte 02 de 02${NC}"
echo ''

echo -e "${LGREEN} Etapa 8/${ETAPAS} -  Download arquivos com wget ${NC}"
./dados_receita/output_files/download_files.sh


echo -e "${LGREEN} Etapa 9/${ETAPAS} - Python install requirements ${NC}"
cd dados_receita
python3.8 -m pip install --upgrade pip
pip install python-dotenv
pip install -r requirements.txt

echo -e "${LGREEN} Etapa 10/${ETAPAS} - Executa ETL e criar banco ${NC}"
python3.8 ETL_coletar_dados_e_gravar_BD.py