<?php
namespace Adianti\Validator;

use Adianti\Validator\TFieldValidator;
use Adianti\Core\AdiantiCoreTranslator;
use Exception;

/**
 * Minimum length validation
 *
 * @version    7.3
 * @package    validator
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMinLengthValidator extends TFieldValidator
{
    /**
     * Validate a given value
     * @param $label Identifies the value to be validated in case of exception
     * @param $value Value to be validated
     * @param $parameters aditional parameters for validation (length)
     */
    public function validate($label, $value, $parameters = NULL)
    {
        $length = $parameters[0];
        
        if (strlen(trim($value)) < $length)
        {
            throw new Exception(AdiantiCoreTranslator::translate('The field ^1 can not be less than ^2 characters', $label, $length));
        }
    }
}
