[← Voltar ao README](../README.md) | [Sumário da documentação](sumario.md)

# Instalação do sistema web

O sistema web fica em `www/cnpjrfb`. Ele roda com o banco SQLite de demonstração que vem no projeto, e para a base completa basta apontá-lo para o banco onde a [carga](carga.md) foi feita.

## 1. Requisitos

* PHP 8.1 ou superior.
* Extensões do PHP: `pdo`, `mbstring`, `pdo_sqlite` (o log do sistema usa SQLite) e o driver do banco com a base completa:

| Banco | Extensão PDO |
| --- | --- |
| MySQL / MariaDB | `pdo_mysql` |
| PostgreSQL | `pdo_pgsql` |
| SQLite | `pdo_sqlite` |
| SQL Server | `pdo_sqlsrv` (driver da Microsoft) |
| Oracle | `pdo_oci` (precisa do Oracle Instant Client; no PHP 8.4 ou superior vem do PECL) |

* Um servidor web. Pode ser Apache, Nginx ou, para testes, o servidor embutido do PHP (`php -S`).
* As outras configurações do PHP são as do [Adianti Framework](https://www.adianti.com.br/framework-quickstart). O sistema exige Adianti 7.6.0 e FormDin 5.1.1 ou superiores, que já vêm no projeto (`www/cnpjrfb/lib` e `www/cnpjrfb/app/lib`).

Para ver se falta alguma extensão, abra o menu **Ajuda > Ver módulos** do sistema.

## 2. Copiar os arquivos

1. Crie no servidor web a pasta `cnpjrfb`.
2. Copie para ela o conteúdo de `www/cnpjrfb` do projeto.
3. Dê ao usuário do servidor web permissão de escrita na pasta. No Debian/Ubuntu com Apache:
   ```bash
   sudo chown -R www-data:www-data /var/www/html/cnpjrfb
   ```
4. Abra `http://<servidor>/cnpjrfb` no navegador. Com o banco de demonstração, os menus **Tabelas** e **Tabelas Apoio** já mostram dados.

Para testar sem Apache:

```bash
cd www/cnpjrfb
php -S localhost:8080
```

## 3. Configurar o banco

A conexão vem do **`.env` do CNPJ-full**, o mesmo arquivo que o Python usa, para a configuração do banco ficar num lugar só. O `www/cnpjrfb/app/config/maindatabase.php` procura esse arquivo na pasta informada em `app/config/application.php` > `'cnpj_full'` > `'path'` (padrão `app/CNPJ-full`, relativo a `www/cnpjrfb`). Detalhes em [Arquitetura](arquitetura.md#conexão-única-o-php-lendo-o-env-do-python).

* **O `.env` é obrigatório.** Sem ele, as telas mostram o erro com o caminho onde o arquivo era esperado.
* **Para usar o SQLite que já vem no projeto** (`app/database/cnpjrfb.db`), o `.env` precisa só de:

```ini
CNPJ_DB_TIPO=sqlite
CNPJ_DB_ARQUIVO=../database/cnpjrfb.db
```

* **Com outro banco**: crie o `.env` a partir do `.env.example` do CNPJ-full e preencha o bloco do banco. Exemplo para PostgreSQL:

```ini
CNPJ_DB_TIPO=postgres
CNPJ_DB_HOST=127.0.0.1
CNPJ_DB_PORTA=5432
CNPJ_DB_NOME=dados_rfb
CNPJ_DB_USUARIO=dados_rfb
CNPJ_DB_SENHA=troque_esta_senha
CNPJ_DB_SCHEMA=dados_rfb
```

| `CNPJ_DB_TIPO` | Banco | O que vai em `CNPJ_DB_NOME` |
| --- | --- | --- |
| `mysql` | MySQL / MariaDB | nome do banco (`dados_rfb`, se usou o `modelo_mysql.sql`) |
| `postgres` | PostgreSQL | nome do banco |
| `sqlite` | SQLite | não usa: o arquivo vai em `CNPJ_DB_ARQUIVO` (relativo à pasta do CNPJ-full, ou absoluto) |
| `sqlserver` | SQL Server | nome do banco |
| `oracle` | Oracle | nome do serviço (ex.: `XEPDB1`) |

> [!WARNING]
> O `.env.example` do CNPJ-full já traz `CNPJ_DB_ARQUIVO=dados/teste/cnpj_teste.db` (dados fictícios). Se o `.env` for só uma cópia do exemplo, o sistema web também passa a usar esse banco de teste.

O sistema lê as tabelas sem schema. Se elas estiverem num schema que não é o padrão da conexão, informe o schema com ponto no final em `www/cnpjrfb/init.php`:

```php
define('DATABASE_SCHEMA','dados_rfb.');
```

| Banco | `DATABASE_SCHEMA` com os scripts de [`docs/modelo_banco.md`](modelo_banco.md) |
| --- | --- |
| PostgreSQL | `'dados_rfb.'` (as tabelas ficam no schema `dados_rfb`) |
| SQL Server | `'dados_rfb.'` |
| MySQL / MariaDB | `''`, com `name` = `dados_rfb` (no MySQL, schema e banco são a mesma coisa) |
| SQLite | `''` |
| Oracle | `''` se conectar com o usuário dono das tabelas; senão `'DADOS_RFB.'` |

Mais detalhes sobre os tipos de conexão estão no [quickstart do Adianti](https://www.adianti.com.br/framework-quickstart).

## 4. Grafo de relacionamentos (opcional)

O grafo é gerado pelo [CNPJ-full](https://github.com/bjverde/CNPJ-full), em Python. O código dele fica dentro do sistema web, na pasta `www/cnpjrfb/app/CNPJ-full`, que o git do cnpjrfb ignora. O nome tem que ser exatamente `CNPJ-full`, com maiúsculas, que é o nome que o `git clone` cria. No Linux, `cnpj-full` não funciona.

1. Baixe o CNPJ-full dentro da pasta `app`:

    ```bash
    cd www/cnpjrfb/app
    git clone https://github.com/bjverde/CNPJ-full.git
    ```

    Para atualizar depois, rode `git pull` dentro de `app/CNPJ-full`.
2. Instale o Python 3.13 e as bibliotecas do CNPJ-full, conforme [Ambientes](https://github.com/bjverde/CNPJ-full/blob/master/docs/ambientes.md). No Docker do cnpjrfb isso já vem pronto (veja [Docker](docker.md#-python-e-cnpj-full-no-container)).
3. Informe ao CNPJ-full onde está o banco, no `.env` dele (`app/CNPJ-full/.env`, variável `CNPJ_DB_ARQUIVO`). Sem isso, ele usa o banco de teste que vem junto.

O comando que a tela executa é configurado na seção `cnpj_full` do `app/config/application.php`. Ele é versionado e vale igual no Docker, no Linux, no Windows e no Mac. Caminhos relativos são a partir da pasta `www/cnpjrfb`. O Python chamado é `python` no Windows e `python3` no Linux e no Mac; a versão é conferida pelo próprio CNPJ-full.

| Chave | Padrão | O que é |
|-------|--------|---------|
| `path` | `app/CNPJ-full` | pasta do clone do CNPJ-full |
| `script` | `cnpj_full/consulta.py` | script de consulta, relativo à pasta do CNPJ-full |
| `nivel` | `3` | profundidade do grafo (`--nivel`) |
| `cache` | `1` | `1` reaproveita o grafo já gerado com a mesma carga do banco (`--cache`); `0` gera sempre |
| `grafos` | `app/output/grafos` | onde ficam os grafos gerados (`<cnpj>/grafo.html` e `cache.json`) |

A pasta `app/output/grafos` já vem no projeto, com o conteúdo ignorado pelo git. Ela é servida pelo Apache: a tela abre o `grafo.html` gerado num iframe e num link para nova aba.

A pasta `app/CNPJ-full` recebe um `.htaccess` com `Require all denied` na primeira vez que um grafo é gerado, para o Apache não servir o `.env` nem o banco. Se usar outro servidor web (nginx, por exemplo), bloqueie essa pasta na configuração dele.

No Linux, o usuário do Apache precisa poder gravar na pasta dos grafos:

```bash
chown -R www-data:www-data www/cnpjrfb/app/output/grafos
```

## 5. Antes de publicar

O sistema não tem login, porque é uma consulta pública. Antes de deixá-lo acessível na internet, siga o checklist em [Segurança em produção](seguranca.md).
