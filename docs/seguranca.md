[← Voltar ao README](../README.md)

# Segurança em produção

O cnpjrfb não tem login, porque é uma consulta pública de dados que a Receita Federal já divulga. Mesmo assim, algumas telas e configurações servem só para o desenvolvimento e precisam ser desligadas antes de publicar o sistema.

## Checklist

- [ ] **Tirar do menu as ferramentas de administração.** Em `www/cnpjrfb/menu.xml`, remova os itens **Painel SQL** (`SystemSQLPanel`), **Database explorer** (`SystemDatabaseExplorer`) e **Setup PHP** (`SystemPHPInfoView`). As duas primeiras deixam qualquer visitante rodar consultas pesadas e exportar tabelas inteiras. A terceira mostra a configuração do servidor.
- [ ] **Bloquear essas classes também pela URL.** O Adianti abre qualquer classe por `index.php?class=...`, mesmo que ela não esteja no menu. Apague os arquivos dessas telas da cópia de produção ou bloqueie o acesso a elas no servidor web.
- [ ] **Usar um usuário de banco só com leitura.** O sistema web só precisa de `SELECT`. Assim, mesmo uma consulta indevida não altera nem apaga dados.
  ```sql
  -- PostgreSQL
  CREATE USER cnpjrfb_web PASSWORD 'troque_esta_senha';
  GRANT USAGE ON SCHEMA dados_rfb TO cnpjrfb_web;
  GRANT SELECT ON ALL TABLES IN SCHEMA dados_rfb TO cnpjrfb_web;
  -- MySQL / MariaDB
  CREATE USER 'cnpjrfb_web'@'%' IDENTIFIED BY 'troque_esta_senha';
  GRANT SELECT ON dados_rfb.* TO 'cnpjrfb_web'@'%';
  ```
- [ ] **Desligar as mensagens de depuração.** Em `www/cnpjrfb/app/config/application.ini`, use `debug = 0`. No `php.ini`, use `display_errors = Off`.
- [ ] **Não publicar a carga.** `www/cargabd` só roda pelo terminal. Deixe essa pasta fora do servidor web, ou bloqueie o acesso a ela, porque ela guarda o `config.php` com a senha do banco, o `error_log.txt` e os arquivos da RFB.
- [ ] **Limitar o `download.php`** a arquivos dentro de `tmp/` e `app/output/`, ou removê-lo se não usar exportações.
- [ ] **Docker:** não use o `docker-compose.yml` de desenvolvimento como está (veja [Docker](docker.md#atenção-é-um-ambiente-de-desenvolvimento)).

## Dados pessoais (LGPD)

Os arquivos da RFB trazem nomes de sócios e representantes legais, CPFs mascarados (ex.: `***123456**`) e faixa etária. Publicar ou cruzar esses dados deve seguir a LGPD (Lei 13.709/2018).
