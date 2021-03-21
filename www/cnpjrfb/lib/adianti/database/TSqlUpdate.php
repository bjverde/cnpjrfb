<?php
namespace Adianti\Database;

use Adianti\Database\TSqlStatement;
use Adianti\Database\TTransaction;

/**
 * Provides an Interface to create UPDATE statements
 *
 * @version    7.3
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSqlUpdate extends TSqlStatement
{
    protected $sql;         // stores the SQL statement
    private $columnValues;
    private $preparedVars;
    
    /**
     * Assign values to the database columns
     * @param $column   Name of the database column
     * @param $value    Value for the database column
     */
    public function setRowData($column, $value)
    {
        if (is_scalar($value) OR is_null($value))
        {
            $this->columnValues[$column] = $value;
        }
    }
    
    /**
     * Unset row data
     * @param $column   Name of the database column
     */
    public function unsetRowData($column)
    {
        if (isset($this->columnValues[$column]))
        {
            unset($this->columnValues[$column]);
        }
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
        // store just scalar values (string, integer, ...)
        if (is_scalar($value))
        {
            if (substr(strtoupper($value),0,7) == '(SELECT')
            {
                $value  = str_replace(['#', '--', '/*'], ['', '', ''], $value);
                $result = $value;
            }
            // if the value must not be escaped (NOESC in front)
            else if (substr($value,0,6) == 'NOESC:')
            {
                $value  = str_replace(['#', '--', '/*'], ['', '', ''], $value);
                $result = substr($value,6);
            }
            // if is a string
            else if (is_string($value) and (!empty($value)))
            {
                if ($prepared)
                {
                    $preparedVar = ':par_'.self::getRandomParameter();
                    $this->preparedVars[ $preparedVar ] = $value;
                    $result = $preparedVar;
                }
                else
                {
                    $conn = TTransaction::get();
                    $result = $conn->quote($value);
                }
            }
            else if (is_bool($value)) // if is a boolean
            {
                $result = $value ? 'TRUE': 'FALSE';
            }
            else if ($value !== '') // if its another data type
            {
                if ($prepared)
                {
                    $preparedVar = ':par_'.self::getRandomParameter();
                    $this->preparedVars[ $preparedVar ] = $value;
                    $result = $preparedVar;
                }
                else
                {
                    $result = $value;
                }
            }
            else
            {
                $result = "NULL";
            }
        }
        else if (is_null($value))
        {
            $result = "NULL";
        }
        
        return $result;
    }
    
    /**
     * Return the prepared vars
     */
    public function getPreparedVars()
    {
        if ($this->criteria)
        {
            // "column values" prepared vars + "where" prepared vars
            return array_merge($this->preparedVars, $this->criteria->getPreparedVars());
        }
        else
        {
            return $this->preparedVars;
        }
    }
    
    /**
     * Returns the UPDATE plain statement
     * @param $prepared Return a prepared Statement
     */
    public function getInstruction( $prepared = FALSE)
    {
        $this->preparedVars = array();
        // creates the UPDATE statement
        $this->sql = "UPDATE {$this->entity}";
        
        // concatenate the column pairs COLUMN=VALUE
        if ($this->columnValues)
        {
            foreach ($this->columnValues as $column => $value)
            {
                $value = $this->transform($value, $prepared);
                $set[] = "{$column} = {$value}";
            }
        }
        $this->sql .= ' SET ' . implode(', ', $set);
        
        // concatenates the criteria (WHERE)
        if ($this->criteria)
        {
            $this->sql .= ' WHERE ' . $this->criteria->dump( $prepared );
        }
        
        // returns the SQL statement
        return $this->sql;
    }
}
