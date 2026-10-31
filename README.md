# cnpjrfb

Sistema web em PHP, feito com o [Adianti Framework](https://www.adianti.com.br/framework), para consultar os [dados públicos do CNPJ](https://dados.gov.br/dados/conjuntos-dados/cadastro-nacional-da-pessoa-juridica---cnpj) publicados pela Receita Federal do Brasil (RFB): empresas, estabelecimentos (matriz e filiais), sócios, Simples/MEI e as tabelas de apoio (CNAE, municípios, países, naturezas jurídicas, qualificações e motivos).

O projeto tem três partes:

* **Sistema web** (`www/cnpjrfb`): telas de pesquisa e ficha completa do CNPJ, no celular, tablet ou desktop.
* **Carga** (`www/cargabd`): script PHP de terminal que lê os arquivos da RFB e grava no banco. Funciona com MySQL/MariaDB, PostgreSQL, SQLite, SQL Server e Oracle.
* **Modelo de banco** (`modelo_banco`): scripts de criação das tabelas para os mesmos cinco bancos, gerados de uma única definição e seguindo o [dicionário de dados da RFB](docs/cnpj-metadados.pdf).

![Visão no Celular](www/cnpjrfb/app/images/celular_empresa_visao.png?raw=true "Visão no Celular")

![Visão no Tablet](www/cnpjrfb/app/images/tablet.png?raw=true "Visão no Tablet")

## Projetos irmãos

Agradecimento para todas as pessoas que contribuíram de forma direta ou indireta para o projeto. Abaixo alguns nomes de destaque, pois o sistema de consulta foi baseado nos scripts de carga de dados das pessoas abaixo. Sem essas pessoas o trabalho seria bem mais difícil:

* Versão 2.0.0 ou superior, Aphonso Henrique do Amaral Rafael - https://github.com/aphonsoar/Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ
* Até a versão 1.2.0, Fabio Serpa. Hoje o projeto continua em https://github.com/bjverde/CNPJ-full, uma atualização do [CNPJ-full do Fabio Serpa](https://github.com/fabioserpa/CNPJ-full) para o layout novo da RFB

| Projeto | Linguagem | O que faz |
| --- | --- | --- |
| [bjverde/cnpjrfb](https://github.com/bjverde/cnpjrfb) | PHP | Este projeto: sistema web de consulta, carga e modelo de banco. |
| [aphonsoar/Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ](https://github.com/aphonsoar/Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ) | Python | Baixa os arquivos e faz a carga no PostgreSQL. Base do cnpjrfb desde a versão 2.0.0. |
| [bjverde/CNPJ-full](https://github.com/bjverde/CNPJ-full) | Python | Atualização do [CNPJ-full do Fabio Serpa](https://github.com/fabioserpa/CNPJ-full) para o layout novo da RFB. Converte os arquivos em SQLite ou CSV, faz consultas e gera o **grafo de relacionamentos**. Usa o mesmo modelo de banco do cnpjrfb. |

## Documentação

* [Instalação do sistema web](docs/instalacao.md): requisitos, cópia dos arquivos e configuração do banco
* [Carga dos dados](docs/carga.md): download dos arquivos da RFB, criação do banco e execução da carga
* [Modelo de banco](modelo_banco/README.md): tabelas, chaves e scripts por banco
* [Docker](docs/docker.md): ambiente pronto com Apache, PHP 8.2 e MariaDB
* [Segurança em produção](docs/seguranca.md): o que desligar antes de publicar o sistema
* [Desenvolvimento](docs/desenvolvimento.md): estrutura de pastas e como alterar o projeto
* [Dicionário de dados da RFB](docs/cnpj-metadados.pdf) (cnpj-metadados.pdf)

## Início rápido

O sistema vem com um pequeno banco SQLite de demonstração, então dá para ver as telas antes de fazer a carga completa:

```bash
git clone https://github.com/bjverde/cnpjrfb.git
cd cnpjrfb/www/cnpjrfb
php -S localhost:8080
# abra http://localhost:8080 no navegador
```

Para usar a base completa da RFB:

1. Crie o banco com o script do seu SGBD em [`modelo_banco/`](modelo_banco/README.md).
2. Baixe e descompacte os arquivos da RFB e rode a carga (veja [Carga dos dados](docs/carga.md)). Leva horas.
3. Aponte o sistema web para o novo banco em `www/cnpjrfb/app/config/maindatabase.php` (veja [Instalação](docs/instalacao.md#3-configurar-o-banco)).

## Requisitos

* PHP 8.1 ou superior, com `pdo`, `mbstring`, `pdo_sqlite` e o driver PDO do seu banco (`pdo_mysql`, `pdo_pgsql`, `pdo_sqlsrv` ou `pdo_oci`).
* Um servidor web (Apache, Nginx ou o servidor embutido do PHP para testes).
* Para a base completa, cerca de 50 GB livres em disco. Números de agosto de 2024:
    * 6,1 GB de arquivos ZIP da RFB, que podem ser apagados depois da carga;
    * 18 GB de arquivos descompactados, que também podem ser apagados depois;
    * 18 GB de banco de dados (PostgreSQL).

| Tabela | Linhas (ago/2024) |
| --- | --- |
| empresa | 45.811.638 |
| estabelecimento | 48.421.619 |
| socios | 20.426.417 |
| simples | 27.893.923 |
| **Total** | **142.553.597** |

## Grafo de relacionamentos

A geração do grafo está desativada no menu desde a versão 2.0.0. O grafo é gerado pelo [CNPJ-full](https://github.com/bjverde/CNPJ-full), que já foi atualizado para o layout novo da RFB. A integração das duas partes ainda está em andamento.

## Sobre o CPF dos sócios e a LGPD

A RFB publica o CPF dos sócios mascarado (ex.: `***123456**`), então uma busca por CPF pode trazer pessoas diferentes. Os dados incluem informações pessoais: use-os de acordo com a LGPD (Lei 13.709/2018).
