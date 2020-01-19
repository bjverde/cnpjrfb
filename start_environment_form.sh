#!/bin/bash

echo -e '\033[01;32m Baixando FormDin FrameWork do github e colocando em www/formdin \033[00;37m!!!'
cd www
git clone https://github.com/bjverde/formDin.git
chmod 777 formdin/
chmod 777 formdin/base/tmp/

echo -e '\033[01;32m Baixando SysGen (gerador de sistemas) do github e colocando em www/formdin/sysgen \033[00;37m!!!'
cd formDin
git clone https://github.com/bjverde/sysgen.git
cd ../..

echo -e '\033[01;34m Instalando Docker \033[00;37m!!!'

sudo apt-get remove docker docker-engine docker.io;
sudo apt-get update;
sudo apt-get install -y apt-transport-https ca-certificates curl software-properties-common;
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
sudo add-apt-repository \
   "deb [arch=amd64] https://download.docker.com/linux/ubuntu \
   $(lsb_release -cs) \
   stable";
sudo apt-get update;
sudo apt-get install docker-ce;

echo -e '\033[01;34m Instalando Docker Compose \033[00;37m!!!'
sudo curl -L https://github.com/docker/compose/releases/download/1.21.0/docker-compose-$(uname -s)-$(uname -m) -o /usr/local/bin/docker-compose;
sudo chmod +x /usr/local/bin/docker-compose;

echo -e '\033[01;34m Agora o Docker vai para baixar as imagens, gerar e levanta o ambiente \033[00;37m!!!'
sudo docker-compose build;
sudo docker-compose up;