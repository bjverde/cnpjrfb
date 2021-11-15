<?php

class ValidarHelper
{        
    public static function InformacoesIniciais()
    {
        try{
            self::drive();
        }
        catch (Exception $e) {
            print($e->getMessage());
        }
    }

    public static function drive()
    {
        throw new InvalidArgumentException('Teste');
    }
}