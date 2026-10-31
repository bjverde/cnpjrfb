[← Voltar ao README](../README.md)

# Carga dos dados

A carga é um script PHP de terminal, em `www/cargabd`. Ele lê os arquivos CSV da Receita Federal (RFB) e grava no banco criado com os scripts de [`modelo_banco/`](../modelo_banco/README.md). Funciona com MySQL/MariaDB, PostgreSQL, SQLite, SQL Server e Oracle.

A carga completa tem cerca de 140 milhões de linhas e leva horas, dependendo do computador e do banco.

Se preferir carregar com Python:

* o [CNPJ-full](https://github.com/bjverde/CNPJ-full) gera um banco SQLite com as mesmas tabelas deste projeto;
* o [Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ](https://github.com/aphonsoar/Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ) faz a carga no PostgreSQL.

## Passo a passo

1. [Baixar os arquivos da RFB](#1-baixar-os-arquivos-da-rfb)
2. [Descompactar](#2-descompactar)
3. [Criar o banco](#3-criar-o-banco)
4. [Configurar a carga](#4-configurar-a-carga)
5. [Rodar a carga](#5-rodar-a-carga)
6. [Criar as chaves estrangeiras](#6-criar-as-chaves-estrangeiras-opcional)

## 1. Baixar os arquivos da RFB

A RFB publica os dados todo mês em `https://arquivos.receitafederal.gov.br/dados/cnpj/dados_abertos_cnpj/`, com uma pasta por competência (ex.: `2026-10/`). O acesso também está no [portal dados.gov.br](https://dados.gov.br/dados/conjuntos-dados/cadastro-nacional-da-pessoa-juridica---cnpj).

Baixe **todos os ZIPs da mesma competência**:

| Arquivos | Tabela |
| --- | --- |
| `Empresas0.zip` a `Empresas9.zip` | `empresa` |
| `Estabelecimentos0.zip` a `Estabelecimentos9.zip` | `estabelecimento` |
| `Socios0.zip` a `Socios9.zip` | `socios` |
| `Simples.zip` | `simples` |
| `Cnaes.zip`, `Motivos.zip`, `Municipios.zip`, `Naturezas.zip`, `Paises.zip`, `Qualificacoes.zip` | `cnae`, `moti`, `munic`, `natju`, `pais`, `quals` |

No Linux há um script que baixa tudo de uma vez:

```bash
cd www/cargabd/download
./download_files.sh 2026-10                  # baixa para esta pasta
./download_files.sh 2026-10 /outra/pasta     # ou para outra pasta
```

Se o download for interrompido, rode o mesmo comando de novo: ele continua de onde parou.

## 2. Descompactar

```bash
cd www/cargabd/download
./unzip_files.sh                              # ZIPs desta pasta -> ../extracted
./unzip_files.sh /pasta/zips /pasta/extraidos # ou informe as pastas
```

Cada ZIP tem um CSV sem cabeçalho, separado por `;`, com campos entre aspas e codificação ISO-8859-1, conforme o [dicionário de dados da RFB](cnpj-metadados.pdf). A carga reconhece o tipo pelo nome do arquivo (`...EMPRECSV`, `...ESTABELE`, `...SOCIOCSV`, `...SIMPLES.CSV...`, `...CNAECSV` etc.), inclusive dentro de subpastas.

## 3. Criar o banco

Rode o script de criação do seu banco, que está em [`modelo_banco/`](../modelo_banco/README.md). Por exemplo:

```bash
mysql -u root -p < modelo_banco/mysql/modelo_mysql.sql
psql -d dados_rfb -f modelo_banco/postgresql/modelo_postgres.sql
sqlite3 dados_rfb.db < modelo_banco/sqlite/modelo_sqlite.sql
```

Não rode ainda os scripts `*_fk.sql`: as chaves estrangeiras são criadas no [passo 6](#6-criar-as-chaves-estrangeiras-opcional).

## 4. Configurar a carga

Edite `www/cargabd/config.php`:

```php
return [
     'db_drive' => 'pgsql'      // mysql, pgsql, sqlite, sqlsrv ou oci
    ,'db_host' => '127.0.0.1'
    ,'db_port' => ''            // vazio = porta padrão do banco
    ,'db_name' => 'dados_rfb'   // SQLite: caminho do arquivo. Oracle: nome do serviço
    ,'db_user' => 'dados_rfb'
    ,'db_password' => 'troque_esta_senha'
    ,'db_schema' => 'dados_rfb' // ignorado no SQLite
    ,'linhas_por_transacao' => 10000
    ,'EXTRACTED_FILES_PATH'=>'/var/opt/dados_receita/extracted'
];
```

| Opção | Descrição |
| --- | --- |
| `db_drive` | Driver PDO: `mysql` (MySQL/MariaDB), `pgsql`, `sqlite`, `sqlsrv` (SQL Server) ou `oci` (Oracle). |
| `db_schema` | Schema das tabelas. O padrão é `dados_rfb`, igual aos scripts de `modelo_banco/`. No MySQL o schema é o banco criado pelo `modelo_mysql.sql`. No Oracle use `''` se conectar com o usuário dono das tabelas. No SQLite é ignorado. |
| `linhas_por_transacao` | Quantas linhas gravar antes de cada `COMMIT`. O padrão é 10000. |
| `EXTRACTED_FILES_PATH` | Pasta com os arquivos descompactados. |

## 5. Rodar a carga

```bash
php www/cargabd/index.php
```

Para rodar em segundo plano e guardar o resultado:

```bash
nohup php www/cargabd/index.php > carga_resultado.txt 2> carga_erros.txt &
```

**A carga apaga as tabelas antes de gravar.** Para refazer só algumas tabelas:

```bash
php www/cargabd/index.php --tabela=empresa,socios
```

Tabelas aceitas: `cnae`, `moti`, `munic`, `natju`, `pais`, `quals`, `estabelecimento`, `socios`, `empresa` e `simples`.

### O que a carga faz com os dados

* Campo vazio vira `NULL`.
* Datas `AAAAMMDD` são gravadas como `AAAA-MM-DD`. Datas vazias, zeradas (`0`, `00000000`) ou inválidas viram `NULL`.
* `capital_social` tem a vírgula decimal trocada por ponto.
* `situacao_cadastral` e `porte_empresa` são gravados com dois dígitos (`2` vira `02`).
* O texto é convertido de ISO-8859-1 para UTF-8.

### Resultado e erros

Para cada arquivo, a carga mostra quantas linhas foram lidas, gravadas e descartadas. No fim, mostra os totais.

* **Linha com número de colunas errado:** é descartada e registrada em `www/cargabd/error_log.txt` com o arquivo e o número da linha. A carga continua.
* **Erro do banco** (conexão, tipo inválido, chave duplicada): a carga para, desfaz a transação em andamento e mostra o arquivo e a linha. O código de saída é 1. Corrija o problema e rode de novo só as tabelas que faltam com `--tabela=`.

## 6. Criar as chaves estrangeiras (opcional)

Depois da carga, rode o script `_fk.sql` do seu banco (ex.: `modelo_banco/postgresql/modelo_postgres_fk.sql`). As chaves ficam separadas por dois motivos: deixam a carga muito mais lenta, e a carga grava `estabelecimento` e `socios` antes de `empresa`.

Para recarregar depois que as FKs existirem, apague as FKs antes. No PostgreSQL, no SQL Server e no Oracle, o `TRUNCATE` ou `DELETE` falha se houver FK apontando para a tabela. No MySQL as FKs são desligadas durante a carga.

## Dicas de desempenho

* Rode a carga na mesma máquina do banco, ou numa rede rápida.
* Os índices secundários são criados junto com as tabelas. Para uma carga mais rápida, crie as tabelas sem os `CREATE INDEX` e rode esses comandos depois da carga.
* No PostgreSQL, aumentar `max_wal_size` durante a carga reduz o tempo gasto com checkpoints.
