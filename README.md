# cnpjrfb

Sistema web em PHP, feito com o [Adianti Framework](https://www.adianti.com.br/framework) e [FormDin 5](https://github.com/bjverde/formDin5), para consultar os [dados públicos do CNPJ](https://dados.gov.br/dados/conjuntos-dados/cadastro-nacional-da-pessoa-juridica---cnpj) publicados pela Receita Federal do Brasil (RFB): empresas, estabelecimentos (matriz e filiais), sócios, Simples Nacional / MEI e as tabelas de apoio (CNAE, municípios, países, naturezas jurídicas, qualificações e motivos da situação cadastral).

O projeto tem três partes:

* **Sistema web** (`www/cnpjrfb`): telas de pesquisa com filtros combinados e ficha completa do CNPJ, adaptadas para celular, tablet ou desktop.
* **Carga**: feita pelo [CNPJ-full](https://github.com/bjverde/CNPJ-full), em Python, que baixa os arquivos da RFB e grava no banco com o mesmo modelo (veja [Carga dos dados](docs/carga.md)).
* **Modelo de banco** ([`docs/modelo_banco.md`](docs/modelo_banco.md) e pasta `docs/modelo_banco/`): scripts de criação das tabelas e chaves estrangeiras para os mesmos cinco bancos, todos com o mesmo modelo e seguindo o [dicionário de dados da RFB](docs/cnpj-metadados.pdf).

![Visão no Celular](www/cnpjrfb/app/images/celular_empresa_visao.png?raw=true "Visão no Celular")

![Visão no Tablet](www/cnpjrfb/app/images/tablet.png?raw=true "Visão no Tablet")

## Documentação

Consulte o **[Sumário da documentação](docs/sumario.md)** para a visão geral de todos os guias, ou acesse diretamente:

* [Sumário da documentação](docs/sumario.md): índice com resumo de todos os documentos em `docs/`
* [Instalação do sistema web](docs/instalacao.md): requisitos, cópia dos arquivos e configuração do banco
* [Carga dos dados](docs/carga.md): download dos arquivos da RFB e carga no banco com o CNPJ-full
* [Modelo de banco de dados](docs/modelo_banco.md): tabelas, chaves e scripts SQL por banco
* [Docker](docs/docker.md): ambiente de desenvolvimento com Apache, PHP 8.5 e PostgreSQL
* [Segurança em produção](docs/seguranca.md): checklist do que desligar antes de publicar o sistema
* [Desenvolvimento](docs/desenvolvimento.md): estrutura de pastas e como alterar o projeto
* [Dicionário de dados da RFB](docs/cnpj-metadados.pdf) (`cnpj-metadados.pdf`)

## Início rápido

O sistema vem com um pequeno banco SQLite de demonstração (`www/cnpjrfb/app/database/cnpjrfb.db`), então é possível testar as telas antes de fazer a carga completa:

```bash
git clone https://github.com/bjverde/cnpjrfb.git
cd cnpjrfb/www/cnpjrfb
php -S localhost:8080
# abra http://localhost:8080 no navegador
```

Para usar a base completa da RFB:

1. Crie o banco com o script do seu SGBD conforme o guia de [Modelo de banco de dados](docs/modelo_banco.md).
2. Baixe os arquivos da RFB e rode a carga com o CNPJ-full (veja [Carga dos dados](docs/carga.md)). Leva horas.
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
| `empresa` | 45.811.638 |
| `estabelecimento` | 48.421.619 |
| `socios` | 20.426.417 |
| `simples` | 27.893.923 |
| **Total** | **142.553.597** |

## Projetos irmãos e agradecimentos

Agradecimento a todas as pessoas que contribuíram de forma direta ou indireta para o projeto. O sistema de consulta e a estrutura de banco foram baseados nos scripts de carga de dados abaixo:

* **Versão 2.0.0 ou superior:** Aphonso Henrique do Amaral Rafael — [Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ](https://github.com/aphonsoar/Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ)
* **Até a versão 1.2.0:** Fabio Serpa — projeto continuado em [bjverde/CNPJ-full](https://github.com/bjverde/CNPJ-full), uma atualização do [CNPJ-full do Fabio Serpa](https://github.com/fabioserpa/CNPJ-full) para o layout novo da RFB.

| Projeto | Linguagem | O que faz |
| --- | --- | --- |
| [bjverde/cnpjrfb](https://github.com/bjverde/cnpjrfb) | PHP | Este projeto: sistema web de consulta, carga e modelo de banco. |
| [aphonsoar/Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ](https://github.com/aphonsoar/Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ) | Python | Baixa os arquivos e faz a carga no PostgreSQL. Base do cnpjrfb desde a versão 2.0.0. |
| [bjverde/CNPJ-full](https://github.com/bjverde/CNPJ-full) | Python | Atualização do [CNPJ-full do Fabio Serpa](https://github.com/fabioserpa/CNPJ-full) para o layout novo da RFB. Converte os arquivos em SQLite ou CSV, faz consultas e gera o **grafo de relacionamentos**. Usa o mesmo modelo de banco do cnpjrfb. |

## Grafo de relacionamentos

A geração do grafo está desativada no menu desde a versão 2.0.0. O grafo é gerado pelo [CNPJ-full](https://github.com/bjverde/CNPJ-full), que já foi atualizado para o layout novo da RFB. A integração das duas partes ainda está em andamento.

## Sobre o CPF dos sócios e a LGPD

A RFB publica o CPF dos sócios mascarado (ex.: `***123456**`), então uma busca por CPF pode trazer pessoas diferentes. Os dados incluem informações pessoais: use-os de acordo com a LGPD (Lei 13.709/2018).
