<?php
namespace Adianti\Validator;

use Adianti\Core\AdiantiCoreTranslator;
use Exception;

/**
 * CNPJ validation (Valid only in Brazil)
 *
 * @version    8.6
 * @package    validator
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd.
 * @license    https://adiantiframework.com.br/license
 */
class TCNPJValidator extends TFieldValidator
{
    /**
     * Validation weights
     */
    private const WEIGHTS = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    
    /**
     * Validate a given value
     *
     * @param $label      Identifies the value to be validated in case of exception
     * @param $value      Value to be validated
     * @param $parameters Additional parameters for validation
     */
    public function validate($label, $value, $parameters = null)
    {
        $cnpj = strtoupper(preg_replace('/[^A-Z0-9]/', '', (string) $value));

        if (!$this->isValidFormat($cnpj) || !$this->checkDigits($cnpj))
        {
            throw new Exception(AdiantiCoreTranslator::translate( 'The field ^1 has not a valid CNPJ', $label ));
        }
    }

    /**
     * Verify basic format rules
     */
    private function isValidFormat(string $cnpj): bool
    {
        if (strlen($cnpj) !== 14)
        {
            return false;
        }

        /*
         * First 12 positions: letters and digits
         * Last 2 positions: verification digits
         */
        if (!preg_match('/^[A-Z0-9]{12}[0-9]{2}$/', $cnpj))
        {
            return false;
        }

        /*
         * Reject sequences composed of a single repeated character
         */
        if (count(array_unique(str_split($cnpj))) === 1)
        {
            return false;
        }

        return true;
    }

    /**
     * Verify check digits
     */
    private function checkDigits(string $cnpj): bool
    {
        $base = substr($cnpj, 0, 12);

        $digit1 = $this->calculateDigit($base, 1);
        $digit2 = $this->calculateDigit($base . $digit1, 0);

        return $cnpj[12] == $digit1 && $cnpj[13] == $digit2;
    }

    /**
     * Calculate a verification digit
     */
    private function calculateDigit(string $value, int $offset): int
    {
        $sum = 0;
        $length = strlen($value);

        for ($i = 0; $i < $length; $i++)
        {
            $sum += $this->charValue($value[$i]) * self::WEIGHTS[$i + $offset];
        }

        $remainder = $sum % 11;

        return ($remainder < 2) ? 0 : (11 - $remainder);
    }

    /**
     * Convert a character to its numeric value
     * according to the official CNPJ alphanumeric rule
     */
    private function charValue(string $char): int
    {
        return ord($char) - ord('0');
    }
}
