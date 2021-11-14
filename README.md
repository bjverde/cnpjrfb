# cnpjrfb
Sistema para Consultar os [Dados públicos CNPJ](https://receita.economia.gov.br/orientacao/tributaria/cadastros/cadastro-nacional-de-pessoas-juridicas-cnpj/dados-publicos-cnpj) fornecidos pela Receita Federal do Brasil.

Versão 2.0.0 - depois das alterações de 21/03/2021

Baseado no projeto https://github.com/aphonsoar/Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ

## Vídeos no Youtube sobre

*Apresentação rápida - Consultar os Dados públicos CNPJ fornecidos pela Receita Federal do Brasil*

[![Apresentação rápida - Consultar os Dados públicos CNPJ fornecidos pela Receita Federal do Brasil](http://img.youtube.com/vi/jgnj-rcs5WE/0.jpg)](http://www.youtube.com/watch?v=jgnj-rcs5WE "Apresentação rápida - Consultar os Dados públicos CNPJ fornecidos pela Receita Federal do Brasil")

*Detalhando as funções do cnpjrfb*

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

Você pode fazer a [instalação manual](#intalação-separada) etapa por etapa ou usar a [Intalação via Docker-compose](#intalação-via-docker-compose) que um pouco mais automatizada

## Requistos
* PHP 7.4 ou superior. Configura o PHP conforme orientações do [Adianti FrameWork 7.3.0](https://www.adianti.com.br/framework-quickstart)
* Python 3.8 ou superior
* Aproximadamente 50 GB de espaço livre em disco para a instalação:
    * 5 GB arquivos zip da Receita Federal. Pode ser liberado depois
    * 18 GB para arquivos texto descompactados. Pode ser liberado depois.
    * 18 GB para banco de dados PostgreSQL.

## Intalação separada

### Parte 1 - PHP
1. Ter um servidor PHP 7.4 ou superior. Configura o PHP conforme orientações do [Adianti FrameWork 7.3.0](https://www.adianti.com.br/framework-quickstart)
1. Copie o conteudo da pasta `www` do projeto para o seu servidor PHP.
1. Verifique se tudo dentro de `<caminho servidor>/cnjrfb/app/CNPJ-full` tem permissão de execução do servidor web. Se for Linux (Debian/Ubuntu) com Apache pode executar `sudo chown -R www-data:www-data`
1. Abra o sistema em um navegador e verifique se os 3 menus dentro HOME está funcionando: Empresa, Sócios e CNEA. 

**ATENÇÃO!! a função de gerar grafo depende da parte 2 em Python para funcionar**. Nesse momento você está usando um mini banco de dados de exemplo com apenas 56 KB para mostrar que tudo está funcionando. A versão final do banco de dados tem mais de 6GB e depende da parte 3 para funcionar.

### Parte 2 - Python 
1. Instale o Python 3.8 ou superior
1. Instale os requisitos `pip install -r requirements.txt` USE o arquivo [requirements.txt aqui no projeto](https://github.com/bjverde/cnpjrfb/blob/master/requirements.txt)
1. Copie o projeto [CNPJ-FULL](https://github.com/fabioserpa/CNPJ-full) e coloque na pasta `<caminho servidor>/cnjrfb/app/CNPJ-full`
    1. [Instale o PIP conforme orientação](https://github.com/fabioserpa/CNPJ-full#gerenciador-de-pacotes-do-python-pip)    
1. Abra o sistema em um navegador. Menu > Facilitadores >  Gera Grafo , sugestão é o CNPJ 00.000.000/0001-91

### Parte 3 - O banco completo !
É algo demorado mesmo! Pois irá baixar 6 GB de dados da Receita Federal e depois criar o banco de dados completo.

Baixar todos dados [Dados públicos CNPJ](https://receita.economia.gov.br/orientacao/tributaria/cadastros/cadastro-nacional-de-pessoas-juridicas-cnpj/dados-publicos-cnpj) na pasta `<caminho servidor>/cnjrfb/app/CNPJ-full/downloads`

Conforme orientação no [CNPJ-FULL do Fabio Serpa](https://github.com/fabioserpa/CNPJ-full#dados-p%C3%BAblicos-cnpj---convers%C3%A3o-para-csvsqlite-e-consultas)
```
ATENÇÃO!

A partir de março de 2021, a Receita Federal mudou completamente a forma de disponibilizar os dados públicos do CNPJ. O script de carga deste repositório ainda não foi atualizado para refletir estas alterações, e portanto não funcionará para os novos arquivos disponibilizados a partir desta data.

A boa notícia é que agora os arquivos já estão sendo disponibilizados pela RF em formato CSV, o que, dependendo do seu caso, pode até dispensar o uso deste script.

Os scripts deste repositório no entanto ainda assim serão atualizados para manter funcional a conversão dos dados para formato SQLite, assim como os scripts de consulta.
```

1. [Converta os arquivos ZIP para Sqlite conforme, CNPJ Full](https://github.com/fabioserpa/CNPJ-full#convers%C3%A3o-para-csv-ou-sqlite) 
1. Alterar o arquivo `<caminho servidor>/cnjrfb/app/config/cnpj_full.ini`. Altere o parâmetro de `name= "app/database/CNPJ_full.db"` para `name = "app/CNPJ-full/data/CNPJ_full.db"`


## Intalação via Docker-compose
Existem alguns arquivos em Docker-compose para criar todo o ambiente necessários para rodar tudo que é necessário. A ideia é com um comando o usuário consiga ter tudo funcionando sem muito esforço.

1. Instale o Docker e Docker-compose 
1. clone o projeto
1. Abriu um terminal na raiz do projeto
1. Execute o comando `docker-compose build` para gerar todo o ambiente.
1. Execute o comando `docker-compose -f docker-compose.yml up -d` para rodar o ambiente já configurado. O Docker pretender resolver apenas [Parte 1 - PHP](#parte-1---php) e a [Parte 2 - Python](#parte-2---python).
1. Verificando se a instalação está correta: Abra o sistema em um navegador e verifique se os 3 menus dentre home está funcionando: Empresa, Sócios e CNEA.
1. Verificando se a instalação está correta: Abra o sistema em um navegador. Menu > Facilitadores >  Gera Grafo , sugestão é o CNPJ 00.000.000/0001-91
1. Executar o procedimento da [Parte 3 - O banco completo](#parte-3---o-banco-completo-) por ser algo muito demorado deve ser feito manualmente.


## Intalação com VirtualBox
Abaixo um breve tutorial para quem deseja instalar dentro do VirtualBox.

1. Instalar o VirtualBox no sistema operacional nativo (Windows 7, 10)
1. Baixa o ISO do Debian 10 https://www.debian.org/releases/buster/debian-installer/
1. Instarlar o Debian 10
1. Se for usar pasta compatilhada no VirtualBox 
    * instale virtual guest additional https://linuxize.com/post/how-to-install-virtualbox-guest-additions-on-debian-10/
    * coloque o usuário principal no sudores `/sbin/usermod -aG sudo <NOME_USUARIO>`
    * reiniciar a VM
    * colocar usuário no grupo virtual box `sudo usermod -a -G vboxsf $USER`
    * reiniciar a VM
1. Clonar o baixar esse projeto
1. rodar o script `virtualbox_debian_install_environment.sh`