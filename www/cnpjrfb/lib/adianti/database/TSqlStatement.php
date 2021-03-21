<?php
namespace Adianti\Database;

use Adianti\Database\TCriteria;

/**
 * Provides an abstract Interface to create a SQL statement
 *
 * @version    7.3
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
abstract class TSqlStatement
{
    protected $sql;         // stores the SQL instruction
    protected $criteria;    // stores the select criteria
    protected $entity;
    
    /**
     * defines the database entity name
     * @param $entity Name of the database entity
     */
    final public function setEntity($entity)
    {
        $this->entity = $entity;
    }
    
    /**
     * Returns the database entity name
     */
    final public function getEntity()
    {
        return $this->entity;
    }
    
    /**
     * Define a select criteria
     * @param $criteria  An TCriteria object, specifiyng the filters
     */
    public function setCriteria(TCriteria $criteria)
    {
        $this->criteria = $criteria;
    }
    
    /**
     * Returns a random parameter
     */
    protected function getRandomParameter()
    {
        return mt_rand(1000000000, 1999999999);
    }
    
    // force method rewrite in child classes
    abstract function getInstruction();
}
