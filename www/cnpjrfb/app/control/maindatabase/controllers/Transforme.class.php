<?php
class Transforme
{

    public static function getDataGridActionDetalharSocio()
    {
        $actionSocioView = new TDataGridAction(array('SocioViewForm', 'onView'));
        $actionSocioView->setLabel('Detalhar Sócio');
        $actionSocioView->setImage('fa:user green');
        $actionSocioView->setParameter('cnpj_basico', '{cnpj_basico}');
        $actionSocioView->setParameter('nome_socio_razao_social', '{nome_socio_razao_social}');
        $actionSocioView->setParameter('cpf_cnpj_socio', '{cpf_cnpj_socio}');
        $actionSocioView->setField('cnpj_basico');
        return $actionSocioView;
    }

    public static function getDataGridActionDetalharEmpresa()
    {
        $action = new TDataGridAction(array('cnpjFormView', 'onView'));
        $action->setLabel("Detalhar Empresa");
        $action->setButtonClass('btn btn-default');
        $action->setImage('fas:building #7C93CF');
        $action->setParameter('cnpj_basico', '{cnpj_basico}');
        $action->setField('cnpj_basico');
        return $action;
    }

    public static function simNao($value)
    {
        if($value === true || $value == 't' || $value === 1 || $value == '1' || $value == 's' || $value == 'S' || $value == 'T'){
            return 'Sim';
        }
        return 'Não';
    }

    public static function numeroBrasil($value)
    {
        if(!$value){
            $value = 0;
        }        
        if (is_string($value)) {
            $value = str_replace(',', '.', $value);
        }
        if(is_numeric($value)){
            return number_format($value, 2, ",", ".");
        }else{
            return $value;
        }
    }

    public static function date($value)
    {
        if( !empty(trim($value)) && $value!='0000-00-00'){
            try{
                $date = new DateTime($value);
                return $date->format('d/m/Y');
            }catch (Exception $e){
                return $value;
            }
        }
    }

    public static function gridDate($value, $object, $row)
    {
        return  self::date($value);
    }
}
