<?php
/*
 * CargaBd_CnpjRFB
 * Criado por Reinaldo A. Barrêto Jr
 * https://github.com/bjverde/cnpjrfb
 *
 * ----------------------------------------------------------------------------
 */
class ConfigHelper
{
    const EXTRACTED_FILES_PATH = "EXTRACTED_FILES_PATH";
    const DB_DRIVE = "db_drive";
    const DB_HOST = "db_host";
    const DB_PORT = "db_port";
    const DB_NAME = "db_name";
    const DB_USER = "db_user";
    const DB_PASS = "db_password";
    const DB_SCHEMA = "db_schema";
    const LINHAS_POR_TRANSACAO = "linhas_por_transacao";

    const SCHEMA_PADRAO = 'dados_rfb';
    const LINHAS_POR_TRANSACAO_PADRAO = 10000;

    private static $config = null;

    public static function loadAll()
    {
        if ( is_null(self::$config) ) {
            $filep = ROOT_PATH.DS.'config.php';
            if (!file_exists($filep)){
                throw new InvalidArgumentException('ERRO: Arquivo Config não encontrado');
            }
            self::$config = require $filep;
        }
        return self::$config;
    }
    /**
     * Usado nos testes para trocar a configuração sem arquivo.
     */
    public static function setConfig($config)
    {
        self::$config = $config;
    }
    public static function getExtractedFilesPath()
    {
        $ini = self::loadAll();
        $path = ArrayHelper::get($ini,self::EXTRACTED_FILES_PATH);
        if( empty($path) ){
            throw new InvalidArgumentException('ERRO: Caminho do arquivos descompactados não informado');
        }
        if( !is_dir($path) ){
            throw new InvalidArgumentException('ERRO: Caminho informado '.$path.' não é válido');
        }
        return $path;
    }
    public static function getDBDrive()
    {
        $ini = self::loadAll();
        return ArrayHelper::get($ini,self::DB_DRIVE);
    }
    public static function getDdHost()
    {
        $ini = self::loadAll();
        return ArrayHelper::get($ini,self::DB_HOST);
    }
    public static function getDdPort()
    {
        $ini = self::loadAll();
        return ArrayHelper::get($ini,self::DB_PORT);
    }
    public static function getDdName()
    {
        $ini = self::loadAll();
        return ArrayHelper::get($ini,self::DB_NAME);
    }
    public static function getDdUser()
    {
        $ini = self::loadAll();
        return ArrayHelper::get($ini,self::DB_USER);
    }
    public static function getDdPassword()
    {
        $ini = self::loadAll();
        return ArrayHelper::get($ini,self::DB_PASS);
    }
    /**
     * Schema das tabelas. Se não estiver no config.php: 'dados_rfb', igual aos
     * scripts de modelo_banco/. No SQLite é sempre vazio (modelo_sqlite.sql não usa schema).
     */
    public static function getDbSchema()
    {
        if ( self::getDBDrive() == TPDOConnection::DBMS_SQLITE ) {
            return '';
        }
        $ini = self::loadAll();
        if ( is_array($ini) && array_key_exists(self::DB_SCHEMA, $ini) ) {
            return trim((string) $ini[self::DB_SCHEMA]);
        }
        return self::SCHEMA_PADRAO;
    }
    /**
     * Schema com ponto no final ('dados_rfb.') ou vazio
     */
    public static function getDbSchemaPrefix()
    {
        $schema = self::getDbSchema();
        if ( $schema === '' ) {
            return '';
        }
        if ( !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $schema) ) {
            throw new InvalidArgumentException('ERRO: db_schema inválido: '.$schema);
        }
        return $schema.'.';
    }
    public static function getLinhasPorTransacao()
    {
        $ini = self::loadAll();
        $qtd = (int) ArrayHelper::get($ini,self::LINHAS_POR_TRANSACAO);
        return $qtd > 0 ? $qtd : self::LINHAS_POR_TRANSACAO_PADRAO;
    }
}
