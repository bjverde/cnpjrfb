<?php
namespace Adianti\Validator;

use Adianti\Validator\TFieldValidator;
use Adianti\Core\AdiantiCoreTranslator;
use Exception;

/**
 * Required field validation
 *
 * @version    7.6
 * @package    validator
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TRequiredValidator extends TFieldValidator
{
    /**
     * Validate a given value
     * @param $label Identifies the value to be validated in case of exception
     * @param $value Value to be validated
     * @param $parameters aditional parameters for validation
     */
    public function validate($label, $value, $parameters = NULL)
    {
        $scalar_empty = function($test) {
            return ( is_scalar($test) AND !is_bool($test) AND trim($test) == '' );
        };
        
        if ( (is_null($value))
          OR ($scalar_empty($value))
          OR (is_array($value) AND count($value)==1 AND isset($value[0]) AND $scalar_empty($value[0]))
          OR (is_array($value) AND empty($value)) )
        {
            throw new Exception(AdiantiCoreTranslator::translate('The field ^1 is required', $label));
        }
    }
}