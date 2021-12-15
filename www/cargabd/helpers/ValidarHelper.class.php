<?php

class ValidarHelper
{      
    
    public static function validarExtensaoCarregada($extensao=null)
    {
        if (!extension_loaded($extensao)) {
            throw new InvalidArgumentException('ERRO: extensão do PHP '.$extensao.' não está instalada!');
        }
    }
    public static function InformacoesIniciais()
    {
        try{
            self::validarExtensaoCarregada('mbstring');
            TPDOConnection::validarDrive();
            TPDOConnection::validarDBMS();
            ConfigHelper::getExtractedFilesPath();
        }
        catch (Exception $e) {
            print($e->getMessage());
        }
    }
    public static function validarData($data)
    {
        if (strlen($data)!=8) {
            $data ='';
        }else if( $data=='00000000' ){
            $data ='';
        }
        return $data;
    }
}