<?php
namespace Adianti\Database;

use Adianti\Database\TExpression;
use Adianti\Database\TSqlStatement;
use Adianti\Registry\TSession;
use Adianti\Util\AdiantiStringConversion;

/**
 * Provides an interface to define filters to be used inside a criteria
 *
 * @version    7.6
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TFilter extends TExpression
{
    private $variable;
    private $operator;
    private $value;
    private $value2;
    private $preparedVars;
    private $caseInsensitive;
    private static $paramCounter;
    
    /**
     * Class Constructor
     * @param  $variable = variable
     * @param  $operator = operator (>, <, =, BETWEEN)
     * @param  $value    = value to be compared
     * @param  $value2   = second value to be compared (between)
     */
    public function __construct($variable, $operator, $value, $value2 = NULL)
    {
        // store the properties
        $this->variable = $variable;
        $this->operator = $operator;
        $this->preparedVars = array();
        
        // transform the value according to its type
        $this->value    = $value;
        
        if ($value2)
        {
            $this->value2 = $value2;
        }
        $this->caseInsensitive = FALSE;
    }
    
    /**
     * Transform the value according to its PHP type
     * before send it to the database
     * @param $value    Value to be transformed
     * @param $prepared If the value will be prepared
     * @return       Transformed Value
     */
    private function transform($value, $prepared = FALSE)
    {
        // if the value is an array
        if (is_array($value))
        {
            $foo = array();
            // iterate the array
            foreach ($value as $x)
            {
                // if the value is an integer
                if (is_numeric($x))
                {
                    if ($prepared)
                    {
                        $preparedVar = ':par_'.$this->getRandomParameter();
                        $this->preparedVars[ $preparedVar ] = $x;
                        $foo[] = $preparedVar;
                    }
                    else
                    {
                        $foo[] = $x;
                    }
                }
                else if (is_string($x))
                {
                    // if the value is an string, add quotes
                    if ($prepared)
                    {
                        $preparedVar = ':par_'.$this->getRandomParameter();
                        $this->preparedVars[ $preparedVar ] = $x;
                        $foo[] = $preparedVar;
                    }
                    else
                    {
                        $foo[] = "'$x'";
                    }
                }
                else if (is_bool($x))
                {
                    $foo[] = ($x) ? 'TRUE' : 'FALSE';
                }
            }
            // convert the array into a string, splitted by ","
            $result = '(' . implode(',', $foo) . ')';
        }
        // if the value is a subselect (must not be escaped as string)
        else if (substr(strtoupper( (string) $value),0,7) == '(SELECT')
        {
            $value  = str_replace(['#', '--', '/*'], ['', '', ''], $value);
            $result = $value;
        }
        // if the value is a session variable
        else if (strpos((string) $value, '{session.') !== false)
        {
            $session_var = AdiantiStringConversion::getBetween($value, '{session.', '}');
            $result = str_replace("{session.{$session_var}}", TSession::getValue($session_var), $value);
        }
        // if the value must not be escaped (NOESC in front)
        else if (substr( (string) $value,0,6) == 'NOESC:')
        {
            $value  = str_replace(['#', '--', '/*'], ['', '', ''], $value);
            $result = substr($value,6);
        }
        // if the value is a string
        else if (is_string($value))
        {
            if ($prepared)
            {
                $preparedVar = ':par_'.$this->getRandomParameter();
                $this->preparedVars[ $preparedVar ] = $value;
                $result = $preparedVar;
            }
            else
            {
                // add quotes
                $result = "'$value'";
            }
        }
        // if the value is NULL
        else if (is_null($value))
        {
            // the result is 'NULL'
            $result = 'NULL';
        }
        // if the value is a boolean
        else if (is_bool($value))
        {
            // the result is 'TRUE' of 'FALSE'
            $result = $value ? 'TRUE' : 'FALSE';
        }
        // if the value is a TSqlStatement object
        else if ($value instanceof TSqlStatement)
        {
            // the result is the return of the getInstruction()
            $result = '(' . $value->getInstruction() . ')';
        }
        else
        {
            if ($prepared)
            {
                $preparedVar = ':par_'.$this->getRandomParameter();
                $this->preparedVars[ $preparedVar ] = $value;
                $result = $preparedVar;
            }
            else
            {
                $result = $value;
            }
        }
        
        // returns the result
        return $result;
    }
    
    /**
     * Return the prepared vars
     */
    public function getPreparedVars()
    {
        return $this->preparedVars;
    }
    
    /**
     * Return the filter as a string expression
     * @return  A string containing the filter
     */
    public function dump( $prepared = FALSE )
    {
        $this->preparedVars = array();
        $value = $this->transform($this->value, $prepared);
        if ($this->value2)
        {
            $value2 = $this->transform($this->value2, $prepared);
            // concatenated the expression
            return "{$this->variable} {$this->operator} {$value} AND {$value2}";
        }
        else
        {
            $variable = $this->variable;
            $operator = $this->operator;

            if ($this->caseInsensitive && stristr(strtolower($operator),'like') !== FALSE)
            {
                $variable = "UPPER({$variable})";
                $value = "UPPER({$value})";
                $operator = str_ireplace('ilike', 'LIKE', $operator);
            }

            // concatenated the expression
            return "{$variable} {$operator} {$value}";
        }
    }
    
    /**
     * Returns a random parameter
     */
    private function getRandomParameter()
    {
        self::$paramCounter ++;
        return self::$paramCounter;
    }

    /**
     * Force case insensitive searches
     */
    public function setCaseInsensitive(bool $value) : void
    {
        $this->caseInsensitive = $value;
    }

    /**
     * Return if case insensitive is turned ON
     */
    public function getCaseInsensitive() : bool
    {
        return $this->caseInsensitive;
    }
}
