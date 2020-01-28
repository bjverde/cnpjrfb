# cnpjrfb
Sistema para Consultar os [Dados públicos CNPJ](https://receita.economia.gov.br/orientacao/tributaria/cadastros/cadastro-nacional-de-pessoas-juridicas-cnpj/dados-publicos-cnpj) fornecidos pela Receita Federal do Brasil.

Esse é um sistema foi feito com PHP usando o [Adianti FrameWork 7.0](https://www.adianti.com.br/framework) e [CNPJ-FULL do Fabio Serpa](https://github.com/fabioserpa/CNPJ-full).


# Instalando e rodando

## Requistos
* Baixar todos dados [Dados públicos CNPJ](https://receita.economia.gov.br/orientacao/tributaria/cadastros/cadastro-nacional-de-pessoas-juridicas-cnpj/dados-publicos-cnpj)
* Python 3.6 ou superior
* Instalar o [CNPJ-FULL](https://github.com/fabioserpa/CNPJ-full)
* Rodar o script para criar o banco de dados [CNPJ-FULL](https://github.com/fabioserpa/CNPJ-full)
* PHP 7.2 ou superior
* Configure o PHP conforme orientações do [Adianti FrameWork](https://www.adianti.com.br/framework-quickstart)

## Intalação separada

* Copie o conteudo da pasta `www` do projeto para o seu servidor PHP.
* Copie os arquivo do [CNPJ-FULL](https://github.com/fabioserpa/CNPJ-full) juntamento o banco de dados para `<caminho servidor>/cnjrfb/app/cnpj_full`