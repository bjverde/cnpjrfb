<?php
class Transforme
{

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
