<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Core\AdiantiApplicationConfig;
use Adianti\Control\TPage;
use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TField;

use Exception;

/**
 * Multi Search Widget
 *
 * @version    7.1
 * @package    widget
 * @subpackage form
 * @author     Matheus Agnes Dias
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMultiSearch extends TSelect implements AdiantiWidgetInterface
{
    protected $id;
    protected $items;
    protected $size;
    protected $height;
    protected $minLength;
    protected $maxSize;
    protected $editable;
    protected $changeAction;
    protected $changeFunction;
    protected $allowClear;
    protected $allowSearch;
    protected $separator;
    protected $value;
    
    /**
     * Class Constructor
     * @param  $name Widget's name
     */
    public function __construct($name)
    {
        // executes the parent class constructor
        parent::__construct($name);
        $this->id   = 'tmultisearch_'.mt_rand(1000000000, 1999999999);

        $this->height = 100;
        $this->minLength = 3;
        $this->maxSize = 0;
        $this->allowClear = TRUE;
        $this->allowSearch = TRUE;
        
        parent::setDefaultOption(FALSE);
        $this->tag->{'component'} = 'multisearch';
        $this->tag->{'widget'} = 'tmultisearch';
    }
    
    /**
     * Disable multiple selection
     */
    public function disableMultiple()
    {
        unset($this->tag->{'multiple'});
    }
    
    /**
     * Disable clear
     */
    public function disableClear()
    {
        $this->allowClear = FALSE;
    }
    
    /**
     * Disable search
     */
    public function disableSearch()
    {
        $this->allowSearch = FALSE;
    }
    
    /**
     * Define the widget's size
     * @param  $width   Widget's width
     * @param  $height  Widget's height
     */
    public function setSize($width, $height = NULL)
    {
        $this->size   = $width;
        if ($height)
        {
            $this->height = $height;
        }
    }

    /**
     * Returns the size
     * @return array(width, height)
     */
    public function getSize()
    {
        return array( $this->size, $this->height );
    }
    
    /**
     * Define the minimum length for search
     */
    public function setMinLength($length)
    {
        $this->minLength = $length;
    }

    /**
     * Define the maximum number of items that can be selected
     */
    public function setMaxSize($maxsize)
    {
        $this->maxSize = $maxsize;
        
        if ($maxsize == 1)
        {
            unset($this->height);
            parent::setDefaultOption(TRUE);
        }
    }
    
    /**
     * Define the field's separator
     * @param $sep A string containing the field's separator
     */
    public function setValueSeparator($sep)
    {
        $this->separator = $sep;
    }
    
    /**
     * Define the field's value
     * @param $value A string containing the field's value
     */
    public function setValue($value)
    {
        $ini = AdiantiApplicationConfig::get();
        
        if (isset($ini['general']['compat']) AND $ini['general']['compat'] ==  '4')
        {
            if ($value)
            {
                parent::setValue(array_keys((array)$value));
            }
        }
        else
        {
            parent::setValue($value);
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
                    foreach ($values as $item)
                    {
                        $return[$item] = $this->items[$item];
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
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " tmultisearch_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " tmultisearch_disable_field('{$form_name}', '{$field}'); " );
    }

    /**
     * Clear the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function clearField($form_name, $field)
    {
        TScript::create( " tmultisearch_clear_field('{$form_name}', '{$field}'); " );
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
            $this->tag->{'name'}  = $this->name.'[]'; // tag name
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
        $search_word = !empty($this->getProperty('placeholder'))? $this->getProperty('placeholder') : AdiantiCoreTranslator::translate('Search');
        $change_action = 'function() {}';
        $allowclear  = $this->allowClear  ? 'true' : 'false';
        $allowsearch = $this->allowSearch ? '1' : 'Infinity';
        
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
        
        TScript::create(" tmultisearch_start( '{$this->id}', '{$this->minLength}', '{$this->maxSize}', '{$search_word}', $multiple, '{$size}', '{$this->height}px', {$allowclear}, {$allowsearch}, $change_action ); ");
        
        if (!$this->editable)
        {
            TScript::create(" tmultisearch_disable_field( '{$this->formName}', '{$this->name}'); ");
        }
    }
}
