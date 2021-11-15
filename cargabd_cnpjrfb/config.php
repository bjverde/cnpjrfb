<?php
/**
 * db_drive = Drive do PDO  : mysql, sqlite, pgsql
 * db_host = endereço do servido de banco de dados
 * db_port = porta do servidor , null ou branco será considerado a porta default
 * 
 * EXTRACTED_FILES_PATH = caminho do arquivo, informe sempre entre aspas simples ''
 */
return [
     'db_drive' => 'mysql'
    ,'db_host' => '127.0.0.1'
    ,'db_port' => '3306'
    ,'db_name' => 'dados_rfb'
    ,'db_user' => 'dados_rfb'
    ,'db_password' => '123456'
    ,'EXTRACTED_FILES_PATH'=>'/var/opt/dados_receita/extracted_files'
];