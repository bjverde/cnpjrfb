[← Voltar ao README](../README.md)

# Desenvolvimento

## Estrutura de pastas

```
cnpjrfb/
├── README.md
├── docs/                         documentação e dicionário de dados da RFB (cnpj-metadados.pdf)
├── modelo_banco/                 scripts de criação do banco
│   ├── gerar_modelos.py          gera todos os scripts a partir de uma única definição
│   ├── mysql/ postgresql/ sqlite/ sqlserver/ oracle/
├── www/
│   ├── cargabd/                  carga dos arquivos da RFB (PHP, só terminal)
│   │   ├── index.php             ponto de entrada (aceita --tabela=)
│   │   ├── config.php            conexão e pasta dos arquivos
│   │   ├── controllers/          Cargabanco (orquestra), UploadCsv (lê o CSV), TPDOConnection (conexão)
│   │   ├── dao/                  um DAO por tabela, com a lista de colunas na ordem do CSV
│   │   ├── helpers/              ConfigHelper, ValidarHelper (datas, códigos), StringHelper
│   │   └── download/             download_files.sh e unzip_files.sh
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
├── docker-compose.yml, adianti_debian12_php8.2.Dockerfile, php.ini
└── log_apache/, mariadb_database/, mariadb_logs/   dados do Docker (fora do Git)
```

## Alterar o modelo de banco

Os scripts de `modelo_banco/` são gerados. Não edite os `.sql` à mão: altere `modelo_banco/gerar_modelos.py` e rode

```bash
python3 modelo_banco/gerar_modelos.py
```

Se mudar colunas, atualize também:

* a lista de colunas do DAO da carga (`www/cargabd/dao/<Tabela>DAO.class.php`), que deve seguir a ordem do CSV da RFB;
* o Active Record do sistema web (`www/cnpjrfb/app/model/maindatabase/<tabela>.class.php`);
* o [CNPJ-full](https://github.com/bjverde/CNPJ-full) (`COLUNAS` em `cnpj_full/cnpj.py`), que usa o mesmo modelo.

## Rodar o sistema localmente

```bash
cd www/cnpjrfb
php -S localhost:8080
```

Para testar com dados próprios, carregue um SQLite com a carga (`db_drive` = `sqlite` em `www/cargabd/config.php`) e aponte `app/config/maindatabase.php` para o arquivo.

## Testar a carga

Ainda não há testes automáticos. Para conferir uma mudança na carga:

1. Monte uma pasta com poucos arquivos no formato da RFB: CSV sem cabeçalho, `;` como separador, campos entre aspas e codificação ISO-8859-1. Use nomes como `K3241.K03200Y0.D41012.EMPRECSV`.
2. Crie um banco de teste com o script de `modelo_banco/`.
3. Rode `php www/cargabd/index.php` e confira as contagens e os valores gravados.

Vale incluir casos como aspas e `;` dentro do texto, datas `00000000`, campos vazios e uma linha com colunas a menos.

## Projetos irmãos

* [bjverde/CNPJ-full](https://github.com/bjverde/CNPJ-full): carga em Python para SQLite/CSV, consultas e grafo. Usa o mesmo modelo de banco.
* [aphonsoar/Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ](https://github.com/aphonsoar/Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ): carga em Python para o PostgreSQL.
* [fabioserpa/CNPJ-full](https://github.com/fabioserpa/CNPJ-full): projeto original do CNPJ-full.
