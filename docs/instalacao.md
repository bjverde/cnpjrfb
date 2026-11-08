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

Onde fica cada coisa:

| O quê | Onde | Para trocar |
|-------|------|-------------|
| Código do CNPJ-full | `app/CNPJ-full` | não muda |
| Grafos gerados (`grafo.html` e `cache.json`) | `app/output/grafos/<cnpj>` | `'grafos'` na seção `cnpj_full` do `app/config/application.php` |
| Python | `python3` no Linux, `python` no Windows | `'python'` na seção `cnpj_full` do `app/config/application.php` (ex.: o Python de um venv) |

A pasta `app/output/grafos` já vem no projeto com um `.htaccess` com `Require all denied`, e o conteúdo dela é ignorado pelo git. A pasta `app/CNPJ-full` recebe o mesmo `.htaccess` na primeira vez que um grafo é gerado. Assim o Apache não serve o `.env`, o banco nem os arquivos gerados. A tela lê o `grafo.html` pelo PHP e o mostra na página. Se usar outro servidor web (nginx, por exemplo), bloqueie essas duas pastas na configuração dele.

No Linux, o usuário do Apache precisa poder gravar na pasta dos grafos:

```bash
chown -R www-data:www-data www/cnpjrfb/app/output/grafos
```

A tela repete a mesma consulta pelo cache: se o grafo daquele CNPJ já foi gerado com a mesma carga do banco, ele é reaproveitado.

## 5. Antes de publicar

O sistema não tem login, porque é uma consulta pública. Antes de deixá-lo acessível na internet, siga o checklist em [Segurança em produção](seguranca.md).
