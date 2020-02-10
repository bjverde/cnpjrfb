<?php
class TipoEmpresaSituacao
{
    public function getList()
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
