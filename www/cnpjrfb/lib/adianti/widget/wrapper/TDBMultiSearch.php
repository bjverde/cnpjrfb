<?php
namespace Adianti\Widget\Wrapper;

use Adianti\Core\AdiantiApplicationConfig;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TMultiSearch;
use Adianti\Database\TTransaction;
use Adianti\Database\TCriteria;
use Adianti\Widget\Form\TForm;

use Exception;

/**
 * Database Multisearch Widget
 *
 * @version    7.3
 * @package    widget
 * @subpackage wrapper
 * @author     Pablo Dall'Oglio
 * @author     Matheus Agnes Dias
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDBMultiSearch extends TMultiSearch
{
    protected $id;
    protected $initialItems;
    protected $items;
    protected $size;
    protected $height;
    protected $minLength;
    protected $maxSize;
    protected $database;
    protected $model;
    protected $key;
    protected $column;
    protected $operator;
    protected $orderColumn;
    protected $criteria;
    protected $mask;
    protected $service;
    protected $seed;
    protected $editable;
    protected $changeFunction;
    protected $idSearch;
    protected $idTextSearch;
    
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
    public function __construct($name, $database, $model, $key, $value, $orderColumn = NULL, TCriteria $criteria = NULL)
    {
        // executes the parent class constructor
        parent::__construct($name);
        $this->id   = 'tdbmultisearch_'.mt_rand(1000000000, 1999999999);
        
        $key   = trim($key);
        $value = trim($value);
        
        if (empty($database))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'database', __CLASS__));
        }
        
        if (empty($model))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'model', __CLASS__));
        }
        
        if (empty($key))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'key', __CLASS__));
        }
        
        if (empty($value))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'value', __CLASS__));
        }
        
        $ini = AdiantiApplicationConfig::get();
        
        $this->database = $database;
        $this->model = $model;
        $this->key = $key;
        $this->column = $value;
        $this->operator = null;
        $this->orderColumn = isset($orderColumn) ? $orderColumn : NULL;
        $this->criteria = $criteria;
        
        if (strpos($value,',') !== false)
        {
            $columns = explode(',', $value);
            $this->mask = '{'.$columns[0].'}';
        }
        else
        {
            $this->mask = '{'.$value.'}';
        }
        
        $this->service = 'AdiantiMultiSearchService';
        $this->seed = APPLICATION_NAME . ( !empty($ini['general']['seed']) ? $ini['general']['seed'] : 's8dkld83kf73kf094' );
        $this->tag->{'widget'} = 'tdbmultisearch';
        $this->idSearch = true;
        $this->idTextSearch = false;
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
     * Disable search by id
     */
    public function disableIdSearch()
    {
        $this->idSearch = false;
    }
    
    /**
     * Enable Id textual search
     */
    public function enableIdTextualSearch()
    {
        $this->idTextSearch = true;
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
     * Define the display mask
     * @param $mask Show mask
     */
    public function setMask($mask)
    {
        $this->mask = $mask;
    }
    
    /**
     * Define the field's value
     * @param $values An array the field's values
     */
    public function setValue($values)
    {
        $original_values = $values;
        $ini = AdiantiApplicationConfig::get();
        
        if (isset($ini['general']['compat']) AND $ini['general']['compat'] ==  '4')
        {
            if ($values)
            {
                parent::setValue( $values );
                parent::addItems( $values );
            }
        }
        else
        {
            $items = [];
            if ($values)
            {
                if (!empty($this->separator))
                {
                    $values = explode($this->separator, $values);
                }
                
                TTransaction::open($this->database);
                foreach ($values as $value)
                {
                    if ($value)
                    {
                        $model = $this->model;
                        
                        $pk = constant("{$model}::PRIMARYKEY");
                        
                        if ($pk === $this->key) // key is the primary key (default)
                        {
                            // use find because it uses cache
                            $object = $model::find( $value );
                        }
                        else // key is an alternative key (uses where->first)
                        {
                            $object = $model::where( $this->key, '=', $value )->first();
                        }
                        
                        if ($object)
                        {
                            $description = $object->render($this->mask);
                            $items[$value] = $description;
                        }
                    }
                }
                TTransaction::close();
                
                parent::addItems( $items );
            }
            parent::setValue( $original_values );
        }
    }
    
    /**
     * Return the post data
     */
    public function getPostData()
    {
        $ini = AdiantiApplicationConfig::get();
        
        if (isset($_POST[$this->name]))
        {
            $values = $_POST[$this->name];
            
            if (isset($ini['general']['compat']) AND $ini['general']['compat'] ==  '4')
            {
                $return = [];
                if (is_array($values))
                {
                    TTransaction::open($this->database);
                    foreach ($values as $value)
                    {
                        if ($value)
                        {
                            $model = $this->model;
                            $pk = constant("{$model}::PRIMARYKEY");
                            
                            if ($pk === $this->key) // key is the primary key (default)
                            {
                                // use find because it uses cache
                                $object = $model::find( $value );
                            }
                            else // key is an alternative key (uses where->first)
                            {
                                $object = $model::where( $this->key, '=', $value )->first();
                            }
                            
                            if ($object)
                            {
                                $description = $object->render($this->mask);
                                $return[$value] = $description;
                            }
                        }
                    }
                }
                return $return;
            }
            else
            {
                if (empty($this->separator))
                {
                    return $values;
                }
                else
                {
                    return implode($this->separator, $values);
                }
            }
        }
        else
        {
            return '';
        }
    }
    
    /**
     * Shows the widget
     */
    public function show()
    {
        // define the tag properties
        $this->tag->{'id'}    = $this->id; // tag id
        
        if (empty($this->tag->{'name'})) // may be defined by child classes
        {
            $this->tag->{'name'}  = $this->name.'[]';  // tag name
        }
        
        if (strstr($this->size, '%') !== FALSE)
        {
            $this->setProperty('style', "width:{$this->size};", false); //aggregate style info
            $size  = "{$this->size}";
        }
        else
        {
            $this->setProperty('style', "width:{$this->size}px;", false); //aggregate style info
            $size  = "{$this->size}px";
        }
        
        $multiple = $this->maxSize == 1 ? 'false' : 'true';
        $orderColumn = isset($this->orderColumn) ? $this->orderColumn : $this->column;
        $criteria = '';
        if ($this->criteria)
        {
            $criteria = str_replace(array('+', '/'), array('-', '_'), base64_encode(serialize($this->criteria)));
        }
        
        $hash = md5("{$this->seed}{$this->database}{$this->key}{$this->column}{$this->model}");
        $length = $this->minLength;
        
        $class = $this->service;
        $callback = array($class, 'onSearch');
        $method = $callback[1];
        $id_search_string = $this->idSearch ? '1' : '0';
        $id_text_search = $this->idTextSearch ? '1' : '0';
        $search_word = !empty($this->getProperty('placeholder'))? $this->getProperty('placeholder') : AdiantiCoreTranslator::translate('Search');
        $url = "engine.php?class={$class}&method={$method}&static=1&database={$this->database}&key={$this->key}&column={$this->column}&model={$this->model}&orderColumn={$orderColumn}&criteria={$criteria}&operator={$this->operator}&mask={$this->mask}&idsearch={$id_search_string}&idtextsearch={$id_text_search}&minlength={$length}";
        
        if ($router = AdiantiCoreApplication::getRouter())
        {
	        $url = $router($url, false);
        }

        $change_action = 'function() {}';
        
        if (isset($this->changeAction))
        {
            if (!TForm::getFormByName($this->formName) instanceof TForm)
            {
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
            }
            
            $string_action = $this->changeAction->serialize(FALSE);
            $change_action = "function() { __adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback'); }";
            $this->setProperty('changeaction', "__adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback')");
        }
        else if (isset($this->changeFunction))
        {
            $change_action = "function() { $this->changeFunction }";
            $this->setProperty('changeaction', $this->changeFunction, FALSE);
        }
        
        // shows the component
        parent::renderItems( false );
        $this->tag->show();
        
        TScript::create(" tdbmultisearch_start( '{$this->id}', '{$length}', '{$this->maxSize}', '{$search_word}', $multiple, '{$url}', '{$size}', '{$this->height}px', '{$hash}', {$change_action} ); ");
        
        if (!$this->editable)
        {
            TScript::create(" tmultisearch_disable_field( '{$this->formName}', '{$this->name}'); ");
        }
    }
}
