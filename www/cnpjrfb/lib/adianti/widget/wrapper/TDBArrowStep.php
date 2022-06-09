<?php

use Adianti\Database\TCriteria;
use Adianti\Widget\Wrapper\AdiantiDatabaseWidgetTrait;

/**
 * Database Arrow Step
 *
 * @version    7.4
 * @package    widget
 * @subpackage util
 * @author     Lucas Tomasi
 * @author     Matheus Agnes Dias
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDBArrowStep extends TArrowStep
{
    protected $items; // array containing the combobox options
    
    private $database;
    private $model;
    private $key;
    private $value;
    private $ordercolumn;
    private $colorcolumn;
    private $criteria;

    use AdiantiDatabaseWidgetTrait;
    
    /**
     * Class Constructor
     * @param  $name     widget's name
     * @param  $database database name
     * @param  $model    model class name
     * @param  $key      table field to be used as key in the combo
     * @param  $value    table field to be listed in the combo
     * @param  $ordercolumn column to order the fields (optional)
     * @param  $criteria criteria (TCriteria object) to filter the model (optional)
     */
    public function __construct($name, $database, $model, $key, $value, $ordercolumn = NULL, TCriteria $criteria = NULL)
    {
        // executes the parent class constructor
        parent::__construct($name);

        $this->database = $database;
        $this->model = $model;
        $this->key = $key;
        $this->value = $value;
        $this->ordercolumn = $ordercolumn;
        $this->criteria = $criteria;
    }

    public function setColorColumn($colorcolumn)
    {
        $this->colorcolumn = $colorcolumn;
    }

    public function show()
    {
        parent::setItems( self::getItemsFromModel($this->database, $this->model, $this->key, $this->value, $this->ordercolumn, $this->criteria) );

        if ($this->colorcolumn)
        {
            parent::setColorItems( self::getItemsFromModel($this->database, $this->model, $this->key, $this->colorcolumn, $this->ordercolumn, $this->criteria) );
        }

        parent::show();
    }
}