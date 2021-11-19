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

    public static function loadAll()
    {        
        $filep = ROOT_PATH.DS.'config.php';
        if (file_exists($filep)){
            $ini = require $filep;
            return $ini;
        }else{
            throw new InvalidArgumentException('ERRO: Arquivo Config não encontrado');
        }
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
        $drive = ArrayHelper::get($ini,self::DB_DRIVE);
        return $drive;
    }
    public static function getDdHost()
    {
        $ini = self::loadAll();
        $drive = ArrayHelper::get($ini,self::DB_HOST);
        return $drive;
    }    
    public static function getDdPort()
    {
        $ini = self::loadAll();
        $drive = ArrayHelper::get($ini,self::DB_PORT);
        return $drive;
    }
    public static function getDdName()
    {
        $ini = self::loadAll();
        $drive = ArrayHelper::get($ini,self::DB_NAME);
        return $drive;
    }
    public static function getDdUser()
    {
        $ini = self::loadAll();
        $drive = ArrayHelper::get($ini,self::DB_USER);
        return $drive;
    }
    public static function getDdPassword()
    {
        $ini = self::loadAll();
        $drive = ArrayHelper::get($ini,self::DB_PASS);
        return $drive;
    }
}