[← Voltar ao README](../README.md)

# Docker

O `docker-compose.yml` da raiz sobe um ambiente de desenvolvimento com quatro serviços:

| Serviço | Imagem | Porta | Para quê |
| --- | --- | --- | --- |
| `apache_php` | `adianti_debian12_php8.2.Dockerfile` (Debian 12, Apache, PHP 8.2 com drivers MySQL, PostgreSQL, SQLite e SQL Server, Composer e Xdebug) | 80, 443, 9003 (Xdebug) | Sistema web e carga |
| `mariadb` | `mariadb:10.11` | 3306 | Banco de dados |
| `adminer` | `adminer` | 8080 | Administração do banco pelo navegador |
| `phpmyadmin` | `phpmyadmin` | 9090 | Administração do MariaDB pelo navegador |

A configuração **ainda não está completa**: ela sobe o PHP e o MariaDB, mas não cria o banco nem configura o sistema para usá-lo.

## Subindo o ambiente

```bash
docker compose build
docker compose up -d
```

* O sistema abre em `http://localhost/cnpjrfb`, com o banco SQLite de demonstração.
* A pasta `www/` do projeto é montada em `/var/www/html`, então as alterações no código aparecem na hora.
* O `php.ini` da raiz é montado no container (`/etc/php/8.2/apache2/php.ini`).
* Os dados do MariaDB ficam em `mariadb_database/` e os logs em `mariadb_logs/` e `log_apache/`. Essas pastas não vão para o Git.

Para entrar no container:

```bash
docker compose exec apache_php bash
```

## Fazendo a carga no MariaDB do Docker

1. Crie as tabelas:
   ```bash
   docker compose exec -T mariadb mariadb -uroot -pp123456 < modelo_banco/mysql/modelo_mysql.sql
   ```
2. Coloque os arquivos descompactados da RFB em `www/cargabd/extracted/`. Veja os passos 1 e 2 de [Carga dos dados](carga.md).
3. Em `www/cargabd/config.php`, use `db_host` = `mariadb`, `db_user` = `root`, `db_password` = `p123456` e `EXTRACTED_FILES_PATH` = `/var/www/html/cargabd/extracted`.
4. Rode a carga dentro do container:
   ```bash
   docker compose exec apache_php php /var/www/html/cargabd/index.php
   ```
5. Aponte o sistema para o MariaDB em `www/cnpjrfb/app/config/maindatabase.php`, com `host` = `mariadb`, `name` = `dados_rfb` e `type` = `mysql` (veja [Instalação](instalacao.md#3-configurar-o-banco)).

## Atenção: é um ambiente de desenvolvimento

Não use este `docker-compose.yml` como está num servidor acessível pela rede:

* a senha do root do MariaDB (`p123456`) está no arquivo e a porta 3306 fica aberta;
* Adminer, phpMyAdmin e Xdebug ficam abertos em todas as interfaces;
* o Xdebug está sempre ligado, o que deixa cada requisição mais lenta;
* a pasta `www/` inteira fica publicada, incluindo a carga e os arquivos da RFB.

Veja [Segurança em produção](seguranca.md).
