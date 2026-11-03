[← Voltar ao README](../README.md) | [Sumário da documentação](sumario.md)

# 🐳 Docker PHP 8.5 + PostgreSQL + pgAdmin (Debian 13)

O `docker-compose.yml` da raiz sobe um ambiente de desenvolvimento com quatro serviços:

Ambiente Docker completo baseado em **Debian 13**, **Apache 2.4.X**, **PHP 8.5**, **PostgreSQL** e **pgAdmin 4**, pronto para desenvolvimento local.

> [!WARNING]
> **Ambiente de Desenvolvimento (com Xdebug Ativo):**
> Este ambiente vem configurado para facilitar o desenvolvimento e depuração local (com **Xdebug ativado** e exibição de erros ativa no Apache). Para uso em **produção**, desative o Xdebug em `config/xdebug.ini`, mude `display_errors = Off` em `config/php-apache.ini`, remova a porta `9003` e altere as senhas padrão no `docker-compose.yml`.

---

## 📦 Detalhamento do Ambiente

* **Sistema Operacional:** Debian 13 (`trixie`)
  * Utilitários: `plocate`, `wget`, `curl`, `apt-transport-https`, `lsb-release`, `vim`, `rpl`, `zip`, `unzip`, `gnupg`
* **Servidor Web:** Apache 2.4.X
  * `mod_rewrite` ativado e `AllowOverride All` habilitado para leitura de `.htaccess`
  * Módulo LDAP (`authnz_ldap` e `ldap`) habilitado
* **PHP 8.5.X:**
  * **Drivers PDO de Banco de Dados:**
    * PostgreSQL (`pdo_pgsql` e `pgsql`)
    * MySQL / MariaDB (`pdo_mysql` e `mysql`)
    * SQLite (`pdo_sqlite` e `sqlite3`)
    * Microsoft SQL Server (`sqlsrv` e `pdo_sqlsrv` 5.13.0 + Microsoft ODBC Driver 18)
  * **NoSQL:** Extensão MongoDB (`php8.5-mongodb`)
  * **Extensões adicionais:** `mbstring`, `curl`, `dom`, `json`, `xml`, `zip`, `gd`, `soap`, `intl`, `xsl`, `ldap`
  * **Xdebug 3.X:** **ATIVADO por padrão** (controlado via `config/xdebug.ini`)
* **Ferramentas de Desenvolvimento:**
  * Composer
  * PHPUnit 13.X
  * Git
