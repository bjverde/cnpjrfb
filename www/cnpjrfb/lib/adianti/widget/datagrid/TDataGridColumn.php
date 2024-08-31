<?php
namespace Adianti\Widget\Datagrid;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TAction;
use Adianti\Widget\Form\TEntry;

/**
 * Representes a DataGrid column
 *
 * @version    7.6
 * @package    widget
 * @subpackage datagrid
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TDataGridColumn
{
    private $name;
    private $label;
    private $align;
    private $width;
    private $action;
    private $editaction;
    private $transformer;
    private $properties;
    private $dataProperties;
    private $totalFunction;
    private $totalMask;
    private $totalCallback;
    private $totalTransformed;
    private $searchable;
    private $inputSearch;
    private $htmlConversion;
    
    /**
     * Class Constructor
     * @param  $name  = Name of the column in the database
     * @param  $label = Text label that will be shown in the header
     * @param  $align = Column align (left, center, right)
     * @param  $width = Column Width (pixels)
     */
    public function __construct($name, $label, $align, $width = NULL)
    {
        $this->name  = $name;
        $this->label = $label;
        $this->align = $align;
        $this->width = $width;
        $this->searchable = false;
        $this->properties = array();
        $this->dataProperties = array();
        $this->htmlConversion = true;
    }
    
    /**
     * Define column visibility
     */
    public function setVisibility($bool)
    {
        if ($bool)
        {
            $this->setProperty('style', '');
            $this->setDataProperty('style', '');
        }
        else
        {
            $this->setProperty('style', 'display:none');
            $this->setDataProperty('style', 'display:none');
        }
    }
    
    /**
     * Enable column auto hide
     */
    public function enableAutoHide($width)
    {
        $this->setProperty('hiddable', $width);
        $this->setDataProperty('hiddable', $width);
    }
    
    /**
     * Enable column search
     */
    public function enableSearch()
    {
        $this->searchable = true;
        
        $name = 'search_' . str_replace(['-', '>'],['_', ''],$this->name) . '_' . uniqid();
        
        $this->inputSearch = new TEntry($name);
        $this->inputSearch->setId($name);
        $this->inputSearch->{'placeholder'} = AdiantiCoreTranslator::translate('Search');
        $this->inputSearch->setSize('50%');
    }
    
    /**
     * Enable htmlspecialchars on output
     */
    public function enableHtmlConversion()
    {
        $this->htmlConversion = true;
    }
    
    /**
     * Disable htmlspecialchars on output
     */
    public function disableHtmlConversion()
    {
        $this->htmlConversion = false;
    }
    
    /**
     * return if has html conversion
     */
    public function hasHtmlConversionEnabled()
    {
        return $this->htmlConversion;
    }
    
    /**
     * Get input search
     */
    public function getInputSearch()
    {
        return $this->inputSearch;
    }
    
    /**
     * Returns if column is searchable
     */
    public function isSearchable()
    {
        return $this->searchable;
    }
    
    /**
     * Define a column header property
     * @param $name  Property Name
     * @param $value Property Value
     */
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;
    }
    
    /**
     * Define a data property
     * @param $name  Property Name
     * @param $value Property Value
     */
    public function setDataProperty($name, $value)
    {
        $this->dataProperties[$name] = $value;
    }
    
    /**
     * Return a column property
     * @param $name  Property Name
     */
    public function getProperty($name)
    {
        if (isset($this->properties[$name]))
        {
            return $this->properties[$name];
        }
    }
    
    /**
     * Return a data property
     * @param $name  Property Name
     */
    public function getDataProperty($name)
    {
        if (isset($this->dataProperties[$name]))
        {
            return $this->dataProperties[$name];
        }
    }
    
    /**
     * Return column properties
     */
    public function getProperties()
    {
        return $this->properties;
    }
    
    /**
     * Return data properties
     */
    public function getDataProperties()
    {
        return $this->dataProperties;
    }
    
    /**
     * Intercepts whenever someones assign a new property's value
     * @param $name     Property Name
     * @param $value    Property Value
     */
    public function __set($name, $value)
    {
        // objects and arrays are not set as properties
        if (is_scalar($value))
        {              
            // store the property's value
            $this->setProperty($name, $value);
        }
    }
    
    /**
     * Returns the database column's name
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Returns the column's label
     */
    public function getLabel()
    {
        return $this->label;
    }
    
    /**
     * Set the column's label
     * @param $label column label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }
    
    /**
     * Returns the column's align
     */
    public function getAlign()
    {
        return $this->align;
    }
    
    /**
     * Returns the column's width
     */
    public function getWidth()
    {
        return $this->width;
    }
    
    /**
     * Define the action to be executed when
     * the user clicks over the column header
     * @param $action     TAction object
     * @param $parameters Action parameters
     */
    public function setAction(TAction $action, $parameters = null)
    {
        $this->action = $action;
        
        if ($parameters)
        {
            $this->action->setParameters($parameters);
        }
    }
    
    /**
     * Returns the action defined by set_action() method
     * @return the action to be executed when the
     * user clicks over the column header
     */
    public function getAction()
    {
        // verify if the column has an actions
        if ($this->action)
        {
            return $this->action;
        }
    }
    
    /**
     * Remove action
     */
    public function removeAction()
    {
        $this->action = null;
    }
    
    /**
     * Define the action to be executed when
     * the user clicks do edit the column
     * @param $action   A TDataGridAction object
     */
    public function setEditAction(TDataGridAction $editaction)
    {
        $this->editaction = $editaction;
    }
    
    /**
     * Returns the action defined by setEditAction() method
     * @return the action to be executed when the
     * user clicks do edit the column
     */
    public function getEditAction()
    {
        // verify if the column has an actions
        if ($this->editaction)
        {
            return $this->editaction;
        }
    }
    
    /**
     * Define a callback function to be applyed over the column's data
     * @param $callback  A function name of a method of an object
     */
    public function setTransformer(Callable $callback)
    {
        $this->transformer = $callback;
    }

    /**
     * Returns the callback defined by the setTransformer()
     */
    public function getTransformer()
    {
        return $this->transformer;
    }
    
    /**
     * Enable total
     */
    public function enableTotal($function, $prefix = null, $decimals = 2, $decimal_separator = ',', $thousand_separator = '.')
    {
        $this->totalFunction = $function;
        $this->totalMask     = "{$prefix}:{$decimals}{$decimal_separator}{$thousand_separator}";
        
        if ($function == 'sum')
        {
            $totalCallback = function($values) {
                return array_sum($values);
            };
            
            $this->setTotalFunction( $totalCallback );
        }
    }
    
    /**
     * Define a callback function to totalize column
     * @param $callback  A function name of a method of an object
     * @param $apply_transformer Apply transform function also in total
     */
    public function setTotalFunction(Callable $callback, $apply_transformer = true)
    {
        $this->totalCallback = $callback;
        $this->totalTransformed = $apply_transformer;
    }
    
    /**
     * Returns the callback defined by the setTotalFunction()
     */
    public function getTotalCallback()
    {
        return $this->totalCallback;
    }
    
    /**
     * Returns total function
     */
    public function getTotalFunction()
    {
        return $this->totalFunction;
    }
    
    /**
     * Returns total mask
     */
    public function getTotalMask()
    {
        return $this->totalMask;
    }
    
    /**
     * Is total transformed
     */
    public function totalTransformed()
    {
        return $this->totalTransformed;
    }
}
