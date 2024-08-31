<?php
namespace Adianti\Database;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TTransaction;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRepository;
use Adianti\Database\TSqlSelect;
use Adianti\Database\TSqlInsert;
use Adianti\Database\TSqlUpdate;
use Adianti\Database\TSqlDelete;
use Adianti\Registry\TSession;

use Math\Parser;
use PDO;
use Exception;
use IteratorAggregate;
use ArrayIterator;
use Traversable;

/**
 * Base class for Active Records
 *
 * @version    7.6
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
abstract class TRecord implements IteratorAggregate
{
    protected $data;  // array containing the data of the object
    protected $vdata; // array with virtual data (non-persistant properties)
    protected $attributes; // array of attributes
    protected $trashed;
    
    /**
     * Class Constructor
     * Instantiates the Active Record
     * @param [$id] Optional Object ID, if passed, load this object
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        $this->attributes = array();
        $this->trashed = FALSE;
        
        if ($id) // if the user has informed the $id
        {
            // load the object identified by ID
            if ($callObjectLoad)
            {
                $object = $this->load($id);
            }
            else
            {
                $object = self::load($id);
            }
            
            if ($object)
            {
                $this->fromArray($object->toArray());
            }
            else
            {
                throw new Exception(AdiantiCoreTranslator::translate('Object ^1 not found in ^2', $id, constant(get_class($this).'::TABLENAME')));
            }
        }
    }
    
    /**
     * Returns iterator
     */
    public function getIterator () : Traversable
    {
        return new ArrayIterator( $this->data );
    }
    
    /**
     * Create a new TRecord and returns the instance
     * @param $data indexed array
     */
    public static function create($data)
    {
        $object = new static;
        $object->fromArray($data);
        $object->store();
        return $object;
    }
    
    /**
     * Executed when the programmer clones an Active Record
     * In this case, we have to clear the ID, to generate a new one
     */
    public function __clone()
    {
        $pk = $this->getPrimaryKey();
        unset($this->$pk);
    }
    
    /**
     * Executed whenever an unknown method is executed
     * @param $method Method name
     * @param $parameter Method parameters
     */
    public static function __callStatic($method, $parameters)
    {
        $class_name = get_called_class();
        if (substr($method,-13) == 'InTransaction')
        {
            $method = substr($method,0,-13);
            if (method_exists($class_name, $method))
            {
                $database = array_shift($parameters);
                TTransaction::open($database);
                $content = forward_static_call_array( array($class_name, $method), $parameters);
                TTransaction::close();
                return $content;
            }
            else
            {
                throw new Exception(AdiantiCoreTranslator::translate('Method ^1 not found', $class_name.'::'.$method.'()'));
            }
        }
        else if (method_exists('TRepository', $method))
        {
            $class = get_called_class(); // get the Active Record class name
            $repository = new TRepository( $class ); // create the repository
            return call_user_func_array( array($repository, $method), $parameters );
        }
        else
        {
            throw new Exception(AdiantiCoreTranslator::translate('Method ^1 not found', $class_name.'::'.$method.'()'));
        }
    }
    
    /**
     * Executed whenever a property is accessed
     * @param $property Name of the object property
     * @return          The value of the property
     */
    public function __get($property)
    {
        // check if exists a method called get_<property>
        if (method_exists($this, 'get_'.$property))
        {
            // execute the method get_<property>
            return call_user_func(array($this, 'get_'.$property));
        }
        else
        {
            if (strpos($property, '->') !== FALSE)
            {
                $parts = explode('->', $property);
                $container = $this;
                foreach ($parts as $part)
                {
                    if (is_object($container))
                    {
                        $result = $container->$part;
                        $container = $result;
                    }
                    else
                    {
                        throw new Exception(AdiantiCoreTranslator::translate('Trying to access a non-existent property (^1)', $property));
                    }
                }
                return $result;
            }
            else
            {
                // returns the property value
                if (isset($this->data[$property]))
                {
                    return $this->data[$property];
                }
                else if (isset($this->vdata[$property]))
                {
                    return $this->vdata[$property];
                }
            }
        }
    }
    
    /**
     * Executed whenever a property is assigned
     * @param $property Name of the object property
     * @param $value    Value of the property
     */
    public function __set($property, $value)
    {
        if ($property == 'data')
        {
            throw new Exception(AdiantiCoreTranslator::translate('Reserved property name (^1) in class ^2', $property, get_class($this)));
        }
        
        // check if exists a method called set_<property>
        if (method_exists($this, 'set_'.$property))
        {
            // executed the method called set_<property>
            call_user_func(array($this, 'set_'.$property), $value);
        }
        else
        {
            if ($value === NULL)
            {
                $this->data[$property] = NULL;
            }
            else if (is_scalar($value))
            {
                // assign the property's value
                $this->data[$property] = $value;
                unset($this->vdata[$property]);
            }
            else
            {
                // other non-scalar properties that won't be persisted
                $this->vdata[$property] = $value;
                unset($this->data[$property]);
            }
        }
    }
    
    /**
     * Returns if a property is assigned
     * @param $property Name of the object property
     */
    public function __isset($property)
    {
        return isset($this->data[$property]) or
               isset($this->vdata[$property]) or
               method_exists($this, 'get_'.$property);
    }
    
    /**
     * Unset a property
     * @param $property Name of the object property
     */
    public function __unset($property)
    {
        unset($this->data[$property]);
        unset($this->vdata[$property]);
    }
    
    /**
     * Returns the cache control
     */
    public function getCacheControl()
    {
        $class = get_class($this);
        $cache_name = "{$class}::CACHECONTROL";
        
        if ( defined( $cache_name ) )
        {
            $cache_control = constant($cache_name);
            $implements = class_implements($cache_control);
            
            if (in_array('Adianti\Registry\AdiantiRegistryInterface', $implements))
            {
                if ($cache_control::enabled())
                {
                    return $cache_control;
                }
            }
        }
        
        return FALSE;
    }
    
    /**
     * Returns the name of database entity
     * @return A String containing the name of the entity
     */
    public function getEntity()
    {
        // get the Active Record class name
        $class = get_class($this);
        // return the TABLENAME Active Record class constant
        return constant("{$class}::TABLENAME");
    }
    
    /**
     * Returns the the name of the primary key for that Active Record
     * @return A String containing the primary key name
     */
    public function getPrimaryKey()
    {
        // get the Active Record class name
        $class = get_class($this);
        // returns the PRIMARY KEY Active Record class constant
        return constant("{$class}::PRIMARYKEY");
    }
    
    /**
     * Returns the the name of the created at column
     * @return A String containing the created at column
     */
    public function getCreatedAtColumn()
    {
        // get the Active Record class name
        $class = get_class($this);
        
        if (defined("{$class}::CREATEDAT"))
        {
            // returns the CREATEDAT Active Record class constant
            return constant("{$class}::CREATEDAT");
        }
    }
    
    /**
     * Returns the the name of the updated at column
     * @return A String containing the updated at column
     */
    public function getUpdatedAtColumn()
    {
        // get the Active Record class name
        $class = get_class($this);
        
        if (defined("{$class}::UPDATEDAT"))
        {
            // returns the UPDATEDAT Active Record class constant
            return constant("{$class}::UPDATEDAT");
        }
    }
    
    /**
     * Returns the the name of the deleted at column
     * @return A String containing the deleted at column
     */
    public static function getDeletedAtColumn()
    {
        // get the Active Record class name
        $class = get_called_class();
        if(defined("{$class}::DELETEDAT"))
        {
            // returns the DELETEDAT Active Record class constant
            return constant("{$class}::DELETEDAT");
        }

        return NULL;
    }
    
    /**
     * Returns the the name of the created at column
     * @return A String containing the created at column
     */
    public function getCreatedByColumn()
    {
        // get the Active Record class name
        $class = get_class($this);
        
        if (defined("{$class}::CREATEDBY"))
        {
            // returns the CREATEDBY Active Record class constant
            return constant("{$class}::CREATEDBY");
        }
    }
    
    /**
     * Returns the the name of the updated at column
     * @return A String containing the updated at column
     */
    public function getUpdatedByColumn()
    {
        // get the Active Record class name
        $class = get_class($this);
        
        if (defined("{$class}::UPDATEDBY"))
        {
            // returns the UPDATEDBY Active Record class constant
            return constant("{$class}::UPDATEDBY");
        }
    }
    
    /**
     * Returns the the name of the deleted at column
     * @return A String containing the deleted at column
     */
    public static function getDeletedByColumn()
    {
        // get the Active Record class name
        $class = get_called_class();
        if(defined("{$class}::DELETEDBY"))
        {
            // returns the DELETEDBY Active Record class constant
            return constant("{$class}::DELETEDBY");
        }

        return NULL;
    }
    
    /**
     * Returns the information related to the logged user
     * @return A String containing user login or id or custom code
     */
    public function getByUserSessionIdentificator()
    {
        // get the Active Record class name
        $class = get_class($this);
        
        $session_var = 'userid';
        
        if (defined("{$class}::USERBYATT"))
        {
            $session_var = constant("{$class}::USERBYATT");
        }
        
        return TSession::getValue($session_var);
    }
    
    /**
     * Returns the the name of the sequence for primary key
     * @return A String containing the sequence name
     */
    private function getSequenceName()
    {
        $conn = TTransaction::get();
        $driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
        
        // get the Active Record class name
        $class = get_class($this);
        
        if (defined("{$class}::SEQUENCE"))
        {
            return constant("{$class}::SEQUENCE");
        }
        else if (in_array($driver, array('oci', 'oci8')))
        {
            return $this->getEntity().'_seq';
        }
        else
        {
            return $this->getEntity().'_'. $this->getPrimaryKey().'_seq';
        }
    }
    
    /**
     * Fill the Active Record properties from another Active Record
     * @param $object An Active Record
     */
    public function mergeObject(TRecord $object)
    {
        $data = $object->toArray();
        foreach ($data as $key => $value)
        {
            $this->data[$key] = $value;
        }
    }
    
    /**
     * Fill the Active Record properties from an indexed array
     * @param $data An indexed array containing the object properties
     */
    public function fromArray($data)
    {
        if (count($this->attributes) > 0)
        {
            $pk = $this->getPrimaryKey();
            foreach ($data as $key => $value)
            {
                // set just attributes defined by the addAttribute()
                if ((in_array($key, $this->attributes) AND is_string($key)) OR ($key === $pk))
                {
                    $this->data[$key] = $data[$key];
                }
            }
        }
        else
        {
            foreach ($data as $key => $value)
            {
                $this->data[$key] = $data[$key];
            }
        }
    }
    
    /**
     * Return the Active Record properties as an indexed array
     * @param $filter_attributes Array of attributes to be returned.
     * @return An indexed array containing the object properties
     */
    public function toArray( $filter_attributes = null )
    {
        $attributes = $filter_attributes ? $filter_attributes : $this->attributes;
        
        $data = array();
        if (count($attributes) > 0)
        {
            $pk = $this->getPrimaryKey();
            if (!empty($this->data))
            {
                foreach ($this->data as $key => $value)
                {
                    if ((in_array($key, $attributes) AND is_string($key)) OR ($key === $pk))
                    {
                        $data[$key] = $this->data[$key];
                    }
                }
            }
        }
        else
        {
            $data = $this->data;
        }
        return $data;
    }
    
    /**
     * Return virtual data (non-persistant properties)
     */
    public function getVirtualData()
    {
        return $this->vdata;
    }
    
    /**
     * Return the Active Record properties as a json string
     * @return A JSON String
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }
    
    /**
     * Render variables inside brackets
     */
    public function render($pattern, $cast = null)
    {
        $content = $pattern;
        if (preg_match_all('/\{(.*?)\}/', $pattern, $matches) )
        {
            foreach ($matches[0] as $match)
            {
                $property = substr($match, 1, -1);
                if (substr($property, 0, 1) == '$')
                {
                    $property = substr($property, 1);
                }
                $value = $this->$property;
                if ($cast)
                {
                    settype($value, $cast);
                }
                $content  = str_replace($match, (string) $value, $content);
            }
        }
        
        return $content;
    }
    
    /**
     * Evaluate variables inside brackets
     */
    public function evaluate($pattern)
    {
        $content = $this->render($pattern, 'float');
        $content = str_replace('+', ' + ', $content);
        $content = str_replace('-', ' - ', $content);
        $content = str_replace('*', ' * ', $content);
        $content = str_replace('/', ' / ', $content);
        $content = str_replace('(', ' ( ', $content);
        $content = str_replace(')', ' ) ', $content);
        
        // fix sintax for operator followed by signal
        foreach (['+', '-', '*', '/'] as $operator)
        {
            foreach (['+', '-'] as $signal)
            {
                $content = str_replace(" {$operator} {$signal} ", " {$operator} {$signal}", $content);
                $content = str_replace(" {$operator}  {$signal} ", " {$operator} {$signal}", $content);
                $content = str_replace(" {$operator}   {$signal} ", " {$operator} {$signal}", $content);
            }
        }
        
        $parser = new Parser;
        $content = $parser->evaluate(substr($content,1));
        return $content;
    }
    
    /**
     * Register an persisted attribute
     */
    public function addAttribute($attribute)
    {
        if ($attribute == 'data')
        {
            throw new Exception(AdiantiCoreTranslator::translate('Reserved property name (^1) in class ^2', $attribute, get_class($this)));
        }
        
        $this->attributes[] = $attribute;
    }
    
    /**
     * Return the persisted attributes
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    /**
     * Get attribute list
     */
    public function getAttributeList()
    {
        if (count($this->attributes) > 0)
        {
            $attributes = $this->attributes;
            array_unshift($attributes, $this->getPrimaryKey());
            return implode(', ', array_unique($attributes));
        }
        
        return '*';
    }
    
    /**
     * Store the objects into the database
     * @return      The number of affected rows
     * @exception   Exception if there's no active transaction opened
     */
    public function store()
    {
        $conn = TTransaction::get();
        
        if (!$conn)
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
        
        $driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
        
        // get the Active Record class name
        $class = get_class($this);
        
        // check if the object has an ID or exists in the database
        $pk = $this->getPrimaryKey();
        $createdat = $this->getCreatedAtColumn();
        $createdby = $this->getCreatedByColumn();
        $updatedat = $this->getUpdatedAtColumn();
        $updatedby = $this->getUpdatedByColumn();
        
        if (method_exists($this, 'onBeforeStore'))
        {
            $virtual_object = (object) $this->data;
            $this->onBeforeStore( $virtual_object );
            $this->data = (array) $virtual_object;
        }
        
        if (empty($this->data[$pk]) or (!self::exists($this->$pk)))
        {
            // increments the ID
            if (empty($this->data[$pk]))
            {
                if ((defined("{$class}::IDPOLICY")) AND (constant("{$class}::IDPOLICY") == 'serial'))
                {
                    unset($this->$pk);
                }
                else if ((defined("{$class}::IDPOLICY")) AND (constant("{$class}::IDPOLICY") == 'uuid'))
                {
                    $this->$pk = implode('-', [
                                     bin2hex(random_bytes(4)),
                                     bin2hex(random_bytes(2)),
                                     bin2hex(chr((ord(random_bytes(1)) & 0x0F) | 0x40)) . bin2hex(random_bytes(1)),
                                     bin2hex(chr((ord(random_bytes(1)) & 0x3F) | 0x80)) . bin2hex(random_bytes(1)),
                                     bin2hex(random_bytes(6))
                                 ]);
                }
                else
                {
                    $this->$pk = $this->getLastID() +1;
                }
            }
            // creates an INSERT instruction
            $sql = new TSqlInsert;
            $sql->setEntity($this->getEntity());
            // iterate the object data
            foreach ($this->data as $key => $value)
            {
                // check if the field is a calculated one
                if ( !method_exists($this, 'get_' . $key) OR (count($this->attributes) > 0) )
                {
                    if (count($this->attributes) > 0)
                    {
                        // set just attributes defined by the addAttribute()
                        if ((in_array($key, $this->attributes) AND is_string($key)) OR ($key === $pk))
                        {
                            // pass the object data to the SQL
                            $sql->setRowData($key, $this->data[$key]);
                        }
                    }
                    else
                    {
                        // pass the object data to the SQL
                        $sql->setRowData($key, $this->data[$key]);
                    }
                }
            }
            
            if (!empty($createdat))
            {
                $info = TTransaction::getDatabaseInfo();
                $date_mask = (in_array($info['type'], ['sqlsrv', 'dblib', 'mssql'])) ? 'Ymd H:i:s' : 'Y-m-d H:i:s';
                $sql->setRowData($createdat, date($date_mask));
            }
            
            if (!empty($createdby))
            {
                $sql->setRowData($createdby, $this->getByUserSessionIdentificator() );
            }
        }
        else
        {
            // creates an UPDATE instruction
            $sql = new TSqlUpdate;
            $sql->setEntity($this->getEntity());
            // creates a select criteria based on the ID
            $criteria = new TCriteria;
            $criteria->add(new TFilter($pk, '=', $this->$pk));
            $sql->setCriteria($criteria);
            // interate the object data
            foreach ($this->data as $key => $value)
            {
                if ($key !== $pk) // there's no need to change the ID value
                {
                    // check if the field is a calculated one
                    if ( !method_exists($this, 'get_' . $key) OR (count($this->attributes) > 0) )
                    {
                        if (count($this->attributes) > 0)
                        {
                            // set just attributes defined by the addAttribute()
                            if ((in_array($key, $this->attributes) AND is_string($key)) OR ($key === $pk))
                            {
                                // pass the object data to the SQL
                                $sql->setRowData($key, $this->data[$key]);
                            }
                        }
                        else
                        {
                            // pass the object data to the SQL
                            $sql->setRowData($key, $this->data[$key]);
                        }
                    }
                }
            }
            
            if (!empty($createdat))
            {
                $sql->unsetRowData($createdat);
            }
            
            if (!empty($updatedat))
            {
                $info = TTransaction::getDatabaseInfo();
                $date_mask = (in_array($info['type'], ['sqlsrv', 'dblib', 'mssql'])) ? 'Ymd H:i:s' : 'Y-m-d H:i:s';
                $sql->setRowData($updatedat, date($date_mask));
            }
            
            if (!empty($updatedby))
            {
                $sql->setRowData($updatedby, $this->getByUserSessionIdentificator() );
            }
        }
        
        // register the operation in the LOG file
        TTransaction::log($sql->getInstruction());
        
        $dbinfo = TTransaction::getDatabaseInfo(); // get dbinfo
        if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
        {
            $command = $sql->getInstruction( TRUE );
            
            if ($driver == 'firebird')
            {
                $command = str_replace('{{primary_key}}', $pk, $command);
            }
            else if ($driver == 'sqlsrv')
            {
                $command .= ";SELECT SCOPE_IDENTITY() as 'last_inserted_id'";
            }
            
            $result = $conn-> prepare ( $command , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $result-> execute ( $sql->getPreparedVars() );
        }
        else
        {
            $command = $sql->getInstruction();
            
            if ($driver == 'firebird')
            {
                $command = str_replace('{{primary_key}}', $pk, $command);
            }
            else if ($driver == 'sqlsrv')
            {
                $command .= ";SELECT SCOPE_IDENTITY() as 'last_inserted_id'";
            }
            
            // execute the query
            $result = $conn-> query($command);
        }
        
        if ((defined("{$class}::IDPOLICY")) AND (constant("{$class}::IDPOLICY") == 'serial'))
        {
            if ( ($sql instanceof TSqlInsert) AND empty($this->data[$pk]) )
            {
                if ($driver == 'firebird')
                {
                    $this->$pk = $result-> fetchColumn();
                }
                else if ($driver == 'sqlsrv')
                {
                    $result->nextRowset();
                    $this->$pk = $result-> fetchColumn();
                }
                else if (in_array($driver, array('oci', 'oci8')))
                {
                    $result_id = $conn-> query('SELECT ' . $this->getSequenceName() . ".currval FROM dual");
                    $this->$pk = $result_id-> fetchColumn();
                }
                else
                {
                    $this->$pk = $conn->lastInsertId( $this->getSequenceName() );
                }
            }
        }
        
        if ( $cache = $this->getCacheControl() )
        {
            $record_key = $class . '['. $this->$pk . ']';
            if ($cache::setValue( $record_key, $this->toArray() ))
            {
                TTransaction::log($record_key . ' stored in cache');
            }
        }
        
        if (method_exists($this, 'onAfterStore'))
        {
            $this->onAfterStore( (object) $this->toArray() );
        }
        
        // return the result of the exec() method
        return $result;
    }
    
    /**
     * Tests if an ID exists
     * @param $id  The object ID
     * @exception  Exception if there's no active transaction opened
     */
    public function exists($id)
    {
        if (empty($id))
        {
            return FALSE;
        }
        
        $class = get_class($this);     // get the Active Record class name
        $pk = $this->getPrimaryKey();  // discover the primary key name
        
        // creates a SELECT instruction
        $sql = new TSqlSelect;
        $sql->setEntity($this->getEntity());
        $sql->addColumn($this->getAttributeList());
        
        // creates a select criteria based on the ID
        $criteria = new TCriteria;
        $criteria->add(new TFilter($pk, '=', $id));
        $sql->setCriteria($criteria);
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            $dbinfo = TTransaction::getDatabaseInfo(); // get dbinfo
            if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
            {
                $result = $conn-> prepare ( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $result-> execute ( $criteria->getPreparedVars() );
            }
            else
            {
                $result = $conn-> query($sql->getInstruction());
            }
            
            // if there's a result
            if ($result)
            {
                // returns the data as an object of this class
                $object = $result-> fetchObject(get_class($this));
            }
            
            return is_object($object);
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    /**
     * ReLoad an Active Record Object from the database
     */
    public function reload()
    {
        // discover the primary key name 
        $pk = $this->getPrimaryKey();
        
        return $this->load($this->$pk);
    }
    
    /**
     * Load an Active Record Object from the database
     * @param $id  The object ID
     * @return     The Active Record Object
     * @exception  Exception if there's no active transaction opened
     */
    public function load($id)
    {
        $class = get_class($this);     // get the Active Record class name
        $pk = $this->getPrimaryKey();  // discover the primary key name
        
        if (method_exists($this, 'onBeforeLoad'))
        {
            $this->onBeforeLoad( $id );
        }
        
        if ( $cache = $this->getCacheControl() )
        {
            $record_key = $class . '['. $id . ']';
            if ($fetched_data = $cache::getValue( $record_key ))
            {
                $fetched_object = (object) $fetched_data;
                $loaded_object  = clone $this;
                if (method_exists($this, 'onAfterLoad'))
                {
                    $this->onAfterLoad( $fetched_object );
                    $loaded_object->fromArray( (array) $fetched_object);
                }
                else
                {
                    $loaded_object->fromArray($fetched_data);
                }
                TTransaction::log($record_key . ' loaded from cache');
                return $loaded_object;
            }
        }
        
        // creates a SELECT instruction
        $sql = new TSqlSelect;
        $sql->setEntity($this->getEntity());
        // use *, once this is called before addAttribute()s
        $sql->addColumn($this->getAttributeList());
        
        // creates a select criteria based on the ID
        $criteria = new TCriteria;
        $criteria->add(new TFilter($pk, '=', $id));

        $deletedat = self::getDeletedAtColumn();
        if (!$this->trashed && $deletedat)
        {
            $criteria->add(new TFilter($deletedat, 'IS', NULL));
        }

        // define the select criteria
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
                $result = $conn-> query($sql->getInstruction());
            }
            
            // if there's a result
            if ($result)
            {
                $activeClass = get_class($this);
                $fetched_object = $result-> fetchObject();
                if ($fetched_object)
                {
                    if (method_exists($this, 'onAfterLoad'))
                    {
                        $this->onAfterLoad($fetched_object);
                    }
                    $object = new $activeClass;
                    $object->fromArray( (array) $fetched_object );
                }
                else
                {
                    $object = NULL;
                }
                
                if ($object)
                {
                    if ( $cache = $this->getCacheControl() )
                    {
                        $record_key = $class . '['. $id . ']';
                        if ($cache::setValue( $record_key, $object->toArray() ))
                        {
                            TTransaction::log($record_key . ' stored in cache');
                        }
                    }
                }
            }
            
            return $object;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    /**
     * Load trashed records
     */
    public function loadTrashed($id)
    {
        $this->trashed = TRUE;
        return $this->load($id);
    }
    
    /**
     * Delete an Active Record object from the database
     * @param [$id]     The Object ID
     * @exception       Exception if there's no active transaction opened
     */
    public function delete($id = NULL)
    {
        $class = get_class($this);
        
        if (method_exists($this, 'onBeforeDelete'))
        {
            $this->onBeforeDelete( (object) $this->toArray() );
        }
        
        // discover the primary key name
        $pk = $this->getPrimaryKey();
        // if the user has not passed the ID, take the object ID
        $id = $id ? $id : $this->$pk;

        $deletedat = self::getDeletedAtColumn();
        $deletedby = self::getDeletedByColumn();
        
        if ($deletedat)
        {
            // creates a Update instruction
            $sql = new TSqlUpdate;
            $sql->setEntity($this->getEntity());

            $info = TTransaction::getDatabaseInfo();
            $date_mask = (in_array($info['type'], ['sqlsrv', 'dblib', 'mssql'])) ? 'Ymd H:i:s' : 'Y-m-d H:i:s';
            $sql->setRowData($deletedat, date($date_mask));
            
            if ($deletedby)
            {
                $sql->setRowData($deletedby, $this->getByUserSessionIdentificator() );
            }
        }
        else
        {
            // creates a DELETE instruction
            $sql = new TSqlDelete;
            $sql->setEntity($this->getEntity());
        }

        // creates a select criteria
        $criteria = new TCriteria;
        $criteria->add(new TFilter($pk, '=', $id));
        // assign the criteria to the delete instruction
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
                // execute the query
                $result = $conn-> query($sql->getInstruction());
            }
            
            if ( $cache = $this->getCacheControl() )
            {
                $record_key = $class . '['. $id . ']';
                if ($cache::delValue( $record_key ))
                {
                    TTransaction::log($record_key . ' deleted from cache');
                }
            }
            
            if (method_exists($this, 'onAfterDelete'))
            {
                $this->onAfterDelete( (object) $this->toArray() );
            }
            
            unset($this->data);
            
            // return the result of the exec() method
            return $result;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    /**
     * Restore soft deleted object
     */
    public function restore()
    {
        $deletedat = self::getDeletedAtColumn();
        
        if ($deletedat)
        {
            $pk = $this->getPrimaryKey();
            $this->withTrashed()->where($pk, '=', $this->$pk)->set($deletedat, null)->update();
            
            return $this;
        }
        else
        {
            throw new Exception(AdiantiCoreTranslator::translate('Softdelete is not active') . ' : '. $this->getEntity());
        }
    }

    /**
     * Returns the FIRST Object ID from database
     * @return      An Integer containing the FIRST Object ID from database
     * @exception   Exception if there's no active transaction opened
     */
    public function getFirstID()
    {
        $pk = $this->getPrimaryKey();
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            // instancia instrução de SELECT
            $sql = new TSqlSelect;
            $sql->addColumn("min({$pk}) as {$pk}");
            $sql->setEntity($this->getEntity());
            // register the operation in the LOG file
            TTransaction::log($sql->getInstruction());
            $result= $conn->Query($sql->getInstruction());
            // retorna os dados do banco
            $row = $result-> fetch();
            return $row[0];
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    /**
     * Returns the LAST Object ID from database
     * @return      An Integer containing the LAST Object ID from database
     * @exception   Exception if there's no active transaction opened
     */
    public function getLastID()
    {
        $pk = $this->getPrimaryKey();
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            // instancia instrução de SELECT
            $sql = new TSqlSelect;
            $sql->addColumn("max({$pk}) as {$pk}");
            $sql->setEntity($this->getEntity());
            // register the operation in the LOG file
            TTransaction::log($sql->getInstruction());
            $result= $conn->Query($sql->getInstruction());
            // retorna os dados do banco
            $row = $result-> fetch();
            return $row[0];
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    /**
     * Method getObjects
     * @param $criteria        Optional criteria
     * @param $callObjectLoad  If load() method from Active Records must be called to load object parts
     * @return                 An array containing the Active Records
     */
    public static function getObjects($criteria = NULL, $callObjectLoad = TRUE, $withTrashed = FALSE)
    {
        // get the Active Record class name
        $class = get_called_class();
        
        // create the repository
        $repository = new TRepository($class, $withTrashed);
        
        if (!$criteria)
        {
            $criteria = new TCriteria;
        }
        
        return $repository->load( $criteria, $callObjectLoad );
    }
    
    /**
     * Method countObjects
     * @param $criteria        Optional criteria
     * @param $withTrashed
     * @return                 An array containing the Active Records
     */
    public static function countObjects($criteria = NULL, $withTrashed = FALSE)
    {
        // get the Active Record class name
        $class = get_called_class();
        
        // create the repository
        $repository = new TRepository($class, $withTrashed);
        if (!$criteria)
        {
            $criteria = new TCriteria;
        }
        
        return $repository->count( $criteria );
    }
    
    /**
     * Load composite objects (parts in composition relationship)
     * @param $composite_class Active Record Class for composite objects
     * @param $foreign_key Foreign key in composite objects
     * @param $id Primary key of parent object
     * @returns Array of Active Records
     */
    public function loadComposite($composite_class, $foreign_key, $id = NULL, $order = NULL)
    {
        $pk = $this->getPrimaryKey(); // discover the primary key name
        $id = $id ? $id : $this->$pk; // if the user has not passed the ID, take the object ID
        $criteria = TCriteria::create( [$foreign_key => $id ], ['order' => $order] );
        $repository = new TRepository($composite_class);
        return $repository->load($criteria);
    }
    
    /**
     * Load composite objects. Shortcut for loadComposite
     * @param $composite_class Active Record Class for composite objects
     * @param $foreign_key Foreign key in composite objects
     * @param $primary_key Primary key of parent object
     * @returns Array of Active Records
     */
    public function hasMany($composite_class, $foreign_key = NULL, $primary_key = NULL, $order = NULL)
    {
        $foreign_key = isset($foreign_key) ? $foreign_key : $this->underscoreFromCamelCase(get_class($this)) . '_id';
        $primary_key = $primary_key ? $primary_key : $this->getPrimaryKey();
        return $this->loadComposite($composite_class, $foreign_key, $this->$primary_key, $order);
    }
    
    /**
     * Create a criteria to load composite objects
     * @param $composite_class Active Record Class for composite objects
     * @param $foreign_key Foreign key in composite objects
     * @param $primary_key Primary key of parent object
     * @returns TRepository instance
     */
    public function filterMany($composite_class, $foreign_key = NULL, $primary_key = NULL, $order = NULL)
    {
        $foreign_key = isset($foreign_key) ? $foreign_key : $this->underscoreFromCamelCase(get_class($this)) . '_id';
        $primary_key = $primary_key ? $primary_key : $this->getPrimaryKey();
        
        $criteria = TCriteria::create( [$foreign_key => $this->$primary_key ], ['order' => $order] );
        $repository = new TRepository($composite_class);
        $repository->setCriteria($criteria);
        return $repository;
    }
    
    /**
     * Delete composite objects (parts in composition relationship)
     * @param $composite_class Active Record Class for composite objects
     * @param $foreign_key Foreign key in composite objects
     * @param $id Primary key of parent object
     */
    public function deleteComposite($composite_class, $foreign_key, $id, $callObjectLoad = FALSE)
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter($foreign_key, '=', $id));
        
        $repository = new TRepository($composite_class);
        return $repository->delete($criteria, $callObjectLoad);
    }
    
    /**
     * Save composite objects (parts in composition relationship)
     * @param $composite_class Active Record Class for composite objects
     * @param $foreign_key Foreign key in composite objects
     * @param $id Primary key of parent object
     * @param $objects Array of Active Records to be saved
     */
    public function saveComposite($composite_class, $foreign_key, $id, $objects, $callObjectLoad = FALSE)
    {
        $this->deleteComposite($composite_class, $foreign_key, $id, $callObjectLoad);
        
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $object-> $foreign_key  = $id;
                $object->store();
            }
        }
    }
    
    /**
     * Load aggregated objects (parts in aggregation relationship)
     * @param $aggregate_class Active Record Class for aggregated objects
     * @param $join_class Active Record Join Class (Parent / Aggregated)
     * @param $foreign_key_parent Foreign key in Join Class to parent object
     * @param $foreign_key_child Foreign key in Join Class to child object
     * @param $id Primary key of parent object
     * @returns Array of Active Records
     */
    public function loadAggregate($aggregate_class, $join_class, $foreign_key_parent, $foreign_key_child, $id = NULL)
    {
        // discover the primary key name
        $pk = $this->getPrimaryKey();
        // if the user has not passed the ID, take the object ID
        $id = $id ? $id : $this->$pk;
        
        $criteria   = new TCriteria;
        $criteria->add(new TFilter($foreign_key_parent, '=', $id));
        
        $repository = new TRepository($join_class);
        $objects = $repository->load($criteria);
        
        $aggregates = array();
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $aggregates[] = new $aggregate_class($object-> $foreign_key_child);
            }
        }
        return $aggregates;
    }
    
    /**
     * Load aggregated objects. Shortcut to loadAggregate
     * @param $aggregate_class Active Record Class for aggregated objects
     * @param $join_class Active Record Join Class (Parent / Aggregated)
     * @param $foreign_key_parent Foreign key in Join Class to parent object
     * @param $foreign_key_child Foreign key in Join Class to child object
     * @returns Array of Active Records
     */
    public function belongsToMany($aggregate_class, $join_class = NULL, $foreign_key_parent = NULL, $foreign_key_child = NULL)
    {
        $class = get_class($this);
        $join_class = isset($join_class) ? $join_class : $class.$aggregate_class;
        $foreign_key_parent = isset($foreign_key_parent) ? $foreign_key_parent : $this->underscoreFromCamelCase($class) . '_id';
        $foreign_key_child  = isset($foreign_key_child)  ? $foreign_key_child  : $this->underscoreFromCamelCase($aggregate_class) . '_id';
        
        return $this->loadAggregate($aggregate_class, $join_class, $foreign_key_parent, $foreign_key_child);
    }
    
    /**
     * Save aggregated objects (parts in aggregation relationship)
     * @param $join_class Active Record Join Class (Parent / Aggregated)
     * @param $foreign_key_parent Foreign key in Join Class to parent object
     * @param $foreign_key_child Foreign key in Join Class to child object
     * @param $id Primary key of parent object
     * @param $objects Array of Active Records to be saved
     */
    public function saveAggregate($join_class, $foreign_key_parent, $foreign_key_child, $id, $objects)
    {
        $this->deleteComposite($join_class, $foreign_key_parent, $id);
        
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $join = new $join_class;
                $join-> $foreign_key_parent = $id;
                $join-> $foreign_key_child  = $object->id;
                $join->store();
            }
        }
    }
    
    /**
     * Returns the first object
     */
    public static function first($withTrashed = FALSE)
    {
        $object = new static;
        $id = $object->getFirstID();

        return self::find($id, $withTrashed);
    }
    
    /**
     * First record or a new one
     */
    public static function firstOrNew($filters = NULL)
    {
        $criteria = TCriteria::create($filters);
        $criteria->setProperty('limit', 1);
        $objects = self::getObjects( $criteria );
        
        if (isset($objects[0]))
        {
            return $objects[0];
        }
        else
        {
            $created = new static;
            if (is_array($filters))
            {
                $created->fromArray($filters);
            }
            return $created;
        }
    }
    
    /**
     * First record or persist a new one
     */
    public static function firstOrCreate($filters = NULL)
    {
        $obj = self::firstOrNew($filters);
        $obj->store();
        return $obj;
    }
    
    /**
     * Returns the last object
     */
    public static function last($withTrashed = FALSE)
    {
        $object = new static;
        $id = $object->getLastID();

        return self::find($id, $withTrashed);
    }
    
    /**
     * Find a Active Record and returns it
     * @return The Active Record itself or NULL when not found
     */
    public static function find($id, $withTrashed = FALSE)
    {
        $classname = get_called_class();
        $ar = new $classname;
        
        if ($withTrashed)
        {
            return $ar->loadTrashed($id);
        }
        else
        {
            return $ar->load($id);
        }
    }
    
    /**
     * Returns all objects
     */
    public static function all($indexed = false, $withTrashed = FALSE)
    {
        $objects = self::getObjects(NULL, FALSE, $withTrashed);
        if ($indexed)
        {
            $list = [];
            foreach ($objects as $object)
            {
                $pk = $object->getPrimaryKey();
                $list[ $object->$pk ] = $object;
            }
            return $list;
        }
        else
        {
            return $objects;
        }
    }
    
    /**
     * Save the object
     */
    public function save()
    {
        $this->store();
    }
    
    /**
     * Creates an indexed array
     * @returns the TRepository object with a filter
     */
    public static function getIndexedArray($indexColumn, $valueColumn, $criteria = NULL, $withTrashed = FALSE)
    {
        $sort_array = false;
        
        if (empty($criteria))
        {
            $criteria = new TCriteria;
            $sort_array = true;
        }
        
        $indexedArray = array();
        $class = get_called_class(); // get the Active Record class name
        $repository = new TRepository($class, $withTrashed); // create the repository
        $objects = $repository->load($criteria, FALSE);
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $key = (isset($object->$indexColumn)) ? $object->$indexColumn : $object->render($indexColumn);
                $val = (isset($object->$valueColumn)) ? $object->$valueColumn : $object->render($valueColumn);
                
                $indexedArray[ $key ] = $val;
            }
        }
        
        if ($sort_array)
        {
            asort($indexedArray);
        }
        return $indexedArray;
    }
    
    /**
     * Creates a Repository with filter
     * @returns the TRepository object with a filter
     */
    public static function select()
    {
        $repository = new TRepository( get_called_class() ); // create the repository
        return $repository->select( func_get_args() );
    }
    
    /**
     * Creates a Repository with group
     * @returns the TRepository object with a group
     */
    public static function groupBy($group)
    {
        $repository = new TRepository( get_called_class() ); // create the repository
        return $repository->groupBy($group);
    }
    
    /**
     * Creates a Repository with filter
     * @returns the TRepository object with a filter
     */
    public static function where($variable, $operator, $value, $logicOperator = TExpression::AND_OPERATOR)
    {
        $repository = new TRepository( get_called_class() ); // create the repository
        return $repository->where($variable, $operator, $value, $logicOperator);
    }
    
    /**
     * Creates a Repository with OR filter
     * @returns the TRepository object with an OR filter
     */
    public static function orWhere($variable, $operator, $value)
    {
        $repository = new TRepository( get_called_class() ); // create the repository
        return $repository->orWhere($variable, $operator, $value);
    }
    
    /**
     * Creates an ordered repository
     * @param  $order = Order column
     * @param  $direction = Order direction (asc, desc)
     * @returns the ordered TRepository object
     */
    public static function orderBy($order, $direction = 'asc')
    {
        $repository = new TRepository( get_called_class() ); // create the repository
        return $repository->orderBy( $order, $direction );
    }
    
    /**
     * Creates a Repository with limit
     * @returns the TRepository object
     */
    public static function take($limit)
    {
        $repository = new TRepository( get_called_class() ); // create the repository
        return $repository->take($limit);
    }
    
    /**
     * Creates a Repository with offset
     * @returns the TRepository object
     */
    public static function skip($offset)
    {
        $repository = new TRepository( get_called_class() ); // create the repository
        return $repository->skip($offset);
    }

    public static function withTrashed()
    {
        return new TRepository(get_called_class(), TRUE);
    }

    private function underscoreFromCamelCase($string)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$'.'1_$'.'2', $string)); 
    }
}
