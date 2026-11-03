[← Voltar ao README](../README.md) | [Sumário da documentação](sumario.md)

# Modelos de banco de dados

Scripts de criação do banco para os dados abertos do CNPJ da Receita Federal, conforme o
"Novo Layout para os DADOS ABERTOS do CNPJ" ([`cnpj-metadados.pdf`](cnpj-metadados.pdf)).

Os scripts de todos os bancos seguem o mesmo modelo: as tabelas, colunas, tipos, chaves e índices
são os mesmos. Ao alterar uma tabela, altere os scripts de todos os bancos.

A criação e a carga do banco vão passar a ser feitas pelo [CNPJ-full](https://github.com/bjverde/CNPJ-full),
que usa este mesmo modelo. Estes scripts ficam aqui como referência e para quem quiser criar o banco à mão.

| Pasta | Script de criação | Chaves estrangeiras | Testado |
|---|---|---|---|
| `modelo_banco/mysql/` | [`modelo_mysql.sql`](modelo_banco/mysql/modelo_mysql.sql) | [`modelo_mysql_fk.sql`](modelo_banco/mysql/modelo_mysql_fk.sql) | MariaDB 10.11 |
| `modelo_banco/postgresql/` | [`modelo_postgres.sql`](modelo_banco/postgresql/modelo_postgres.sql) | [`modelo_postgres_fk.sql`](modelo_banco/postgresql/modelo_postgres_fk.sql) | PostgreSQL 16 |
| `modelo_banco/sqlite/` | [`modelo_sqlite.sql`](modelo_banco/sqlite/modelo_sqlite.sql) | declaradas na própria tabela | SQLite 3 |
| `modelo_banco/sqlserver/` | [`modelo_sqlserver.sql`](modelo_banco/sqlserver/modelo_sqlserver.sql) | [`modelo_sqlserver_fk.sql`](modelo_banco/sqlserver/modelo_sqlserver_fk.sql) | só sintaxe (SQL Server 2016+) |
| `modelo_banco/oracle/` | [`modelo_oracle.sql`](modelo_banco/oracle/modelo_oracle.sql) | [`modelo_oracle_fk.sql`](modelo_banco/oracle/modelo_oracle_fk.sql) | só sintaxe (Oracle 12c+) |

## Ordem de uso

1. Rode o script de criação do banco escolhido.
2. Faça a carga dos dados (hoje com `www/cargabd`, conforme [`docs/carga.md`](carga.md); depois, com o CNPJ-full).
3. Opcional: rode o script `_fk.sql` para criar as chaves estrangeiras. Elas ficam separadas porque
   deixam a carga muito mais lenta e porque a carga grava `estabelecimento` e `socios` antes de `empresa`.

## Modelo

| Tabela | Chave primária | Observação |
|---|---|---|
| `empresa` | `cnpj_basico` | Uma linha por CNPJ básico (8 dígitos) |
| `estabelecimento` | `cnpj_basico`, `cnpj_ordem`, `cnpj_dv` | Matriz e filiais; FK para `empresa` |
| `simples` | `cnpj_basico` | Opção pelo Simples Nacional e MEI; FK para `empresa` |
| `socios` | `id` (gerado pelo banco) | Quadro societário (vários sócios por empresa); FK para `empresa` |
| `cnae`, `moti`, `munic`, `natju`, `pais`, `quals` | `codigo` | Tabelas de domínio (apoio) |

Pontos de atenção na carga:

* `capital_social` é numérico (`DECIMAL(18,2)`). O arquivo usa vírgula decimal, que precisa ser trocada por ponto.
* Datas vêm como `AAAAMMDD`. Valores `0` ou `00000000` devem ser gravados como `NULL`.
* `situacao_cadastral` e `porte_empresa` são códigos de dois dígitos (`02`, `08`, `01`, `05`...).

O MER e o arquivo do MySQL Workbench em `modelo_banco/mysql/` ([`mer_mysql.pdf`](modelo_banco/mysql/mer_mysql.pdf), [`mer_mysql.png`](modelo_banco/mysql/mer_mysql.png), [`modelo_workbench.mwb`](modelo_banco/mysql/modelo_workbench.mwb)) são do
modelo antigo e não refletem as chaves corrigidas. A referência oficial são os scripts `.sql`.
