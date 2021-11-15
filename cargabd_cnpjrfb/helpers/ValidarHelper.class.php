<?php

class ValidarHelper
{        
    public static function InformacoesIniciais()
    {
        try{
            TPDOConnection::validarDrive();
            ConfigHelper::getExtractedFilesPath();
        }
        catch (Exception $e) {
            print($e->getMessage());
        }
    }
}