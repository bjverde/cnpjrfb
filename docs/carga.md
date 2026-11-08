[← Voltar ao README](../README.md) | [Sumário da documentação](sumario.md)

# Carga dos dados

O download e a carga dos arquivos da Receita Federal (RFB) são feitos pelo [CNPJ-full](https://github.com/bjverde/CNPJ-full), em Python. Ele grava as mesmas tabelas do [modelo de banco](modelo_banco.md) deste projeto, então o sistema web lê o banco gerado sem nenhuma conversão.

A carga completa tem cerca de 140 milhões de linhas e leva horas, dependendo do computador e do banco.

## Passo a passo

1. **Baixar os arquivos da RFB** com o `download.py`: veja [Download](https://github.com/bjverde/CNPJ-full/blob/master/docs/download.md). Baixe todos os ZIPs da mesma competência e não precisa descompactar.
2. **Rodar a carga** com o `carga.py`:
    * SQLite ou CSV: veja [Carga](https://github.com/bjverde/CNPJ-full/blob/master/docs/carga.md);
    * PostgreSQL: veja [Carga no PostgreSQL](https://github.com/bjverde/CNPJ-full/blob/master/docs/postgresql.md).
3. **Apontar o sistema web** para o banco carregado, no `.env` do CNPJ-full (lido pelo `www/cnpjrfb/app/config/maindatabase.php`) (veja [Instalação](instalacao.md#3-configurar-o-banco)).

Para preparar o ambiente (Windows, Linux, WSL ou Docker) e testar antes com um banco pequeno, veja [Ambientes](https://github.com/bjverde/CNPJ-full/blob/master/docs/ambientes.md) no CNPJ-full.

## Chaves estrangeiras

Se você criar o banco à mão com os scripts de [`docs/modelo_banco/`](modelo_banco.md), rode o script `_fk.sql` do seu banco só depois da carga (ex.: `docs/modelo_banco/postgresql/modelo_postgres_fk.sql`). As chaves ficam separadas porque deixam a carga muito mais lenta e porque a carga grava `estabelecimento` e `socios` antes de `empresa`.

Para recarregar depois que as FKs existirem, apague as FKs antes. No PostgreSQL, no SQL Server e no Oracle, o `TRUNCATE` ou `DELETE` falha se houver FK apontando para a tabela.

## Outros projetos

* O [Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ](https://github.com/aphonsoar/Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ) também faz a carga no PostgreSQL, em Python.
