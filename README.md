# cnpjrfb
Sistema web em PHP usando o usando o [Adianti FrameWork](https://www.adianti.com.br/framework) para consultar os [Dados públicos CNPJ](https://www.gov.br/receitafederal/pt-br/assuntos/orientacao-tributaria/cadastros/consultas/dados-publicos-cnpj) fornecidos pela Receita Federal do Brasil.

A Versão 2.0.0 contem as alterações do modelo de dados de 21/03/2021 feito pela RFB. 

Agradecimento para todas as pessoas que contribuíram de forma direta ou indireta para o projeto. Abaixo alguns nomes de destaque, pois o sistema de consulta foi baseado nos scripts de carga de dados das pessoas abaixo. Sem essas pessoas o trabalho seria bem mais difícil: 
* Versão 2.0.0 ou superior, Aphonso Henrique do Amaral Rafael - https://github.com/aphonsoar/Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ
* Até a versão 1.2.0, Fabio Serpa - https://github.com/fabioserpa/CNPJ-full

# Instalando e rodando
O processo de instalação é simples para a parte PHP. Porém a carga do banco de dados pode ser complicada e muito demorada, levando algumas horas até dias dependendo do seu hardware.

## Requisitos

* PHP 7.4 ou superior
* Banco de Dados Relacional, com a carga dos dados conforme modelo. Funciona nos bancos PostgreSQL, MySQL, MariaDB, SQLite !
* Linux (Debian 10), pode funcionar no Windows porém não foi testado
* Aproximadamente 50 GB de espaço livre em disco para a instalação:
    * 5 GB arquivos zip da Receita Federal, que pode ser liberado depois da instalação.
    * 18 GB para arquivos texto descompactados, que pode ser liberado depois da instalação
    * 18 GB para banco de dados. Considerando o PostgreSQL.

## Informação sobre o Banco de dados e carga ETL

Tabela | Quantidade de linhas | Tamanho em MB
------ | ------------------ | --------------------
empresa | 45.811.638 | 
estabelecimento | 48.421.619 | 
socios | 20.426.417 | 
simples | 27.893.923 |
**Total** | **142.553.597**|

## Intalação separada

### Parte 1 - PHP
1. Ter um servidor PHP 7.4 ou superior. Com o drive PDO do banco relacional desejado e para o SqLite
1. Configura o PHP conforme orientações do [Adianti FrameWork 7.3.0](https://www.adianti.com.br/framework-quickstart)
1. Copie o conteudo da pasta `www/cnjrfb` do projeto para o seu servidor PHP.
1. Verifique se tudo dentro de `<caminho servidor>/cnjrfb/` tem permissão de execução do servidor web. Se for Linux (Debian/Ubuntu) com Apache pode executar `sudo chown -R www-data:www-data`
1. Abra o sistema em um navegador e verifique se os sistema está funcionando. Existe um pequeno banco em SqLite para demonstrar o funcionamento.

**ATENÇÃO!! a função de gerar grafo foi removida temporarimente da versão 2.**

### Parte 2 - a carga dos dados, no banco relacional !
Agora vem parte demorada ! 

* Baixe todos os arquivos do [site de receita federal](https://www.gov.br/receitafederal/pt-br/assuntos/orientacao-tributaria/cadastros/consultas/dados-publicos-cnpj). Para quem está usando Linux tem um script para essa parta `projeto/dados_receita/output_files/download_files.sh`
* Se conhece python e deseja usar o PostGres faça os procedimentos https://github.com/aphonsoar/Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ 
* Se não conhece python pode usar o script PHP para carga no banco 
    * Altere o arquivo de configuração `projeto/www/cargabd/config.php`
    * rode o script em modo terminal `projeto/www/cargabd/index.php`
    * vá descençar !! o processo todo em desktop no win10, i5, 16 ram, PHP 7.4 no wamp com config padrão levaria mais de 30 horas.

**ATENÇÃO!! devido o volume de dados crie índices, nas colunas que pretender ter o maior volume de pesquisa. Recomendo criar na coluna cnpj_basico em todas as tabelas**

### Parte 3 - Configurando o PHP para o banco completo !
* altera o arquivo `<caminho servidor>/cnjrfb/app/config/maindatabase.php` para apontar para o novo banco de dados. Veja como em [Adianti FrameWork 7.3.0](https://www.adianti.com.br/framework-quickstart)

## Intalação com VirtualBox - NÃO ESTÁ COMPLETO
Abaixo um breve tutorial para quem deseja rodar dentro do VirtualBox.

1. Instalar o VirtualBox
1. Baixa o ISO do Debian 10 https://www.debian.org/releases/buster/debian-installer/
1. Instarlar o Debian 10
1. Instale virtual guest additional https://averagelinuxuser.com/virtualbox-shared-folder/
    * montar CD do Virtual Box
    * logar como root
    * coloque o usuário principal no sudores `/sbin/usermod -aG sudo <NOME_USUARIO>`
    * executar `apt-get update; apt-get -y install build-essential dkms linux-headers-$(uname -r)`
    * rodar `sh /media/cdrom/VBoxLinuxAdditions.sh`
    * reiniciar a VM
    * colocar usuário no grupo virtual box `/sbin/usermod -aG vboxsf <NOME_USUARIO>`
    * reiniciar a VM
1. Se for usar pasta compatilhada no VirtualBox faça o mapeamento
    * copiar `<projeto>/www/` para `/var/www/html/`
    * copiar `<projeto>/dados_receita/` para `/var/opt/dados_receita/`
1. Se for clonar ou baixar esse projeto
    *  copiar a pasta `dados_receita` para `/var/opt/dados_receita/`
    * copiar `www/cnpjrfb` para `/var/www/httpd/cnpjrfb/`
1. rodar o script `vbox_environment_1.sh`
1. rodar o script `vbox_environment_2.sh`

## Intalação via Docker-compose - NÃO ESTÁ COMPLETO
Existem alguns arquivos em Docker-compose para criar todo o ambiente necessários para rodar tudo que é necessário. A ideia é com um comando o usuário consiga ter tudo funcionando sem muito esforço.

1. Instale o Docker e Docker-compose 
1. clone o projeto
1. Abriu um terminal na raiz do projeto
1. Execute o comando `docker-compose build` para gerar todo o ambiente.
1. Execute o comando `docker-compose -f docker-compose.yml up -d` para rodar o ambiente já configurado. O Docker pretender resolver apenas [Parte 1 - PHP](#parte-1---php) e a [Parte 2 - Python](#parte-2---python).
1. Verificando se a instalação está correta: Abra o sistema em um navegador e verifique se os 3 menus dentre home está funcionando: Empresa, Sócios e CNEA.
1. Verificando se a instalação está correta: Abra o sistema em um navegador. Menu > Facilitadores >  Gera Grafo , sugestão é o CNPJ 00.000.000/0001-91
1. Executar o procedimento da [Parte 3 - O banco completo](#parte-3---o-banco-completo-) por ser algo muito demorado deve ser feito manualmente.