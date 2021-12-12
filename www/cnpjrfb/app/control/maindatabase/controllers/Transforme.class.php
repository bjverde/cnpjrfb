<?php
class Transforme
{

    public static function numeroBrasil($value)
    {
        var_dump($value);
        if(!$value){
            $value = 0;
        }else if(is_numeric($value)){
            return "R$ " . number_format($value, 2, ",", ".");
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
