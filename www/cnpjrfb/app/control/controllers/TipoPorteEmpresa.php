<?php
class TipoPorteEmpresa
{

    public static function getByid($id)
    {
        $list = self::getList();
        return $list[$id];
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
