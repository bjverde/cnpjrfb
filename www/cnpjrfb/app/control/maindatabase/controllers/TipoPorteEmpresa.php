<?php
class TipoPorteEmpresa
{

    public static function getByid($id)
    {
        if( is_null($id) || $id === '' ){
            return $id;
        }
        $id = str_pad(trim((string) $id), 2, '0', STR_PAD_LEFT); //'1' ou '01'
        $list = self::getList();
        return isset($list[$id]) ? $list[$id] : $id;
    }

    public static function getList()
    {
        $list = array(
             '00'=>'Não Informado'
            ,'01'=>'Micro Empresa'
            ,'03'=>'Empresa de Pequeno Portes'
            ,'05'=>'Demais'
        );
        return $list;
    }


}
