<?php
class TipoEmpresaSituacao
{
    public static function getByid($id)
    {
        $list = self::getList();
        return $list[$id];
    }

    public static function getList()
    {
        $list = array(
             '1'=>'NULA'
            ,'2'=>'ATIVA'
            ,'3'=>'SUSPENSA'
            ,'4'=>'INAPTA'
            ,'8'=>'BAIXADA'
        );
        return $list;
    }


}
