<?php
class TipoSocio
{
    public function getList()
    {
        $list = array(
             1=>'PESSOA JURÍDICA'
            ,2=>'PESSOA FISICA'
            ,3=>'ESTRANGEIRO'
        );
        return $list;
    }


}
