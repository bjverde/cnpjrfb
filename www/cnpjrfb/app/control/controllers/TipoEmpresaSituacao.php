<?php
class TipoEmpresaSituacao
{
    public static function getByid($id)
    {
        $list = self::getList();
        $idQtd = strlen($id);
        /*
        if($idQtd==1){
            $id = '0'.$id;
        }
        */
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
