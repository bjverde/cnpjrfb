<?php
class TipoMatrizFilial
{

    public static function getByid($id)
    {
        $list = self::getList();
        return isset($list[$id]) ? $list[$id] : $id;
    }

    public static function getList()
    {
        $list = array(
             '1'=>'Matriz'
            ,'2'=>'Filial'
        );
        return $list;
    }


}
