<?php
namespace Adianti\Validator;

use Adianti\Validator\TFieldValidator;
use Adianti\Core\AdiantiCoreTranslator;
use Exception;

/**
 * Maximum stripped text length validation
 *
 * @version    7.4
 * @package    validator
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TStrippedTextMaxLengthValidator extends TFieldValidator
{
    /**
     * Validate a given value
     * @param $label Identifies the value to be validated in case of exception
     * @param $value Value to be validated
     * @param $parameters aditional parameters for validation (min value)
     */
    public function validate($label, $value, $parameters = NULL)
    {
        $maxvalue = $parameters[0];

        $value = preg_replace('/(<p><br><\/p>)/i', ' ', $value);
        $value = preg_replace('/(<([^>]+)>)/i', '', $value);
        $value = preg_replace('/(&nbsp;)/i', ' ', $value);
        
        $value = strlen($value);
        
        if ($value > $maxvalue)
        {
            throw new Exception(AdiantiCoreTranslator::translate('The field ^1 can not be greater than ^2 characters', $label, $maxvalue));
        }
    }
}
