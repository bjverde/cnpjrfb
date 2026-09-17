<?php
/**
 * Strong password validation
 *
 * @version    8.6
 * @package    validator
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license-template
 */
class TStrongPasswordValidator extends TFieldValidator
{
    /**
     * Validate a given value
     * @param $label Identifies the value to be validated in case of exception
     * @param $value Value to be validated
     * @param $parameters aditional parameters for validation (ex: mask)
     */
    public function validate($label, $value, $parameters = NULL)
    {
        if ($value)
        {
            // Validate password strength
            $uppercase = preg_match('@[A-Z]@', $value);
            $lowercase = preg_match('@[a-z]@', $value);
            $number    = preg_match('@[0-9]@', $value);
            $specialChars = preg_match('@[^\w]@', $value);
            
            if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($value) < 6)
            {
                throw new Exception(_t('Password should be at least 6 characters and include at least one upper case letter, one number, and one special character'));
            }
        }
    }
}
