<?php
class TipoFaixaEtaria
{

    public static function getByid($id)
    {
        $list = self::getList();
        return $list[$id];
    }

    public static function getList()
    {
        $list = array(
             1=>'entre 0 a 12 anos'
            ,2=>'entre 13 a 20 anos'
            ,3=>'entre 21 a 30 anos'
            ,4=>'entre 31 a 40 anos'
            ,5=>'entre 41 a 50 anos'
            ,6=>'entre 51 a 60 anos'
            ,7=>'entre 61 a 70 anos'
            ,8=>'entre 71 a 80 anos'
            ,9=>'maiores de 80 anos'
            ,0=>'não se aplica'
        );
        return $list;
    }
}
