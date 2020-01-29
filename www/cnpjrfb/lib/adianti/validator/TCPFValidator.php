<?php
namespace Adianti\Validator;

use Adianti\Validator\TFieldValidator;
use Adianti\Core\AdiantiCoreTranslator;
use Exception;

/**
 * CPF validation (Valid only in Brazil)
 *
 * @version    7.1
 * @package    validator
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TCPFValidator extends TFieldValidator
{
    /**
     * Validate a given value
     * @param $label Identifies the value to be validated in case of exception
     * @param $value Value to be validated
     * @param $parameters aditional parameters for validation
     */
    public function validate($label, $value, $parameters = NULL)
    {
        // cpfs inválidos
        $nulos = array("12345678909","11111111111","22222222222","33333333333",
                       "44444444444","55555555555","66666666666","77777777777",
                       "88888888888","99999999999","00000000000");
        // Retira todos os caracteres que nao sejam 0-9
        $cpf = preg_replace("/[^0-9]/", "", $value);
        
        if (strlen($cpf) <> 11)
        {
            throw new Exception(AdiantiCoreTranslator::translate('The field ^1 has not a valid CPF', $label));
        }
        
        // Retorna falso se houver letras no cpf
        if (!(preg_match("/[0-9]/",$cpf)))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The field ^1 has not a valid CPF', $label));
        }

        // Retorna falso se o cpf for nulo
        if( in_array($cpf, $nulos) )
        {
            throw new Exception(AdiantiCoreTranslator::translate('The field ^1 has not a valid CPF', $label));
        }

        // Calcula o penúltimo dígito verificador
        $acum=0;
        for($i=0; $i<9; $i++)
        {
          $acum+= $cpf[$i]*(10-$i);
        }

        $x=$acum % 11;
        $acum = ($x>1) ? (11 - $x) : 0;
        // Retorna falso se o digito calculado eh diferente do passado na string
        if ($acum != $cpf[9])
        {
          throw new Exception(AdiantiCoreTranslator::translate('The field ^1 has not a valid CPF', $label));
        }
        // Calcula o último dígito verificador
        $acum=0;
        for ($i=0; $i<10; $i++)
        {
          $acum+= $cpf[$i]*(11-$i);
        }  

        $x=$acum % 11;
        $acum = ($x > 1) ? (11-$x) : 0;
        // Retorna falso se o digito calculado eh diferente do passado na string
        if ( $acum != $cpf[10])
        {
          throw new Exception(AdiantiCoreTranslator::translate('The field ^1 has not a valid CPF', $label));
        }  
    }
}
