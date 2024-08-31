<?php
namespace Adianti\Database;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TRecord;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TSqlSelect;

use PDO;
use Exception;
use ReflectionMethod;
use ReflectionClass;

/**
 * Implements the Repository Pattern to deal with collections of Active Records
 *
 * @version    7.6
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TRepository
{
    protected $class; // Active Record class to be manipulated
    protected $trashed;
    protected $criteria; // buffered criteria to use with fluent interfaces
    protected $setValues;
    protected $columns;
    protected $aggregates;
    protected $colTransformers;
    
    /**
     * Class Constructor
     * @param $class = Active Record class name
     */
    public function __construct($class, $withTrashed = FALSE)
    {
        if (class_exists($class))
        {
            if (is_subclass_of($class, 'TRecord'))
            {
                $this->class = $class;
                $this->trashed = $withTrashed;
                $this->criteria = new TCriteria;
            }
            else
            {
                throw new Exception(AdiantiCoreTranslator::translate('The class ^1 was not accepted as argument. The class informed as parameter must be subclass of ^2.', $class, 'TRecord'));
            }
        }
        else
        {
            throw new Exception(AdiantiCoreTranslator::translate('The class ^1 was not found. Check the class name or the file name. They must match', '"' . $class . '"'));
        }
        
        $this->aggregates = [];
    }
    
    /**
     * Set criteria
     */
    public function setCriteria(TCriteria $criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * Set withTrashed using fluent interfaces
     */
    public function withTrashed()
    {
        $this->trashed = true;
        return $this;
    }

    /**
     * Returns the name of database entity
     * @return A String containing the name of the entity
     */
    protected function getEntity()
    {
        return constant($this->class.'::TABLENAME');
    }
    
    /**
     * Get attribute list from entity
     */
    protected function getAttributeList()
    {
        if (!empty($this->columns))
        {
            return implode(', ', $this->columns);
        }
        else
        {
            $object = new $this->class;
            return $object->getAttributeList();
        }
    }
    
    /**
     * Define columns list
     */
    public function select($columns)
    {
        $this->columns = $columns;
        return $this;
    }
    
    /**
     * Add a run time criteria using fluent interfaces
     * 
     * @param  $variable = variable
     * @param  $operator = comparison operator (>,<,=)
     * @param  $value    = value to be compared
     * @param  $logicOperator = logical operator (TExpression::AND_OPERATOR, TExpression::OR_OPERATOR)
     * @return A TRepository object
     */
    public function where($variable, $operator, $value, $logicOperator = TExpression::AND_OPERATOR)
    {
        $value2 = NULL;
        
        if (strtoupper($operator) === 'BETWEEN' && is_array($value) && count($value) == 2)
        {
            $value_array = $value;
            $value  = $value_array[0];
            $value2 = $value_array[1];
        }
        
        $this->criteria->add(new TFilter($variable, $operator, $value, $value2), $logicOperator);
        
        return $this;
    }
    
    /**
     * Assign values to the database columns
     * 
     * @param  $column = column name
     * @param  $value  = column value
     * @return A TRepository object
     */
    public function set($column, $value)
    {
        if (is_scalar($value) OR is_null($value))
        {
            $this->setValues[$column] = $value;
        }
        
        return $this;
    }
    
    /**
     * Add a run time OR criteria using fluent interfaces
     * 
     * @param  $variable = variable
     * @param  $operator = comparison operator (>,<,=)
     * @param  $value    = value to be compared
     * @return A TRepository object
     */
    public function orWhere($variable, $operator, $value)
    {
        $this->criteria->add(new TFilter($variable, $operator, $value), TExpression::OR_OPERATOR);
        
        return $this;
    }
    
    /**
     * Define the ordering for criteria using fluent interfaces
     * 
     * @param  $order = Order column
     * @param  $direction = Order direction (asc, desc)
     * @return A TRepository object
     */
    public function orderBy($order, $direction = 'asc')
    {
        $this->criteria->setProperty('order', $order);
        $this->criteria->setProperty('direction', $direction);
        
        return $this;
    }
    
    /**
     * Define the group for criteria using fluent interfaces
     * 
     * @param  $group Group column
     * @return A TRepository object
     */
    public function groupBy($group)
    {
        $this->criteria->setProperty('group', $group);
        
        return $this;
    }
    
    /**
     * Define the LIMIT criteria using fluent interfaces
     * 
     * @param  $limit = Limit
     * @return A TRepository object
     */
    public function take($limit)
    {
        $this->criteria->setProperty('limit', $limit);
        
        return $this;
    }
    
    /**
     * Define the OFFSET criteria using fluent interfaces
     * 
     * @param  $offset = Offset
     * @return A TRepository object
     */
    public function skip($offset)
    {
        $this->criteria->setProperty('offset', $offset);
        
        return $this;
    }
    
    /**
     * Load a collection       of objects from database using a criteria
     * @param $criteria        An TCriteria object, specifiyng the filters
     * @param $callObjectLoad  If load() method from Active Records must be called to load object parts
     * @return                 An array containing the Active Records
     */
    public function load(TCriteria $criteria = NULL, $callObjectLoad = TRUE)
    {
        if (!$criteria)
        {
            $criteria = isset($this->criteria) ? $this->criteria : new TCriteria;
        }
        
        $class = $this->class;
        $deletedat = $class::getDeletedAtColumn();
        
        if (!$this->trashed && $deletedat)
        {
            $criteria->add(new TFilter($deletedat, 'IS', NULL));
        }

        // creates a SELECT statement
        $sql = new TSqlSelect;
        $sql->addColumn($this->getAttributeList());
        $sql->setEntity($this->getEntity());
        // assign the criteria to the SELECT statement
        $sql->setCriteria($criteria);
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            // register the operation in the LOG file
            TTransaction::log($sql->getInstruction());
            $dbinfo = TTransaction::getDatabaseInfo(); // get dbinfo
            if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
            {
                $result = $conn-> prepare ( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $result-> execute ( $criteria->getPreparedVars() );
            }
            else
            {
                // execute the query
                $result= $conn-> query($sql->getInstruction());
            }
            $results = array();
            
            $class = $this->class;
            $callback = array($class, 'load'); // bypass compiler
            
            // Discover if load() is overloaded
            $rm = new ReflectionMethod($class, $callback[1]);
            
            if ($result)
            {
                // iterate the results as objects
                while ($raw = $result-> fetchObject())
                {
                    $object = new $this->class;
                    if (method_exists($object, 'onAfterLoadCollection'))
                    {
                        $object->onAfterLoadCollection($raw);
                    }
                    $object->fromArray( (array) $raw);
                    
                    if ($callObjectLoad)
                    {
                        // reload the object because its load() method may be overloaded
                        if ($rm->getDeclaringClass()-> getName () !== 'Adianti\Database\TRecord')
                        {
                            $object->reload();
                        }
                    }
                    
                    if ( ($cache = $object->getCacheControl()) && empty($this->columns))
                    {
                        $pk = $object->getPrimaryKey();
                        $record_key = $class . '['. $object->$pk . ']';
                        if ($cache::setValue( $record_key, $object->toArray() ))
                        {
                            TTransaction::log($record_key . ' stored in cache');
                        }
                    }
                    
                    if (!empty($this->colTransformers))
                    {
                        foreach ($this->colTransformers as $transf_alias => $transf_callback)
                        {
                            if (isset($object->$transf_alias))
                            {
                                $object->$transf_alias = $transf_callback($object->$transf_alias);
                            }
                        }
                    }
                    
                    // store the object in the $results array
                    $results[] = $object;
                }
            }
            return $results;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    /**
     * Load with no aggregates
     */
    public function loadStatic()
    {
        return $this->load(null, false);
    }
    
    /**
     * Return a indexed array
     */
    public function getIndexedArray($indexColumn, $valueColumn = NULL, $criteria = NULL)
    {
        if (is_null($valueColumn))
        {
            $valueColumn = $indexColumn;
        }
        
        $criteria = (empty($criteria)) ? $this->criteria : $criteria;
        $objects = $this->load($criteria, FALSE);
        
        $indexedArray = array();
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $key = (isset($object->$indexColumn)) ? $object->$indexColumn : $object->render($indexColumn);
                $val = (isset($object->$valueColumn)) ? $object->$valueColumn : $object->render($valueColumn);
                
                $indexedArray[ $key ] = $val;
            }
        }
        
        if (empty($criteria) or ( $criteria instanceof TCriteria and empty($criteria->getProperty('order')) ))
        {
            asort($indexedArray);
        }
        return $indexedArray;
    }
    
    /**
     * Update values in the repository
     */
    public function update($setValues = NULL, TCriteria $criteria = NULL)
    {
        if (!$criteria)
        {
            $criteria = isset($this->criteria) ? $this->criteria : new TCriteria;
        }
        $class = $this->class;
        $deletedat = $class::getDeletedAtColumn();
        
        if (!$this->trashed && $deletedat)
        {
            $criteria->add(new TFilter($deletedat, 'IS', NULL));
        }

        $setValues = isset($setValues) ? $setValues : $this->setValues;
        
        $class = $this->class;
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            $dbinfo = TTransaction::getDatabaseInfo(); // get dbinfo
            
            // creates a UPDATE statement
            $sql = new TSqlUpdate;
            if ($setValues)
            {
                foreach ($setValues as $column => $value)
                {
                    $sql->setRowData($column, $value);
                }
            }
            $sql->setEntity($this->getEntity());
            // assign the criteria to the UPDATE statement
            $sql->setCriteria($criteria);
            
            if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
            {
                $statement = $conn-> prepare ( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $result = $statement-> execute ( $sql->getPreparedVars() );
            }
            else
            {
                // execute the UPDATE statement
                $result = $conn->exec($sql->getInstruction());
            }
            
            // register the operation in the LOG file
            TTransaction::log($sql->getInstruction());
            
            // update cache
            $record = new $class;
            if ( $cache = $record->getCacheControl() )
            {
                $pk = $record->getPrimaryKey();
                
                // creates a SELECT statement
                $sql = new TSqlSelect;
                $sql->addColumn($this->getAttributeList());
                $sql->setEntity($this->getEntity());
                // assign the criteria to the SELECT statement
                $sql->setCriteria($criteria);
                
                if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
                {
                    $subresult = $conn-> prepare ( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $subresult-> execute ( $criteria->getPreparedVars() );
                }
                else
                {
                    $subresult = $conn-> query($sql->getInstruction());
                }
                
                if ($subresult)
                {
                    // iterate the results as objects
                    while ($raw = $subresult-> fetchObject())
                    {
                        $object = new $this->class;
                        $object->fromArray( (array) $raw);
                    
                        $record_key = $class . '['. $raw->$pk . ']';
                        if ($cache::setValue( $record_key, $object->toArray() ))
                        {
                            TTransaction::log($record_key . ' stored in cache');
                        }
                    }
                }
            }
            
            return $result;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    /**
     * Delete a collection of Active Records from database
     * @param $criteria  An TCriteria object, specifiyng the filters
     * @return           The affected rows
     */
    public function delete(TCriteria $criteria = NULL, $callObjectLoad = FALSE)
    {
        if (!$criteria)
        {
            $criteria = isset($this->criteria) ? $this->criteria : new TCriteria;
        }

        $class = $this->class;
        $deletedat = $class::getDeletedAtColumn();
        
        if (!$this->trashed && $deletedat)
        {
            $criteria->add(new TFilter($deletedat, 'IS', NULL));
        }
        
        $class = $this->class;
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            $dbinfo = TTransaction::getDatabaseInfo(); // get dbinfo
            
            // first, clear cache
            $record = new $class;
            if ( ($cache = $record->getCacheControl()) OR $callObjectLoad )
            {
                $pk = $record->getPrimaryKey();
                
                // creates a SELECT statement
                $sql = new TSqlSelect;
                $sql->addColumn( $pk );
                $sql->setEntity($this->getEntity());
                // assign the criteria to the SELECT statement
                $sql->setCriteria($criteria);
                
                if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
                {
                    $result = $conn-> prepare ( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $result-> execute ( $criteria->getPreparedVars() );
                }
                else
                {
                    $result = $conn-> query($sql->getInstruction());
                }
                
                if ($result)
                {
                    // iterate the results as objects
                    while ($row = $result-> fetchObject())
                    {
                        if ($cache)
                        {
                            $record_key = $class . '['. $row->$pk . ']';
                            if ($cache::delValue( $record_key ))
                            {
                                TTransaction::log($record_key . ' deleted from cache');
                            }
                        }
                        
                        if ($callObjectLoad)
                        {
                            $object = new $this->class;
                            $object->fromArray( (array) $row);
                            $object->delete();
                        }
                    }
                }
            }
            
            if ($deletedat)
            {
                // creates a Update instruction
                $sql = new TSqlUpdate;
                $sql->setEntity($this->getEntity());

                $info = TTransaction::getDatabaseInfo();
                $date_mask = (in_array($info['type'], ['sqlsrv', 'dblib', 'mssql'])) ? 'Ymd H:i:s' : 'Y-m-d H:i:s';
                $sql->setRowData($deletedat, date($date_mask));
            }
            else
            {
                // creates a DELETE statement
                $sql = new TSqlDelete;
                $sql->setEntity($this->getEntity());
            }

            // assign the criteria to the DELETE statement
            $sql->setCriteria($criteria);
            
            if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
            {
                $result = $conn-> prepare ( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                
                if ($sql instanceof TSqlUpdate)
                {
                    $result-> execute ($sql->getPreparedVars());
                }
                else
                {
                    $result-> execute ($criteria->getPreparedVars());
                }
            }
            else
            {
                // execute the DELETE statement
                $result = $conn->exec($sql->getInstruction());
            }
            
            // register the operation in the LOG file
            TTransaction::log($sql->getInstruction());
            
            return $result;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    /**
     * Return the amount of objects that satisfy a given criteria
     * @param $criteria  An TCriteria object, specifiyng the filters
     * @return           An Integer containing the amount of objects that satisfy the criteria
     */
    public function count(TCriteria $criteria = NULL)
    {
        if (!$criteria)
        {
            $criteria = isset($this->criteria) ? $this->criteria : new TCriteria;
        }
        
        $class = $this->class;
        $deletedat = $class::getDeletedAtColumn();
        
        if (!$this->trashed && $deletedat)
        {
            $criteria->add(new TFilter($deletedat, 'IS', NULL));
        }

        // creates a SELECT statement
        $sql = new TSqlSelect;
        $sql->addColumn('count(*)');
        $sql->setEntity($this->getEntity());
        // assign the criteria to the SELECT statement
        $sql->setCriteria($criteria);
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            // register the operation in the LOG file
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
                return $row[0];
            }
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    /**
     * Count distinct aggregate
     * @param $column  Column to be aggregated
     * @return         An array of objects or the total value (if does not have group by)
     */
    public function countDistinctBy($column, $alias = null, Callable $transformation = null)
    {
        $alias = is_null($alias) ? $column : $alias;
        return $this->aggregate('count', 'distinct ' . $column, $alias, $transformation);
    }
    
    /**
     * Count aggregate
     * @param $column  Column to be aggregated
     * @param $alias   Column alias
     * @return         An array of objects or the total value (if does not have group by)
     */
    public function countBy($column, $alias = null, Callable $transformation = null)
    {
        return $this->aggregate('count', $column, $alias, $transformation);
    }
    
    /**
     * Count aggregate and do another aggregate after
     * @param $column  Column to be aggregated
     * @param $alias   Column alias
     * @return         self object
     */
    public function countByAnd($column, $alias = null, Callable $transformation = null)
    {
        $this->aggregates[] = ['count', $column, $alias, $transformation];
        return $this;
    }
    
    /**
     * Sum aggregate
     * @param $column  Column to be aggregated
     * @param $alias   Column alias
     * @return         An array of objects or the total value (if does not have group by)
     */
    public function sumBy($column, $alias = null, Callable $transformation = null)
    {
        return $this->aggregate('sum', $column, $alias, $transformation);
    }
    
    /**
     * Sum aggregate and do another aggregate after
     * @param $column  Column to be aggregated
     * @param $alias   Column alias
     * @return         self object
     */
    public function sumByAnd($column, $alias = null, Callable $transformation = null)
    {
        $this->aggregates[] = ['sum', $column, $alias, $transformation];
        return $this;
    }
    
    /**
     * Average aggregate
     * @param $column  Column to be aggregated
     * @param $alias   Column alias
     * @return         An array of objects or the total value (if does not have group by)
     */
    public function avgBy($column, $alias = null, Callable $transformation = null)
    {
        return $this->aggregate('avg', $column, $alias, $transformation);
    }
    
    /**
     * Average aggregate and do another aggregate after
     * @param $column  Column to be aggregated
     * @param $alias   Column alias
     * @return         self object
     */
    public function avgByAnd($column, $alias = null, Callable $transformation = null)
    {
        $this->aggregates[] = ['avg', $column, $alias, $transformation];
        return $this;
    }
    
    /**
     * Min aggregate
     * @param $column  Column to be aggregated
     * @param $alias   Column alias
     * @return         An array of objects or the total value (if does not have group by)
     */
    public function minBy($column, $alias = null, Callable $transformation = null)
    {
        return $this->aggregate('min', $column, $alias, $transformation);
    }
    
    /**
     * Min aggregate and do another aggregate after
     * @param $column  Column to be aggregated
     * @param $alias   Column alias
     * @return         self object
     */
    public function minByAnd($column, $alias = null, Callable $transformation = null)
    {
        $this->aggregates[] = ['min', $column, $alias, $transformation];
        return $this;
    }
    
    /**
     * Max aggregate
     * @param $column  Column to be aggregated
     * @param $alias   Column alias
     * @return         An array of objects or the total value (if does not have group by)
     */
    public function maxBy($column, $alias = null, Callable $transformation = null)
    {
        return $this->aggregate('max', $column, $alias, $transformation);
    }
    
    /**
     * Max aggregate and do another aggregate after
     * @param $column  Column to be aggregated
     * @param $alias   Column alias
     * @return         self object
     */
    public function maxByAnd($column, $alias = null, Callable $transformation = null)
    {
        $this->aggregates[] = ['max', $column, $alias, $transformation];
        return $this;
    }
    
    /**
     * Aggregate column
     * @param $function Aggregate function (count, sum, min, max, avg)
     * @return          An array of objects or the total value (if does not have group by)
     */
    protected function aggregate($function, $column, $alias = null, Callable $transformation = null)
    {
        $criteria = isset($this->criteria) ? $this->criteria : new TCriteria;
        
        $class = $this->class;
        $deletedat = $class::getDeletedAtColumn();
        
        if (!$this->trashed && $deletedat)
        {
            $criteria->add(new TFilter($deletedat, 'IS', NULL));
        }
        
        $alias = $alias ? $alias : $column;
        // creates a SELECT statement
        $sql = new TSqlSelect;
        if (!empty( $this->criteria->getProperty('group') ))
        {
            if (is_array($this->criteria->getProperty('group')))
            {
                foreach ($this->criteria->getProperty('group') as $group)
                {
                    $sql->addColumn( $group );
                }
            }
            else
            {
                $sql->addColumn( $this->criteria->getProperty('group') );
            }
        }
        
        $transformers = [];
        
        if ($this->aggregates)
        {
            foreach ($this->aggregates as $aggregate)
            {
                list($agg_function, $agg_column, $agg_alias, $agg_transform) = $aggregate;
                
                if (!empty($agg_transform))
                {
                    $this->transformColumn( $agg_alias, $agg_transform);
                }
                $sql->addColumn("$agg_function({$agg_column}) as \"{$agg_alias}\"");
            }
        }
        
        $sql->addColumn("$function({$column}) as \"{$alias}\"");
        
        if (!empty($transformation))
        {
            $this->transformColumn( $alias, $transformation);
        }
        
        $sql->setEntity($this->getEntity());
        
        // assign the criteria to the SELECT statement
        $sql->setCriteria($criteria);
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            // register the operation in the LOG file
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
            
            $results = [];
            
            if ($result)
            {
                // iterate the results as objects
                while ($raw = $result-> fetchObject())
                {
                    if (!empty($this->colTransformers))
                    {
                        foreach ($this->colTransformers as $transf_alias => $transf_callback)
                        {
                            if (isset($raw->$transf_alias))
                            {
                                $raw->$transf_alias = $transf_callback($raw->$transf_alias);
                            }
                        }
                    }
                    
                    $results[] = $raw;
                }
            }
            
            if ($results)
            {
                if ( (count($results) > 1) || !empty($this->criteria->getProperty('group')))
                {
                    return $results;
                }
                else
                {
                    return $results[0]->$alias;
                }
            }
            
            return 0;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    /**
     * Alias for load()
     */
    public function get(TCriteria $criteria = NULL, $callObjectLoad = TRUE)
    {
        return $this->load($criteria, $callObjectLoad);
    }
    
    /**
     * Returns the first collection item
     */
    public function first($callObjectLoad = TRUE)
    {
        $collection = $this->take(1)->load(null, $callObjectLoad);
        if (isset($collection[0]))
        {
            return $collection[0];
        }
    }
    
    /**
     * Returns the last collection item
     */
    public function last($callObjectLoad = TRUE)
    {
        $class = $this->class;
        $pk = (new $class)->getPrimaryKey();
        
        $collection = $this->orderBy($pk,'desc')->take(1)->load(null, $callObjectLoad);
        if (isset($collection[0]))
        {
            return $collection[0];
        }
    }
    
    /**
     * Returns transformed collection
     */
    public function transform( Callable $callback, $callObjectLoad = TRUE)
    {
        $collection = $this->load(null, $callObjectLoad);
        
        if ($collection)
        {
            foreach ($collection as $object)
            {
                call_user_func($callback, $object);
            }
        }
        
        return $collection;
    }
    
    /**
     * Set a transformation for a column
     */
    public function transformColumn( $alias, Callable $callback)
    {
        $this->colTransformers[$alias] = $callback;
        
        return $this;
    }
    
    /**
     * Returns filtered collection
     */
    public function filter( Callable $callback, $callObjectLoad = TRUE)
    {
        $collection = $this->load(null, $callObjectLoad);
        $newcollection = [];
        
        if ($collection)
        {
            foreach ($collection as $object)
            {
                if (call_user_func($callback, $object))
                {
                    $newcollection[] = $object;
                }
            }
        }
        
        return $newcollection;
    }
    
    /**
     * Dump Criteria
     */
	public function dump($prepared = FALSE)
    {
        if (isset($this->criteria) AND $this->criteria)
        {
            $criteria = clone $this->criteria;
            
            $class = $this->class;
            $deletedat = $class::getDeletedAtColumn();
            
            if (!$this->trashed && $deletedat)
            {
                $criteria->add(new TFilter($deletedat, 'IS', NULL));
            }

            return $criteria->dump($prepared);
        }

        return NULL;
    }
}
