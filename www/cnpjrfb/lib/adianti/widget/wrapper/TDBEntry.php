<?php
namespace Adianti\Widget\Wrapper;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TEntry;
use Adianti\Database\TCriteria;

use Exception;

/**
 * Database Entry Widget
 *
 * @version    7.4
 * @package    widget
 * @subpackage wrapper
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDBEntry extends TEntry
{
    protected $minLength;
    protected $service;
    protected $displayMask;
    private $database;
    private $model;
    private $column;
    private $operator;
    private $orderColumn;
    private $criteria;
    
    /**
     * Class Constructor
     * @param  $name     widget's name
     * @param  $database database name
     * @param  $model    model class name
     * @param  $value    table field to be listed in the combo
     * @param  $ordercolumn column to order the fields (optional)
     * @param  $criteria criteria (TCriteria object) to filter the model (optional)
     */
    public function __construct($name, $database, $model, $value, $orderColumn = NULL, TCriteria $criteria = NULL)
    {
        // executes the parent class constructor
        parent::__construct($name);
        
        $value = trim($value);
        
        if (empty($database))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'database', __CLASS__));
        }
        
        if (empty($model))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'model', __CLASS__));
        }
        
        if (empty($value))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'value', __CLASS__));
        }
        
        $this->minLength = 1;
        $this->database = $database;
        $this->model = $model;
        $this->column = $value;
        $this->displayMask = '{'.$value.'}';
        $this->operator = null;
        $this->orderColumn = isset($orderColumn) ? $orderColumn : NULL;
        $this->criteria = $criteria;
        $this->service = 'AdiantiAutocompleteService';
    }
    
    /**
     * Define the display mask
     * @param $mask Show mask
     */
    public function setDisplayMask($mask)
    {
        $this->displayMask = $mask;
    }
    
    /**
     * Define the search service
     * @param $service Search service
     */
    public function setService($service)
    {
        $this->service = $service;
    }
    
    /**
     * Define the minimum length for search
     */
    public function setMinLength($length)
    {
        $this->minLength = $length;
    }
    
    /**
     * Define the search operator
     * @param $operator Search operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }
    
    /**
     * Shows the widget
     */
    public function show()
    {
        parent::show();
        
        $min = $this->minLength;
        $orderColumn = isset($this->orderColumn) ? $this->orderColumn : $this->column;
        $criteria = '';
        if ($this->criteria)
        {
            $criteria = base64_encode(serialize($this->criteria));
        }
        
        $seed = APPLICATION_NAME.'s8dkld83kf73kf094';
        $hash = md5("{$seed}{$this->database}{$this->column}{$this->model}");
        $length = $this->minLength;
        
        $class = $this->service;
        $callback = array($class, 'onSearch');
        $method = $callback[1];
        $url = "engine.php?class={$class}&method={$method}&static=1&database={$this->database}&column={$this->column}&model={$this->model}&orderColumn={$orderColumn}&criteria={$criteria}&operator={$this->operator}&hash={$hash}&mask={$this->displayMask}";
        
        TScript::create(" tdbentry_start( '{$this->name}', '{$url}', '{$min}' );");
    }
}
