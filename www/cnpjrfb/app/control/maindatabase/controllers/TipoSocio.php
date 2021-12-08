<?php
class TipoSocio
{

    public static function getByid($id)
    {
        $list = self::getList();
        return $list[$id];
    }

    public static function getList()
    {
        $list = array(
             1=>'PESSOA JURÍDICA'
            ,2=>'PESSOA FISICA'
            ,3=>'ESTRANGEIRO'
        );
        return $list;
    }


}
