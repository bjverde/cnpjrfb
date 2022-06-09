<?php
namespace Adianti\Validator;

use Adianti\Core\AdiantiCoreTranslator;
use Exception;

/**
 * CNPJ validation (Valid only in Brazil)
 *
 * @version    7.4
 * @package    validator
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TCNPJValidator extends TFieldValidator
{
    /**
     * Validate a given value
     * @param $label Identifies the value to be validated in case of exception
     * @param $value Value to be validated
     * @param $parameters aditional parameters for validation
     */
    public function validate($label, $value, $parameters = NULL)
    {
        $cnpj = preg_replace( "@[./-]@", "", $value );
        if( strlen( $cnpj ) <> 14 or !is_numeric( $cnpj ) )
        {
            throw new Exception(AdiantiCoreTranslator::translate('The field ^1 has not a valid CNPJ', $label));
        }
        $k = 6;
        $soma1 = 0;
        $soma2 = 0;
        for( $i = 0; $i < 13; $i++ )
        {
            $k = $k == 1 ? 9 : $k;
            $soma2 += ( substr($cnpj, $i, 1) * $k );
            $k--;
            if($i < 12)
            {
                if($k == 1)
                {
                    $k = 9;
                    $soma1 += ( substr($cnpj, $i, 1) * $k );
                    $k = 1;
                }
                else
                {
                    $soma1 += ( substr($cnpj, $i, 1) * $k );
                }
            }
        }
        
        $digito1 = $soma1 % 11 < 2 ? 0 : 11 - $soma1 % 11;
        $digito2 = $soma2 % 11 < 2 ? 0 : 11 - $soma2 % 11;
        
        $valid = ( substr($cnpj, 12, 1) == $digito1 and substr($cnpj, 13, 1) == $digito2 );
        
        if (!$valid)
        {
            throw new Exception(AdiantiCoreTranslator::translate('The field ^1 has not a valid CNPJ', $label));
        }
    }
}
