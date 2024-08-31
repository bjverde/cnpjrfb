<?php
namespace Adianti\Database;

use Adianti\Database\TSqlStatement;
use Adianti\Database\TTransaction;

use PDO;

/**
 * Provides an Interface to create SELECT statements
 *
 * @version    7.6
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TSqlSelect extends TSqlStatement
{
    private $columns;   // array with the column names to be returned
    
    /**
     * Add a column name to be returned
     * @param $column   A string containing a column name
     */
    public function addColumn($column)
    {
        // add the column name to the array
        $this->columns[] = $column;
    }
    
    /**
     * Returns the SELECT statement as an string according to the database driver
     * @param $prepared Return a prepared Statement
     */
    public function getInstruction( $prepared = FALSE)
    {
        $conn = TTransaction::get();
        $driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($this->criteria)
        {
            $dbInfo = TTransaction::getDatabaseInfo();
            if(isset($dbInfo['case']) AND $dbInfo['case'] == 'insensitive')
            {
                $this->criteria->setCaseInsensitive(TRUE);
            }
        }

        if (in_array($driver, array('mssql', 'dblib', 'sqlsrv')))
        {
            return $this->getSqlServerInstruction( $prepared );
        }
        else if (in_array($driver, array('oci', 'oci8')))
        {
            return $this->getOracleInstruction( $prepared );
        }
        else if (in_array($driver, array('firebird')))
        {
            return $this->getInterbaseInstruction( $prepared );
        }
        else
        {
            return $this->getStandardInstruction( $prepared );
        }
    }
    
    /**
     * Returns the SELECT statement as an string for standard open source drivers
     * @param $prepared Return a prepared Statement
     */
    public function getStandardInstruction( $prepared )
    {
        // creates the SELECT instruction
        $this->sql  = 'SELECT ';
        // concatenate the column names
        $this->sql .= implode(',', $this->columns);
        // concatenate the entity name
        $this->sql .= ' FROM ' . $this->entity;
        
        // concatenate the criteria (WHERE)
        if ($this->criteria)
        {
            $expression = $this->criteria->dump( $prepared );
            if ($expression)
            {
                $this->sql .= ' WHERE ' . $expression;
            }
            
            // get the criteria properties
            $order     = $this->criteria->getProperty('order');
            $group     = $this->criteria->getProperty('group');
            $limit     = (int) $this->criteria->getProperty('limit');
            $offset    = (int) $this->criteria->getProperty('offset');
            $direction = in_array($this->criteria->getProperty('direction'), array('asc', 'desc')) ? $this->criteria->getProperty('direction') : '';
            
            if ($group)
            {
                $this->sql .= ' GROUP BY ' . (is_array($group) ? implode(',', $group) : $group);
            }
            if ($order)
            {
                $this->sql .= ' ORDER BY ' . $order . ' ' . $direction;
            }
            if ($limit)
            {
                $this->sql .= ' LIMIT ' . $limit;
            }
            if ($offset)
            {
                $this->sql .= ' OFFSET ' . $offset;
            }
        }
        // return the SQL statement
        return $this->sql;
    }
    
    /**
     * Returns the SELECT statement as an string for standard open source drivers
     * @param $prepared Return a prepared Statement
     */
    public function getInterbaseInstruction( $prepared )
    {
        // creates the SELECT instruction
        $this->sql  = 'SELECT ';
        
        if ($this->criteria)
        {
            $limit     = (int) $this->criteria->getProperty('limit');
            $offset    = (int) $this->criteria->getProperty('offset');
            
            if ($limit)
            {
                $this->sql .= ' FIRST ' . $limit . ' ';
            }
            if ($offset)
            {
                $this->sql .= ' SKIP ' . $offset . ' ';
            }
        }
        
        // concatenate the column names
        $this->sql .= implode(',', $this->columns);
        
        // concatenate the entity name
        $this->sql .= ' FROM ' . $this->entity;
        
        // concatenate the criteria (WHERE)
        if ($this->criteria)
        {
            $expression = $this->criteria->dump( $prepared );
            if ($expression)
            {
                $this->sql .= ' WHERE ' . $expression;
            }
            
            // get the criteria properties
            $group     = $this->criteria->getProperty('group');
            $order     = $this->criteria->getProperty('order');
            $direction = in_array($this->criteria->getProperty('direction'), array('asc', 'desc')) ? $this->criteria->getProperty('direction') : '';
            
            if ($group)
            {
                $this->sql .= ' GROUP BY ' . (is_array($group) ? implode(',', $group) : $group);
            }
            
            if ($order)
            {
                $this->sql .= ' ORDER BY ' . $order . ' ' . $direction;
            }
        }
        
        // return the SQL statement
        return $this->sql;
    }
    
    /**
     * Returns the SELECT statement as an string for mssql/dblib drivers
     * @param $prepared Return a prepared Statement
     */
    public function getSqlServerInstruction( $prepared )
    {
        // obtém a cláusula WHERE do objeto criteria.
        if ($this->criteria)
        {
            $expression = $this->criteria->dump( $prepared );
            
            // obtém as propriedades do critério
            $group    = $this->criteria->getProperty('group');
            $order    = $this->criteria->getProperty('order');
            $limit    = (int) $this->criteria->getProperty('limit');
            $offset   = (int) $this->criteria->getProperty('offset');
            $direction= in_array($this->criteria->getProperty('direction'), array('asc', 'desc')) ? $this->criteria->getProperty('direction') : '';
        }
        $columns = implode(',', $this->columns);
        
        if ((isset($limit) OR isset($offset)) AND ($limit>0 OR $offset>0))
        {
            if (empty($order))
            {
                $order = '(SELECT NULL)';
            }
            $this->sql = "SELECT {$columns} FROM ( SELECT ROW_NUMBER() OVER (order by {$order} {$direction}) AS __ROWNUMBER__, {$columns} FROM {$this->entity}";
            
            if (!empty($expression))
            {
                $this->sql.= "    WHERE {$expression} ";
            }
            $this->sql .= " ) AS TAB2";
            if ((isset($limit) OR isset($offset)) AND ($limit>0 OR $offset>0))
            {
                $this->sql .= " WHERE";
            }
            
            if ($limit >0 )
            {
                $total = $offset + $limit;
                $this->sql .= " __ROWNUMBER__ <= {$total} ";
                
                if ($offset)
                {
                    $this->sql .= " AND ";
                }
            }
            if ($offset > 0)
            {
                $this->sql .= " __ROWNUMBER__ > {$offset} ";
            }
        }
        else
        {
            $this->sql  = 'SELECT ';
            $this->sql .= $columns;
            $this->sql .= ' FROM ' . $this->entity;
            if (!empty($expression))
            {
                $this->sql .= ' WHERE ' . $expression;
            }
            
            if (isset($group) AND !empty($group))
            {
                $this->sql .= ' GROUP BY ' . (is_array($group) ? implode(',', $group) : $group);
            }
            if (isset($order) AND !empty($order))
            {
                $this->sql .= ' ORDER BY ' . $order . ' ' . $direction;
            }
        }
        return $this->sql;
    }
    
    /**
     * Returns the SELECT statement as an string for oci8 drivers
     * @param $prepared Return a prepared Statement
     */
    public function getOracleInstruction( $prepared )
    {
        // obtém a cláusula WHERE do objeto criteria.
        if ($this->criteria)
        {
            $expression = $this->criteria->dump( $prepared );
            
            // obtém as propriedades do critério
            $group    = $this->criteria->getProperty('group');
            $order    = $this->criteria->getProperty('order');
            $limit    = (int) $this->criteria->getProperty('limit');
            $offset   = (int) $this->criteria->getProperty('offset');
            $direction= in_array($this->criteria->getProperty('direction'), array('asc', 'desc')) ? $this->criteria->getProperty('direction') : '';
        }
        $columns = implode(',', $this->columns);
        
        $basicsql  = 'SELECT ';
        $basicsql .= $columns;
        $basicsql .= ' FROM ' . $this->entity;
        
        if (!empty($expression))
        {
            $basicsql .= ' WHERE ' . $expression;
        }
        
        if (isset($group) AND !empty($group))
        {
            $basicsql .= ' GROUP BY ' . (is_array($group) ? implode(',', $group) : $group);
        }
        if (isset($order) AND !empty($order))
        {
            $basicsql .= ' ORDER BY ' . $order . ' ' . $direction;
        }
        
        if ((isset($limit) OR isset($offset)) AND ($limit>0 OR $offset>0))
        {
            $this->sql = "SELECT {$columns} ";
            $this->sql.= "  FROM (";
            $this->sql.= "       SELECT rownum \"__ROWNUMBER__\", A.{$columns} FROM ({$basicsql}) A";
            
            if ($limit >0 )
            {
                $total = $offset + $limit;
                $this->sql .= " WHERE rownum <= {$total} ";
            }
            $this->sql.= ")";
            if ($offset > 0)
            {
                $this->sql .= " WHERE \"__ROWNUMBER__\" > {$offset} ";
            }
        }
        else
        {
            $this->sql = $basicsql;
        }
        
        return $this->sql;
    }
}
