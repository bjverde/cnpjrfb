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

echo -e "${LGREEN} Etapa 1/4 - Download arquivos com wget ${NC}"
#/var/opt/dados_receita/download_files.sh


echo -e "${LGREEN} Etapa 2/5 - Python install requirements ${NC}"
cd /var/opt/dados_receita
python3.8 -m pip install --upgrade pip
pip install python-dotenv
pip install -r requirements.txt

echo -e "${LGREEN} Etapa 3/5 - Criando ENV ${NC}"
echo "# Config Files" >> .env
echo "OUTPUT_FILES_PATH=/var/opt/dados_receita/output_files" >> .env
echo "EXTRACTED_FILES_PATH=/var/opt/dados_receita/extracted_files" >> .env
echo "# Config Bando" >> .env
echo "DB_HOST=localhost" >> .env
echo "DB_PORT=5432" >> .env
echo "DB_USER=postgres" >> .env
echo "DB_PASSWORD=postgres" >> .env
echo "DB_NAME=Dados_RFB" >> .env

echo -e "${LGREEN} Etapa 4/5 - Executa ETL e criar banco ${NC}"
python3.8 /var/opt/aphonsoar/code/ETL_coletar_dados_e_gravar_BD.py

echo ''
echo -e "${YBLUE} Alteração feita em $DESTINO ${NC}"
echo ''