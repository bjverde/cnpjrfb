[← Voltar ao README principal](../README.md)

# Sumário da documentação

Índice geral dos documentos disponíveis na pasta `docs/`, com um breve resumo de cada um.

| Documento | Resumo |
| --- | --- |
| [Instalação do sistema web](instalacao.md) | Requisitos de PHP e extensões PDO, cópia dos arquivos para o servidor web e configuração da conexão em `app/config/maindatabase.php` e `init.php`. |
| [Carga dos dados](carga.md) | Passo a passo para baixar e descompactar os arquivos CSV da Receita Federal, criar o banco, configurar `www/cargabd/config.php`, executar a carga pelo terminal e criar as chaves estrangeiras. |
| [Modelo de banco de dados](modelo_banco.md) | Visão geral das tabelas, chaves primárias e estrangeiras, cuidados com tipos de dados na carga e links para os scripts SQL de criação (MySQL/MariaDB, PostgreSQL, SQLite, SQL Server e Oracle) em `docs/modelo_banco/`. |
| [Docker](docker.md) | Como subir o ambiente de desenvolvimento em containers (Apache + PHP 8.2, MariaDB, Adminer e phpMyAdmin) e rodar a carga no MariaDB do Docker. |
| [Segurança em produção](seguranca.md) | Checklist do que desativar ou restringir antes de publicar o sistema na internet (menus administrativos, permissões do usuário do banco, depuração) e orientações sobre a LGPD. |
| [Desenvolvimento](desenvolvimento.md) | Estrutura de pastas do repositório, como manter o modelo de banco sincronizado com os DAOs e Active Records, e como testar alterações localmente. |
| [Dicionário de dados da RFB (`cnpj-metadados.pdf`)](cnpj-metadados.pdf) | Documento oficial da Receita Federal ("Novo Layout para os DADOS ABERTOS do CNPJ") com a especificação de todas as colunas e tabelas dos arquivos públicos. |

