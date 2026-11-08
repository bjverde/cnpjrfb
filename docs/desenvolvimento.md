[← Voltar ao README](../README.md) | [Sumário da documentação](sumario.md)

# Desenvolvimento

## Estrutura de pastas

```
cnpjrfb/
├── README.md
├── docs/                         guias (sumario.md, instalacao.md, carga.md, modelo_banco.md...) e dicionário da RFB (cnpj-metadados.pdf)
│   ├── modelo_banco/             scripts de criação do banco
│   │   ├── mysql/ postgresql/ sqlite/ sqlserver/ oracle/
├── www/
│   └── cnpjrfb/                  sistema web (Adianti Framework + FormDin 5)
│       ├── index.php, engine.php, init.php, menu.xml
│       ├── app/config/           application.ini, maindatabase.php (conexão)
│       ├── app/control/maindatabase/
│       │   ├── views/            telas: listas (EmpresaList, EstabelecimentoList...) e fichas (cnpjFormView, SocioViewForm)
│       │   ├── controllers/      regras e tabelas de códigos (TipoEmpresaSituacao, TipoPorteEmpresa...)
│       │   └── dao/              consultas SQL do FormDin
│       ├── app/model/maindatabase/  Active Records do Adianti, um por tabela
│       ├── app/database/         cnpjrfb.db (demonstração) e log.db
│       ├── app/resources/        ajuda.html e modelos HTML
│       ├── lib/ e app/lib/       Adianti Framework e FormDin (não editar)
│       └── vendor/               dependências do Composer
├── docker-compose.yml, debian13_apache_php85_pgsql.Dockerfile, config/
└── logs/, postgres_database/     dados do Docker (fora do Git)
```

## Alterar o modelo de banco

Os scripts descritos em [`docs/modelo_banco.md`](modelo_banco.md) seguem o mesmo modelo em todos os bancos. Ao mudar uma tabela, altere os scripts de **todos** os bancos (MySQL, PostgreSQL, SQLite, SQL Server e Oracle) e os `_fk.sql` correspondentes.

Se mudar colunas, atualize também:

* o Active Record do sistema web (`www/cnpjrfb/app/model/maindatabase/<tabela>.class.php`);
* o [CNPJ-full](https://github.com/bjverde/CNPJ-full), que faz a carga e usa o mesmo modelo (colunas na ordem do CSV da RFB).

## Rodar o sistema localmente

```bash
cd www/cnpjrfb
php -S localhost:8080
```

Para testar com dados próprios, gere um SQLite com a carga do [CNPJ-full](carga.md) e aponte `app/config/maindatabase.php` para o arquivo.

## Projetos irmãos

* [aphonsoar/Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ](https://github.com/aphonsoar/Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ): carga em Python para o PostgreSQL.
* [bjverde/CNPJ-full](https://github.com/bjverde/CNPJ-full): atualização do [CNPJ-full do Fabio Serpa](https://github.com/fabioserpa/CNPJ-full) para o layout novo. Carga em Python para SQLite/CSV, consultas e grafo, com o mesmo modelo de banco.
