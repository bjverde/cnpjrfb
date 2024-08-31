<?php
namespace Adianti\Database;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TTransaction;
use Adianti\Database\TCriteria;
use Adianti\Database\TSqlSelect;
use Adianti\Database\TSqlInsert;
use Adianti\Database\TSqlUpdate;
use Adianti\Database\TSqlDelete;

use PDO;
use Exception;
use SplFileObject;
use Closure;

/**
 * Database Task manager
 *
 * @version    7.6
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2018 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TDatabase
{
    /**
     * Drop table
     * 
     * @param $conn     Connection
     * @param $table    Table name
     * @param $ifexists Drop only if exists
     */
    public static function dropTable($conn, $table, $ifexists = false)
    {
        $driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
        
        if (in_array($driver, ['oci', 'dblib', 'sqlsrv']))
        {
            $list = [];
            $table_upper    = strtoupper($table);
            $list['oci']    = "SELECT * FROM cat WHERE table_type in ('TABLE', 'VIEW') AND table_name = '{$table_upper}'";
            $list['dblib']  = "SELECT * FROM sysobjects WHERE (type = 'U' or type='V') AND name = '{$table}'";
            $list['sqlsrv'] = $list['dblib'];
            
            if ($ifexists)
            {
                $sql = $list[$driver];
                $result = $conn->query($sql);
                if (count($result->fetchAll()) > 0)
                {
                    $sql = "DROP TABLE {$table}";
                    TTransaction::log($sql);
                    return $conn->query($sql);
                }
            }
            else
            {
                $sql = "DROP TABLE {$table}";
                TTransaction::log($sql);
                return $conn->query($sql);
            }
        }
        else
        {
            $ife = $ifexists ? ' IF EXISTS ' : '';
            $sql = "DROP TABLE {$ife} {$table}";
            TTransaction::log($sql);
            return $conn->query($sql);
        }
    }
    
    /**
     * Create table
     * 
     * @param $conn     Connection 
     * @param $table    Table name
     * @param $columns  Array of columns
     */
    public static function createTable($conn, $table, $columns)
    {
        $columns_list = [];
        foreach ($columns as $column => $type)
        {
            $columns_list[] = "{$column} {$type}";
        }
        
        $sql = "CREATE TABLE {$table} (" . implode(',', $columns_list) . ")";
        
        TTransaction::log($sql);
        return $conn->query($sql);
    }
    
    /**
     * Drop column
     * 
     * @param $conn     Connection
     * @param $table    Table name
     * @param $column   Column name
     */
    public static function dropColumn($conn, $table, $column)
    {
        $sql = "ALTER TABLE {$table} DROP COLUMN {$column}";
        TTransaction::log($sql);
        return $conn->query($sql);
    }
    
    /**
     * Add column
     *
     * @param $conn     Connection 
     * @param $table    Table name
     * @param $column   Column name
     * @param $type     Column type
     * @param $options  Column options
     */
    public static function addColumn($conn, $table, $column, $type, $options)
    {
        $sql = "ALTER TABLE {$table} ADD {$column} {$type} {$options}";
        TTransaction::log($sql);
        return $conn->query($sql);
    }
    
    /**
     * Insert data
     * 
     * @param $conn           Connection
     * @param $table          Table name
     * @param $values         Array of values
     * @param $avoid_criteria Criteria to avoid insertion
     */
    public static function insertData($conn, $table, $values, $avoid_criteria = null)
    {
        if (!empty($avoid_criteria))
        {
            if (self::countData($conn, $table, $avoid_criteria) > 0)
            {
                return;
            }
        }
        
        $sql = new TSqlInsert;
        $sql->setEntity($table);
        
        foreach ($values as $key => $value)
        {
            $sql->setRowData($key, $value);
        }
        
        TTransaction::log($sql->getInstruction());
        
        $dbinfo = TTransaction::getDatabaseInfo(); // get dbinfo
        if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
        {
            $result = $conn-> prepare ( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $result-> execute ( $sql->getPreparedVars() );
        }
        else
        {
            // execute the query
            $result = $conn-> query($sql->getInstruction());
        }
        
        return $result;
    }


    /**
     * Update data
     * 
     * @param $conn           Connection
     * @param $table          Table name
     * @param $values         Array of values
     * @param $avoid_criteria Criteria to avoid insertion
     */
    public static function updateData($conn, $table, $values, $criteria = null)
    {
        $sql = new TSqlUpdate;
        $sql->setEntity($table);
        
        if ($criteria)
        {
            $sql->setCriteria($criteria);
        }
        
        foreach ($values as $key => $value)
        {
            $sql->setRowData($key, $value);
        }
        
        TTransaction::log($sql->getInstruction());
        
        $dbinfo = TTransaction::getDatabaseInfo(); // get dbinfo
        if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
        {
            $result = $conn-> prepare ( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $result-> execute ( $sql->getPreparedVars() );
        }
        else
        {
            // execute the query
            $result = $conn-> query($sql->getInstruction());
        }
        
        return $result;
    }
    
    /**
     * Clear table data
     * 
     * @param $conn     Connection
     * @param $table    Table name
     * @param $criteria Filter criteria
     */
    public static function clearData($conn, $table, $criteria = null)
    {
        $sql = new TSqlDelete;
        $sql->setEntity($table);
        if ($criteria)
        {
            $sql->setCriteria($criteria);
        }
        
        TTransaction::log( $sql->getInstruction() );
        return $conn->query( $sql->getInstruction() );
    }
    
    /**
     * Execute SQL
     * 
     * @param $conn  Connection
     * @param $query SQL
     */
    public static function execute($conn, $query)
    {
        TTransaction::log($query);
        return $conn->query($query);
    }
    
    /**
     * Get RAW Data
     * 
     * @param $conn            Connection
     * @param $query           SQL
     * @param $mapping         Mapping between fields
     * @param $prepared_values Parameters for SQL Query
     */
    public static function getData($conn, $query, $mapping = null, $prepared_values = null, Closure $action = null)
    {
        $data = [];
        
        $result  = $conn->prepare($query);
        $result->execute($prepared_values);
        
        foreach ($result as $row)
        {
            $values = [];
            if ($mapping)
            {
                foreach ($mapping as $map)
                {
                    $newcolumn = $map[1];
                    $values[$newcolumn] = self::transform($row, $map);
                }
            }
            else
            {
                $values = $row;
            }
            
            if (empty($action))
            {
                $data[] = $values;
            }
            else
            {
                $action($values);
            }
        }
        
        if (empty($action))
        {
            return $data;
        }
    }
    
    /**
     * Get a row from the table
     * 
     * @param $conn     PDO source connection
     * @param $table    Source table
     * @param $criteria Filter criteria
     */
    public static function getRowData(PDO $conn, $table, $criteria = null)
    {
        $sql = new TSqlSelect;
        $sql->setEntity($table);
        
        if (empty($criteria))
        {
            $criteria = new TCriteria;
        }
        $criteria->setProperty('limit', 1);
        $sql->setCriteria($criteria);
        
        $sql->addColumn('*');
        
        TTransaction::log($sql->getInstruction());
        
        $dbinfo = TTransaction::getDatabaseInfo(); // get dbinfo
        if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
        {
            $result = $conn-> prepare ( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $result-> execute ( $criteria->getPreparedVars() );
        }
        else
        {
            // executes the SELECT statement
            $result= $conn-> query($sql->getInstruction());
        }
        
        if ($result)
        {
            $row = $result->fetch();
            return $row;
        }
        
        return null;
    }
    
    /**
     * Count data from table
     * 
     * @param $conn     PDO source connection
     * @param $table    Source table
     * @param $criteria Filter criteria
     */
    public static function countData(PDO $conn, $table, $criteria = null)
    {
        $sql = new TSqlSelect;
        $sql->setEntity($table);
        
        if ($criteria)
        {
            $sql->setCriteria($criteria);
        }
        $sql->addColumn('count(*)');
        
        TTransaction::log($sql->getInstruction());
        
        $dbinfo = TTransaction::getDatabaseInfo(); // get dbinfo
        if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
        {
            $result = $conn-> prepare ( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $result-> execute ( $criteria->getPreparedVars() );
        }
        else
        {
            // executes the SELECT statement
            $result= $conn-> query($sql->getInstruction());
        }
        
        if ($result)
        {
            $row = $result->fetch();
            return (int) $row[0];
        }
        
        return 0;
    }
    
    /**
     * Copy data from table to table
     * 
     * @param $source_conn     PDO source connection
     * @param $target_conn     PDO target connection
     * @param $source_table    Source table
     * @param $target_table    Target table
     * @param $mapping         Mapping between fields
     * @param $criteria        Filter criteria
     * @param $bulk_inserts    Inserts per time
     * @param $auto_commit     Auto commit after x inserts
     */
    public static function copyData(PDO $source_conn, PDO $target_conn, $source_table, $target_table, $mapping, $criteria = null, $bulk_inserts = 1, $auto_commit = false)
    {
        $driver = $target_conn->getAttribute(PDO::ATTR_DRIVER_NAME);
        $bulk_inserts = $driver == 'oci' ? 1 : $bulk_inserts;
        
        $source_columns = [];
        $target_columns = [];
        
        foreach ($mapping as $map)
        {
            if (!empty($map[0]) AND substr($map[0],0,4) !== 'VAL:')
            {
                $source_columns[] = $map[0];
            }
            $target_columns[] = $map[1];
        }
        
        $sel = new TSqlSelect;
        $sel->setEntity($source_table);
        if ($criteria)
        {
            $sel->setCriteria($criteria);
        }
        
        foreach ($source_columns as $source_column)
        {
            $sel->addColumn($source_column);
        }
        
        $result = $source_conn->query($sel->getInstruction());
        
        $ins = new TSqlMultiInsert;
        $ins->setEntity($target_table);
        $buffer_counter = 0;
        $commit_counter = 0;
        
        foreach ($result as $row)
        {
            $values = [];
            foreach ($mapping as $map)
            {
                $newcolumn = $map[1];
                $values[$newcolumn] = self::transform($row, $map);
            }
            $ins->addRowValues($values);
            
            $buffer_counter ++;
            $commit_counter ++;
            
            if ($buffer_counter == $bulk_inserts)
            {
                TTransaction::log( $ins->getInstruction() );
                $target_conn->query($ins->getInstruction());
                $buffer_counter = 0;
                
                // restart bulk insert
                $ins = new TSqlMultiInsert;
                $ins->setEntity($target_table);
                
                if ($auto_commit)
                {
                    if ($commit_counter == $auto_commit)
                    {
                        $target_conn->commit();
                        $target_conn->beginTransaction();
                        TTransaction::log( 'COMMIT' );
                        $commit_counter = 0;
                    }
                }
            }
        }
        
        if ($buffer_counter > 0)
        {
            TTransaction::log( $ins->getInstruction() );
            $target_conn->query($ins->getInstruction());
        }
    }
    
    /**
     * Copy data from query to table
     * 
     * @param $source_conn     PDO source connection
     * @param $target_conn     PDO target connection
     * @param $query           SQL Query
     * @param $target_table    Target table
     * @param $mapping         Mapping between fields
     * @param $prepared_values Parameters for SQL Query
     * @param $bulk_inserts    Inserts per time
     * @param $auto_commit     Auto commit after x inserts
     */
    public static function copyQuery(PDO $source_conn, PDO $target_conn, $query, $target_table, $mapping, $prepared_values = null, $bulk_inserts = 1, $auto_commit = false)
    {
        $driver = $target_conn->getAttribute(PDO::ATTR_DRIVER_NAME);
        $bulk_inserts = $driver == 'oci' ? 1 : $bulk_inserts;
        
        $target_columns = [];
        
        foreach ($mapping as $map)
        {
            $target_columns[] = $map[1];
        }
        
        $result = $source_conn->prepare($query);
        $result->execute($prepared_values);
        
        $ins = new TSqlMultiInsert;
        $ins->setEntity($target_table);
        $buffer_counter = 0;
        $commit_counter = 0;
        
        foreach ($result as $row)
        {
            $values = [];
            foreach ($mapping as $map)
            {
                $newcolumn = $map[1];
                $values[$newcolumn] = self::transform($row, $map);
            }
            $ins->addRowValues($values);
            
            $buffer_counter ++;
            $commit_counter ++;
            
            if ($buffer_counter == $bulk_inserts)
            {
                TTransaction::log( $ins->getInstruction() );
                $target_conn->query($ins->getInstruction());
                $buffer_counter = 0;
                
                // restart bulk insert
                $ins = new TSqlMultiInsert;
                $ins->setEntity($target_table);
                
                if ($auto_commit)
                {
                    if ($commit_counter == $auto_commit)
                    {
                        $target_conn->commit();
                        $target_conn->beginTransaction();
                        TTransaction::log( 'COMMIT' );
                        $commit_counter = 0;
                    }
                }
            }
        }
        
        if ($buffer_counter > 0)
        {
            TTransaction::log( $ins->getInstruction() );
            $target_conn->query($ins->getInstruction());
        }
    }
    
    /**
     * Import data from CSV file
     * @param $filename        CSV File to import
     * @param $target_conn     Target connection
     * @param $target_table    Target table
     * @param $mapping         Mapping between fields
     * @param $separator       Columns separator [,]
     */
    public static function importFromFile($filename, $target_conn, $target_table, $mapping, $separator = ',', $bulk_inserts = 1)
    {
        $driver = $target_conn->getAttribute(PDO::ATTR_DRIVER_NAME);
        $bulk_inserts = $driver == 'oci' ? 1 : $bulk_inserts;
        
        $counter = 1;
        
        if (!file_exists($filename))
        {
            throw new Exception(AdiantiCoreTranslator::translate('File not found' . ': ' . $filename));
        }
        
        if (!is_readable($filename))
        {
            throw new Exception(AdiantiCoreTranslator::translate('Permission denied' . ': ' . $filename));
        }
        
        $data   = file($filename);
        $header = str_getcsv($data[0], $separator);
        
        $ins = new TSqlMultiInsert;
        $ins->setEntity($target_table);
        $buffer_counter = 0;
        
        while (isset($data[$counter]))
        {
            $row = str_getcsv($data[$counter ++], $separator);
            foreach ($row as $key => $value)
            {
                $row[ $header[$key] ] = $value;
            }
            
            $values = [];
            foreach ($mapping as $map)
            {
                $newcolumn = $map[1];
                $values[$newcolumn] = self::transform($row, $map);
            }
            
            $ins->addRowValues($values);
            
            $buffer_counter ++;
            if ($buffer_counter == $bulk_inserts)
            {
                TTransaction::log( $ins->getInstruction() );
                $target_conn->query($ins->getInstruction());
                $buffer_counter = 0;
                
                // restart bulk insert
                $ins = new TSqlMultiInsert;
                $ins->setEntity($target_table);
            }
        }
        if ($buffer_counter > 0)
        {
            TTransaction::log( $ins->getInstruction() );
            $target_conn->query($ins->getInstruction());
        }
    }
    
    /**
     * Export data to CSV file
     * @param $source_conn     Source connection
     * @param $source_table    Target table
     * @param $filename        CSV File to import
     * @param $mapping         Mapping between fields
     * @param $criteria        Select criteria
     * @param $separator       Columns separator [,]
     */
    public static function exportToFile($source_conn, $source_table, $filename, $mapping, $criteria = null, $separator = ',')
    {
        $source_columns = [];
        $target_columns = [];
        
        if ( (file_exists($filename) AND !is_writable($filename)) OR (!is_writable(dirname($filename))) )
        {
            throw new Exception(AdiantiCoreTranslator::translate('Permission denied' . ': ' . $filename));
        }
        
        foreach ($mapping as $map)
        {
            if (!empty($map[0]) AND substr($map[0],0,4) !== 'VAL:')
            {
                $source_columns[] = $map[0];
            }
            $target_columns[] = $map[1];
        }
        
        $sel = new TSqlSelect;
        $sel->setEntity($source_table);
        if ($criteria)
        {
            $sel->setCriteria($criteria);
        }
        
        foreach ($source_columns as $source_column)
        {
            $sel->addColumn($source_column);
        }
        
        $result = $source_conn->query($sel->getInstruction());
        
        $file = new SplFileObject($filename, 'w');
        $file->setCsvControl(',');
        $file->fputcsv($target_columns);
        
        foreach ($result as $row)
        {
            $values = [];
            foreach ($mapping as $map)
            {
                $newcolumn = $map[1];
                $values[$newcolumn] = self::transform($row, $map);
            }
            
            $file->fputcsv(array_values($values));
        }
        $file = null; // close
    }
    
    /**
     * Transform value according to mapping rules
     * @param $row Row values
     * @param $map Array with mapping instruction
     */
    private static function transform($row, $map)
    {
        $column   = $map[0];
        $callback = isset($map[2]) ? $map[2] : null;
        $value    = (substr($column,0,4)== 'VAL:') ? substr($column,4) : $row[$column];
        
        if (is_string($value))
        {
            $value = preg_replace('/[[:cntrl:]]/', '', $value);
        }
        
        if (is_callable($callback))
        {
            $value = call_user_func($callback, $value, $row);
        }
        
        return $value;
    }
}