* **Containers Adicionais no `docker-compose.yml`:**
  * **PostgreSQL (`database`):** Banco de dados relacional na porta `5432` com persistência em `./postgres_database`
  * **pgAdmin 4 (`pgadmin`):** Interface web de administração rodando em [http://localhost:8080](http://localhost:8080)

---

## 🚀 Subindo o Ambiente

Na raiz desta pasta (`debian13_php8.5_pgsql`), execute:

```bash
docker-compose -f docker-compose.yml up -d --build
```

### 🔗 Endereços e Credenciais Padrão

| Serviço | URL / Host | Usuário | Senha | Observação |
| :--- | :--- | :--- | :--- | :--- |
| **Apache / PHP** | [http://localhost](http://localhost) | — | — | Página inicial com links e listagem de projetos em `www/` |
| **PHPInfo** | [http://localhost/info.php](http://localhost/info.php) | — | — | Exibe todas as extensões e diretivas do PHP |
| **pgAdmin 4 (Web)** | [http://localhost:8080](http://localhost:8080) | `user@domain.com` | `123456` | Login para entrar na interface web do pgAdmin |
| **PostgreSQL (Banco)** | Host interno: `database` (ou `localhost:5432` no host) | `postgres` | `postgres` | Banco padrão: `postgres` (alterável no `docker-compose.yml`) |

---

## ⏱️ Tempo Estimado de Build e Inicialização (Benchmark)

Abaixo estão os tempos reais medidos ao executar `docker-compose -f docker-compose.yml up -d --build` do zero (sem imagens ou cache prévio no Docker):

### 💻 Ambiente de Teste
* **Processador:** 12th Gen Intel® Core™ i7-12700H (14 núcleos / 20 threads)
* **Memória RAM:** 32 GB
* **Sistema Operacional:** Windows 11 (Docker Desktop com backend WSL 2)
* **Conexão de Internet (Download):** ~92,28 Mbps

### 📊 Detalhamento do Tempo por Etapa (1ª Execução — Sem Cache)

| Etapa do Processo | Tempo Medido | Detalhes do que é executado |
| :--- | :---: | :--- |
| **1. Download das imagens (`pull`)** | **~1 min 08 s** (`68s`) | Download e extração das imagens oficiais `postgres`, `dpage/pgadmin4` e `debian:13` |
| **2. Sistema Base Debian 13 (`apt`)** | **~25,3 s** | `apt-get update/upgrade` e utilitários (`plocate`, `curl`, `wget`, `vim`, `rpl`, `gnupg`, `tzdata`) |
| **3. Apache 2 + PHP 8.5 e Extensões** | **~1 min 08 s** (`68,5s`) | Repositório Sury, Apache 2, PHP 8.5, `curl`, `dom/xml/zip/soap/intl/xsl`, `mbstring`, `gd`, `ldap`, `mongodb` e PDO (`pgsql`, `mysql`, `sqlite`) |
| **4. Ferramentas Dev e Xdebug** | **~15,9 s** | Instalação do `git`, `composer`, `phpunit-13` e `php8.5-xdebug` |
| **5. Drivers SQL Server (ODBC 18 + PECL)** | **~1 min 23 s** (`82,8s`) | `php8.5-dev` (`34,5s`), `msodbcsql18` (`6,3s`) e compilação C/C++ via PECL do `sqlsrv-5.13.0` (`16,3s`) e `pdo_sqlsrv-5.13.0` (`17,1s`) |
| **6. Exportação da Imagem Docker** | **~25,2 s** | Empacotamento e descompactação das camadas da imagem `debian13_apache_php85_pgsql:last` |
| **7. Subida dos Containers + Healthcheck** | **~2,5 s** | Criação da rede `formdin-network`, inicialização do `postgres` (`Healthy`), `pgAdmin` e `apache_php` |
| **TOTAL (1ª execução do zero)** | **~5 min 20 s** | **Download completo + compilação das 49 etapas do `Dockerfile` + subida dos 3 containers** |
| **Execuções seguintes (com cache)** | **~8 a 10 s** | Quando a imagem já está construída localmente |

---

## ⚙️ Estrutura de Pastas e Configurações (`config/`)

Todos os arquivos de configuração, dados e logs ficam organizados em pastas dedicadas e mapeados via volumes no [`docker-compose.yml`](docker-compose.yml):

```text
debian13_php8.5_pgsql/
├── config/
│   ├── php-apache.ini       # Configurações do PHP para o Apache (/etc/php/8.5/apache2/php.ini)
│   ├── php-cli.ini          # Configurações do PHP para o terminal CLI (/etc/php/8.5/cli/php.ini)
│   ├── xdebug.ini           # Configuração do Xdebug (/etc/php/8.5/apache2/conf.d/20-xdebug.ini)
│   ├── apache_default.conf  # VirtualHost padrão do Apache (/etc/apache2/sites-enabled/000-default.conf)
│   ├── servers.json         # Pré-cadastro automático do servidor PostgreSQL no pgAdmin 4
│   └── postgresql.conf      # Configurações customizadas opcionais para o PostgreSQL
├── logs/                    # Logs de acesso e erro do Apache (access.log, error.log)
├── postgres_database/       # Dados persistidos do banco PostgreSQL (/var/lib/postgresql/data)
├── www/                     # Código-fonte dos seus projetos (/var/www/html)
├── debian13_apache_php85_pgsql.Dockerfile
└── docker-compose.yml
```

### 1. Persistência e Logs do PostgreSQL (`postgres_database/`)
* **Persistência (`./postgres_database`):** Ativada por padrão no `docker-compose.yml` com `PGDATA=/var/lib/postgresql/data/pgdata`, garantindo que seus bancos e tabelas não sejam perdidos ao parar ou recriar o container.
* **Logs do PostgreSQL:** Segue o padrão oficial da imagem Docker (`logging_collector=off`), enviando os logs para a saída padrão do Docker (acompanhe via `docker logs -f postgres` ou pelo painel do Docker Desktop). Caso queira gravar em arquivo (`./logs/postgresql.log`), basta descomentar o volume `./logs:/var/log/postgresql:rw` e o bloco `command` no [`docker-compose.yml`](docker-compose.yml).

### 2. `config/php-apache.ini` e `config/php-cli.ini`
Permitem customizar separadamente o comportamento do PHP no servidor web (`php-apache.ini`) e na linha de comando (`php-cli.ini`). Após editar, basta reiniciar o container:
```bash
docker-compose restart apache_php
```
**Configurações mais comuns no `config/php-apache.ini`:**
* `display_errors = On` (linha 503) — exibe erros na tela durante o desenvolvimento (mude para `Off` em produção).
* `error_reporting = E_ALL` (linha 486) — nível de detalhamento dos erros.
* `post_max_size = 80M` (linha 698) — limite máximo de dados via POST.
* `upload_max_filesize = 20M` (linha 850) — tamanho máximo de upload de arquivos.

### 3. `config/apache_default.conf`
Define o `VirtualHost *:80` do Apache apontando para `/var/www/html` com `AllowOverride All`. Ideal caso você precise configurar redirecionamentos, cabeçalhos customizados ou alterar o `DocumentRoot`.

### 4. `config/xdebug.ini` (Como Desativar o Xdebug)
O Xdebug já é instalado no `Dockerfile` e vem **ATIVADO por padrão** em [`config/xdebug.ini`](config/xdebug.ini) na porta `9003`.
* **Para desativar (ganhar performance ou preparar para produção) sem reconstruir a imagem:**
  1. Abra o arquivo [`config/xdebug.ini`](config/xdebug.ini).
  2. Comente a linha `zend_extension=xdebug.so` adicionando `;` no início (`;zend_extension=xdebug.so`) — ou altere `xdebug.mode = off`.
  3. Reinicie o container:
     ```bash
     docker-compose restart apache_php
     ```
* **Para reativar:** Descomente novamente `zend_extension=xdebug.so` em `config/xdebug.ini` e reinicie o container `apache_php`.

### 5. `config/servers.json` (Servidor Pré-Cadastrado no pgAdmin)
Este arquivo é montado automaticamente no container do pgAdmin (`/pgadmin4/servers.json`). Graças a ele, ao fazer login no pgAdmin (`http://localhost:8080`), o servidor **`PostgreSQL Docker (database)`** já estará cadastrado na lista de servidores — bastando informar a senha `postgres` na primeira conexão.

### 6. `config/postgresql.conf`
Arquivo opcional com parâmetros do banco PostgreSQL (`max_connections`, `shared_buffers`, `timezone = 'America/Sao_Paulo'`, `log_statement`). Para utilizá-lo por completo, basta descomentar a linha do volume `./config/postgresql.conf` no serviço `database` dentro do [`docker-compose.yml`](docker-compose.yml).

---

## 🛠️ Comandos Úteis

Veja a lista de comandos úteis do Docker em [../docs/commands.md](../docs/commands.md).