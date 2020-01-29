<?php
namespace Adianti\Database;

use Adianti\Database\TSqlStatement;
use Adianti\Database\TTransaction;
use Adianti\Database\TCriteria;
use Exception;

/**
 * Provides an Interface to create an MULTI INSERT statement
 *
 * @version    7.1
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSqlMultiInsert extends TSqlStatement
{
    protected $sql;
    private $rows;
    
    /**
     * Constructor method
     */
    public function __construct()
    {
        $this->rows = [];
    }
    
    /**
     * Add a row data
     * @param $row Row data
     */
    public function addRowValues($row)
    {
        $this->rows[] = $row;
    }
    
    /**
     * Transform the value according to its PHP type before send it to the database
     * @param $value    Value to be transformed
     * @return       Transformed Value
     */
    private function transform($value)
    {
        // store just scalar values (string, integer, ...)
        if (is_scalar($value))
        {
            // if is a string
            if (is_string($value) and (!empty($value)))
            {
                $conn = TTransaction::get();
                $result = $conn->quote($value);
            }
            else if (is_bool($value)) // if is a boolean
            {
                $result = $value ? 'TRUE': 'FALSE';
            }
            else if ($value !== '') // if its another data type
            {
                $result = $value;
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
     * this method doesn't exist in this class context
     * @param $criteria A TCriteria object, specifiyng the filters
     * @exception       Exception in any case
     */
    public function setCriteria(TCriteria $criteria)
    {
        throw new Exception("Cannot call setCriteria from " . __CLASS__);
    }
    
    /**
     * Returns the INSERT plain statement
     * @param $prepared Return a prepared Statement
     */
    public function getInstruction( $prepared = FALSE )
    {
        if ($this->rows)
        {
            $buffer = [];
            $target_columns = implode(',', array_keys($this->rows[0]));
            
            foreach ($this->rows as $row)
            {
                foreach ($row as $key => $value)
                {
                    $row[$key] = $this->transform($value);
                }
                
                $values_list = implode(',', $row);
                $buffer[] = "($values_list)";
            }
            
            $this->sql = "INSERT INTO {$this->entity} ($target_columns) VALUES " . implode(',', $buffer);
            return $this->sql;
        }
    }
}
