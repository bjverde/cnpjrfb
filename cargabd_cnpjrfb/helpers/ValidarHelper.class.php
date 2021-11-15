<?php

class ValidarHelper
{        
    public static function InformacoesIniciais()
    {
        try{
            TPDOConnection::validarDrive();
            TPDOConnection::validarDBMS();
            ConfigHelper::getExtractedFilesPath();
        }
        catch (Exception $e) {
            print($e->getMessage());
        }
    }
}