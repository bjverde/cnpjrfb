[← Voltar ao README](../README.md)

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

A conexão fica em `www/cnpjrfb/app/config/maindatabase.php`:

```php
return [
     "host" => "127.0.0.1"
    ,"port" => "5432"
    ,"name" => "dados_rfb"
    ,"user" => "dados_rfb"
    ,"pass" => "troque_esta_senha"
    ,"type" => "pgsql"
    ,"prep" => "1"
    ,"slog" => "SystemSqlLogService"
];
```

| `type` | Banco | O que vai em `name` |
| --- | --- | --- |
| `mysql` | MySQL / MariaDB | nome do banco (`dados_rfb`, se usou o `modelo_mysql.sql`) |
| `pgsql` | PostgreSQL | nome do banco |
| `sqlite` | SQLite | caminho do arquivo `.db` |
| `sqlsrv` | SQL Server | nome do banco |
| `oracle` | Oracle | nome do serviço (ex.: `XEPDB1`) |

O sistema lê as tabelas sem schema. Se elas estiverem num schema que não é o padrão da conexão, informe o schema com ponto no final em `www/cnpjrfb/init.php`:

```php
define('DATABASE_SCHEMA','dados_rfb.');
```

| Banco | `DATABASE_SCHEMA` com os scripts de `modelo_banco/` |
| --- | --- |
| PostgreSQL | `'dados_rfb.'` (as tabelas ficam no schema `dados_rfb`) |
| SQL Server | `'dados_rfb.'` |
| MySQL / MariaDB | `''`, com `name` = `dados_rfb` (no MySQL, schema e banco são a mesma coisa) |
| SQLite | `''` |
| Oracle | `''` se conectar com o usuário dono das tabelas; senão `'DADOS_RFB.'` |

Mais detalhes sobre os tipos de conexão estão no [quickstart do Adianti](https://www.adianti.com.br/framework-quickstart).

## 4. Antes de publicar

O sistema não tem login, porque é uma consulta pública. Antes de deixá-lo acessível na internet, siga o checklist em [Segurança em produção](seguranca.md).
