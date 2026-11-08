[← Voltar ao README principal](../README.md)

# Sumário da documentação

Índice geral dos documentos disponíveis na pasta `docs/`, com um breve resumo de cada um.

| Documento | Resumo |
| --- | --- |
| [Arquitetura: cnpjrfb + CNPJ-full](arquitetura.md) | Diagrama de como os dois projetos se ligam (CNPJ-full grava, cnpjrfb lê o mesmo banco e pede o grafo), como o PHP lê o `.env` do CNPJ-full no `maindatabase.php` e o que já existe e o que falta. |
| [Instalação do sistema web](instalacao.md) | Requisitos de PHP e extensões PDO, cópia dos arquivos para o servidor web e configuração da conexão pelo `.env` do CNPJ-full (lido pelo `app/config/maindatabase.php`) e `init.php`. |
| [Carga dos dados](carga.md) | Como baixar os arquivos da Receita Federal e fazer a carga no banco com o CNPJ-full, e quando criar as chaves estrangeiras. |
| [Modelo de banco de dados](modelo_banco.md) | Visão geral das tabelas, chaves primárias e estrangeiras, cuidados com tipos de dados na carga e links para os scripts SQL de criação (MySQL/MariaDB, PostgreSQL, SQLite, SQL Server e Oracle) em `docs/modelo_banco/`. |
| [Docker](docker.md) | Como subir o ambiente de desenvolvimento em containers (Debian 13, Apache + PHP 8.5, PostgreSQL e pgAdmin) e usar o CNPJ-full (carga, consultas e grafo) dentro do container. |
| [Segurança em produção](seguranca.md) | Checklist do que desativar ou restringir antes de publicar o sistema na internet (menus administrativos, permissões do usuário do banco, depuração) e orientações sobre a LGPD. |
| [Desenvolvimento](desenvolvimento.md) | Estrutura de pastas do repositório, como manter o modelo de banco sincronizado com os Active Records e o CNPJ-full, e como testar alterações localmente. |
| [Dicionário de dados da RFB (`cnpj-metadados.pdf`)](cnpj-metadados.pdf) | Documento oficial da Receita Federal ("Novo Layout para os DADOS ABERTOS do CNPJ") com a especificação de todas as colunas e tabelas dos arquivos públicos. |

