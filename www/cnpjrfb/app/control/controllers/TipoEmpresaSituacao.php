<?php
class TipoEmpresaSituacao
{
    public static function getByid($id)
    {
        $list = self::getList();
        $idQtd = strlen($id);
        if($idQtd==1){
            $id = '0'.$id;
        }
        return $list[$id];
    }

    public static function getList()
    {
        $list = array(
             '01'=>'NULA'
            ,'02'=>'ATIVA'
            ,'03'=>'SUSPENSA'
            ,'04'=>'INAPTA'
            ,'08'=>'BAIXADA'
        );
        return $list;
    }


}
