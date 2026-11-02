# Modelos de banco de dados

Scripts de criação do banco para os dados abertos do CNPJ da Receita Federal, conforme o
"Novo Layout para os DADOS ABERTOS do CNPJ" (`cnpj-metadados.pdf`).

Todos os scripts são gerados por `gerar_modelos.py` a partir de uma única definição do modelo,
por isso as tabelas, colunas, tipos, chaves e índices são os mesmos em todos os bancos.
Para alterar o modelo, edite o gerador e rode:

```bash
python3 modelo_banco/gerar_modelos.py
```

| Pasta | Script de criação | Chaves estrangeiras | Testado |
|---|---|---|---|
| `mysql/` | `modelo_mysql.sql` | `modelo_mysql_fk.sql` | MariaDB 10.11 |
| `postgresql/` | `modelo_postgres.sql` | `modelo_postgres_fk.sql` | PostgreSQL 16 |
| `sqlite/` | `modelo_sqlite.sql` | declaradas na própria tabela | SQLite 3 |
| `sqlserver/` | `modelo_sqlserver.sql` | `modelo_sqlserver_fk.sql` | só sintaxe (SQL Server 2016+) |
| `oracle/` | `modelo_oracle.sql` | `modelo_oracle_fk.sql` | só sintaxe (Oracle 12c+) |

## Ordem de uso

1. Rode o script de criação.
2. Faça a carga dos dados (`www/cargabd`).
3. Opcional: rode o script `_fk.sql` para criar as chaves estrangeiras. Elas ficam separadas porque
   deixam a carga muito mais lenta e porque a carga grava `estabelecimento` e `socios` antes de `empresa`.

## Modelo

| Tabela | Chave primária | Observação |
|---|---|---|
| `empresa` | `cnpj_basico` | Uma linha por CNPJ básico (8 dígitos) |
| `estabelecimento` | `cnpj_basico`, `cnpj_ordem`, `cnpj_dv` | Matriz e filiais; FK para `empresa` |
| `simples` | `cnpj_basico` | FK para `empresa` |
| `socios` | `id` (gerado pelo banco) | Vários sócios por empresa; FK para `empresa` |
| `cnae`, `moti`, `munic`, `natju`, `pais`, `quals` | `codigo` | Tabelas de domínio |

Pontos de atenção na carga:

* `capital_social` é numérico (`DECIMAL(18,2)`). O arquivo usa vírgula decimal, que precisa ser trocada por ponto.
* Datas vêm como `AAAAMMDD`. Valores `0` ou `00000000` devem ser gravados como NULL.
* `situacao_cadastral` e `porte_empresa` são códigos de dois dígitos (`02`, `08`, `01`, `05`...).

O MER e o arquivo do MySQL Workbench em `mysql/` (`mer_mysql.*`, `modelo_workbench.mwb`) são do
modelo antigo e não refletem as chaves corrigidas.
