<?php
class Transforme
{

    public static function date($value)
    {
        if( !empty(trim($value)) && $value!='0000-00-00')
        {
            try
            {
                $date = new DateTime($value);
                return $date->format('d/m/Y');
            }
            catch (Exception $e)
            {
                return $value;
            }
        }
    }

    public static function gridDate($value, $object, $row)
    {
        return  self::date($value);
    }
}
