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
}