<?php
/**
 * db_drive = Drive do PDO  : mysql, pgsql, sqlite, sqlsrv (SQL Server) ou oci (Oracle)
 * db_name = nome do banco. No Oracle é o nome do serviço (ex.: XEPDB1) e no SQLite o caminho do arquivo
 * db_host = endereço do servido de banco de dados
 * db_port = porta do servidor , null ou branco será considerado a porta default
 * db_schema = schema das tabelas. Padrão 'dados_rfb' (igual aos scripts de modelo_banco/).
 *             No SQLite é ignorado. No Oracle o schema é o usuário: use 'dados_rfb' ou '' se conectar com o próprio usuário dono das tabelas. No MySQL o schema é o banco criado por modelo_mysql.sql.
 * linhas_por_transacao = quantas linhas gravar antes de cada COMMIT (padrão 10000)
 * 
 * EXTRACTED_FILES_PATH = caminho do arquivo, informe sempre entre aspas simples ''
 */
return [
     'db_drive' => 'mysql'
    ,'db_host' => '127.0.0.1'
    ,'db_port' => '3306'
    ,'db_name' => 'mybb'
    ,'db_user' => 'mybb'
    ,'db_password' => 'mybb'
    ,'db_schema' => 'dados_rfb'
    ,'linhas_por_transacao' => 10000
    ,'EXTRACTED_FILES_PATH'=>'/var/opt/dados_receita/extracted_files'
];