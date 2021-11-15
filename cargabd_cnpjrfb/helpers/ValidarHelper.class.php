<?php

class ValidarHelper
{        
    public static function InformacoesIniciais()
    {
        try{
            TPDOConnection::validarDrive();
        }
        catch (Exception $e) {
            print($e->getMessage());
        }
    }
}