<?php
class TipoEmpresaSituacao
{
    /**
     * Aceita '2' ou '02' (o banco grava com dois dígitos, conforme o layout da RFB).
     * Código desconhecido devolve o próprio código.
     */
    public static function getByid($id)
    {
        if( is_null($id) || $id === '' ){
            return $id;
        }
        $id = str_pad(trim((string) $id), 2, '0', STR_PAD_LEFT);
        $list = self::getList();
        return isset($list[$id]) ? $list[$id] : $id;
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
