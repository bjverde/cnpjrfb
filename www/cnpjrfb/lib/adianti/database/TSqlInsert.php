<?php
namespace Adianti\Database;

use Adianti\Database\TSqlStatement;
use Adianti\Database\TTransaction;
use Adianti\Database\TCriteria;
use Exception;
use PDO;

/**
 * Provides an Interface to create an INSERT statement
 *
 * @version    7.3
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSqlInsert extends TSqlStatement
{
    protected $sql;
    private $columnValues;
    private $preparedVars;
    
    /**
     * Constructor method
     */
    public function __construct()
    {
        $this->columnValues = [];
        $this->preparedVars = [];
    }
    
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
            // if is a string
            if (is_string($value) and (!empty($value)))
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
     * this method doesn't exist in this class context
     * @param $criteria A TCriteria object, specifiyng the filters
     * @exception       Exception in any case
     */
    public function setCriteria(TCriteria $criteria)
    {
        throw new Exception("Cannot call setCriteria from " . __CLASS__);
    }
    
    /**
     * Return the prepared vars
     */
    public function getPreparedVars()
    {
        return $this->preparedVars;
    }
    
    /**
     * Returns the INSERT plain statement
     * @param $prepared Return a prepared Statement
     */
    public function getInstruction( $prepared = FALSE )
    {
        $conn = TTransaction::get();
        $driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
        
        $this->preparedVars = array();
        $columnValues = $this->columnValues;
        if ($columnValues)
        {
            foreach ($columnValues as $key => $value)
            {
                $columnValues[$key] = $this->transform($value, $prepared);
            }
        }
        
        $this->sql = "INSERT INTO {$this->entity} (";
        $columns = implode(', ', array_keys($columnValues));   // concatenates the column names
        $values  = implode(', ', array_values($columnValues)); // concatenates the column values
        $this->sql .= $columns . ')';
        $this->sql .= " VALUES ({$values})";
        
        if ($driver == 'firebird')
        {
            $this->sql .= " RETURNING {{primary_key}}";
        }
        
        // returns the string
        return $this->sql;
    }
}
