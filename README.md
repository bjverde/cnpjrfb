# cnpjrfb
Sistema para Consultar os [Dados públicos CNPJ](https://receita.economia.gov.br/orientacao/tributaria/cadastros/cadastro-nacional-de-pessoas-juridicas-cnpj/dados-publicos-cnpj) fornecidos pela Receita Federal do Brasil.

## Vídeos no Youtube sobre
[![Apresentação rápida - Consultar os Dados públicos CNPJ fornecidos pela Receita Federal do Brasil](http://img.youtube.com/vi/jgnj-rcs5WE/0.jpg)](http://www.youtube.com/watch?v=jgnj-rcs5WE "Apresentação rápida - Consultar os Dados públicos CNPJ fornecidos pela Receita Federal do Brasil")

[![Detalhando as funções do cnpjrfb](http://img.youtube.com/vi/p0vgvzox-BM/0.jpg)](http://www.youtube.com/watch?v=p0vgvzox-BM "Detalhando as funções do cnpjrfb")


## Desktop
Esse é um sistema foi feito com PHP usando o [Adianti FrameWork 7.1](https://www.adianti.com.br/framework) e [CNPJ-FULL do Fabio Serpa](https://github.com/fabioserpa/CNPJ-full).

![Tela de Pesquisa Empresa](www/cnpjrfb/app/images/tela_pesquisa_empresa.png?raw=true "Tela de Pesquisa Empresa")

![Tela de Pesquisa Socio](www/cnpjrfb/app/images/tela_pesquisa_socio.png?raw=true "Tela de Pesquisa Socio")

## Celular
![Visão no Celular](www/cnpjrfb/app/images/celular_empresa_visao.png?raw=true "Visão no Celular")

![Visão no Celular menu CNAE](www/cnpjrfb/app/images/celular_empresa_pesquisa.png?raw=true "Visão no Celular menu CNAE")

![Visão no Celular menu](www/cnpjrfb/app/images/celular_menu.png?raw=true "Visão no Celular menu")

## Tablet

![Visão no Tablet](www/cnpjrfb/app/images/tablet.png?raw=true "Visão no Tablet")

# Instalando e rodando

## Requistos
* PHP 7.2 ou superior. Configura o PHP conforme orientações do [Adianti FrameWork 7.1](https://www.adianti.com.br/framework-quickstart)
* Python 3.6 ou superior
* Aproximadamento 200 GB de espaço livre em disco para a instalação:
    * 6 GB arquivos zip da Receita Federal. Pode ser liberado depois
    * 85 GB para arquivos texto descompactados. Pode ser liberado depois.
    * 85 GB para banco de dados SqLite.

## Intalação separada

### Parte 1 - O Ambiente
Na primeira parte será a instalação dos elementos básicos sem banco de dados completo.

1. Requisito atendidos: PHP, Python e Disco
1. Copie o conteudo da pasta `www` do projeto para o seu servidor PHP.
1. Copie o projeto [CNPJ-FULL](https://github.com/fabioserpa/CNPJ-full) e coloque na pasta `<caminho servidor>/cnjrfb/app/CNPJ-full`
    1. [Instale o PIP conforme orientação](https://github.com/fabioserpa/CNPJ-full#gerenciador-de-pacotes-do-python-pip)
    1. Instale os requisitos `pip install -r requirements.txt` USE o arquivo [requirements.txt aqui no projeto](https://github.com/bjverde/cnpjrfb/blob/master/requirements.txt)
1. Copie arquivo `<caminho servidor>/cnjrfb/app/database/CNPJ_full.db` 57 Kb para `<caminho servidor>/cnjrfb/app/CNPJ-full/data`
1. Crie a pasta `<caminho servidor>/cnjrfb/app/CNPJ-full/graficos`
1. Verifique se tudo dentro de `<caminho servidor>/cnjrfb/app/CNPJ-full` tem permissão de execução do servidor web. Se for Linux (Debian/Ubuntu) com Apache pode executar `sudo chown -R www-data:www-data`
1. Abra o sistema em um navegador e verifique se tudo está funcionando incluindo a geração do grafo.

### Parte 2 - O banco completo !
A segunda parte é algo demoradao mesmo. Pois irá baixar 6 GB de dados da Receita Federal e depois criar o banco de dados completo.

1. Baixar todos dados [Dados públicos CNPJ](https://receita.economia.gov.br/orientacao/tributaria/cadastros/cadastro-nacional-de-pessoas-juridicas-cnpj/dados-publicos-cnpj) na pasta `<caminho servidor>/cnjrfb/app/CNPJ-full/downloads`
1. Execute o comando da carga `python3 cnpj.py downloads sqlite data --dir` essa é parte mais demorada.


## Intalação via Docker-compose
Existem alguns arquivos em Docker-compose para criar todo o ambiente necessários para rodar tudo que é necessário. A ideia é com um comando o usuário consiga ter tudo funcionando sem muito esforço.

### No Linux
* instale o Docker e Docker-compose 
* clone o projeto
* Execute o comando `sudo docker-compose build` para gerar todo o ambiente
* Execute o comando `sudo docker-compose -f docker-compose.yml up -d` para rodar o ambiente já configurado.
* Docker pretender resolver apenas [Parte 1 - O Ambiente](#parte-1---o-ambiente), a [Parte 2 - O banco completo !](#parte-2---o-banco-completo-) por ser algo muito demorado deve ser feito manualmente
