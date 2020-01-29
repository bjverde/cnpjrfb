<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TAction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TField;

use Exception;

/**
 * Select Widget
 *
 * @version    7.1
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSelect extends TField implements AdiantiWidgetInterface
{
    protected $id;
    protected $height;
    protected $items; // array containing the combobox options
    protected $formName;
    protected $changeFunction;
    protected $changeAction;
    protected $defaultOption;
    protected $separator;
    protected $value;
    protected $withTitles;
    
    /**
     * Class Constructor
     * @param  $name widget's name
     */
    public function __construct($name)
    {
        // executes the parent class constructor
        parent::__construct($name);
        $this->id   = 'tselect_' . mt_rand(1000000000, 1999999999);
        $this->defaultOption = '';
        $this->withTitles = true;
        
        // creates a <select> tag
        $this->tag = new TElement('select');
        $this->tag->{'class'} = 'tselect'; // CSS
        $this->tag->{'multiple'} = '1';
        $this->tag->{'widget'} = 'tselect';
    }
    
    
    /**
     * Disable multiple selection
     */
    public function disableMultiple()
    {
        unset($this->tag->{'multiple'});
        $this->tag->{'size'} = 3;
    }

    /**
     * Disable option titles
     */
    public function disableTitles()
    {
        $this->withTitles = false;
    }
    
    public function setDefaultOption($option)
    {
        $this->defaultOption = $option;
    }
    
    /**
     * Add items to the select
     * @param $items An indexed array containing the combo options
     */
    public function addItems($items)
    {
        if (is_array($items))
        {
            $this->items = $items;
        }
    }
    
    /**
     * Return the items
     */
    public function getItems()
    {
        return $this->items;
    }
    
    /**
     * Define the Field's width
     * @param $width Field's width in pixels
     * @param $height Field's height in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $this->size = $width;
        $this->height = $height;
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
        if (empty($this->separator))
        {
            $this->value = $value;
        }
        else
        {
            if ($value)
            {
                $this->value = explode($this->separator, $value);
            }
        }
    }
    
    /**
     * Return the post data
     */
    public function getPostData()
    {
        if (isset($_POST[$this->name]))
        {
            if ($this->tag->{'multiple'})
            {
                if (empty($this->separator))
                {
                    return $_POST[$this->name];
                }
                else
                {
                    return implode($this->separator, $_POST[$this->name]);
                }
            }
            else
            {
                return $_POST[$this->name][0];
            }
        }
        else
        {
            return array();
        }
    }
    
    /**
     * Define the action to be executed when the user changes the combo
     * @param $action TAction object
     */
    public function setChangeAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->changeAction = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
    }
    
    /**
     * Set change function
     */
    public function setChangeFunction($function)
    {
        $this->changeFunction = $function;
    }
    
    /**
     * Reload combobox items after it is already shown
     * @param $formname form name (used in gtk version)
     * @param $name field name
     * @param $items array with items
     * @param $startEmpty ...
     */
    public static function reload($formname, $name, $items, $startEmpty = FALSE)
    {
        $code = "tselect_clear('{$formname}', '{$name}'); ";
        if ($startEmpty)
        {
            $code .= "tselect_add_option('{$formname}', '{$name}', '', ''); ";
        }
        
        if ($items)
        {
            foreach ($items as $key => $value)
            {
                $code .= "tselect_add_option('{$formname}', '{$name}', '{$key}', '{$value}'); ";
            }
        }
        TScript::create($code);
    }
    
    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " tselect_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " tselect_disable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Clear the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function clearField($form_name, $field)
    {
        TScript::create( " tselect_clear_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Render items
     */
    protected function renderItems( $with_titles = true )
    {
        if ($this->defaultOption !== FALSE)
        {
            // creates an empty <option> tag
            $option = new TElement('option');
            
            $option->add( $this->defaultOption );
            $option->{'value'} = '';   // tag value

            // add the option tag to the combo
            $this->tag->add($option);
        }
        
        if ($this->items)
        {
            // iterate the combobox items
            foreach ($this->items as $chave => $item)
            {
                if (substr($chave, 0, 3) == '>>>')
                {
                    $optgroup = new TElement('optgroup');
                    $optgroup->{'label'} = $item;
                    // add the option to the combo
                    $this->tag->add($optgroup);
                }
                else
                {
                    // creates an <option> tag
                    $option = new TElement('option');
                    $option->{'value'} = $chave;  // define the index
                    if ($with_titles)
                    {
                        $option->{'title'} = $item;  // define the title
                    }
                    $option->{'titside'} = 'left';  // define the title side
                    $option->add(htmlspecialchars($item));      // add the item label
                    
                    // verify if this option is selected
                    if ( (is_array($this->value)  AND @in_array($chave, $this->value)) OR
                         (is_scalar($this->value) AND strlen( (string) $this->value ) > 0 AND @in_array($chave, (array) $this->value)))
                    {
                        // mark as selected
                        $option->{'selected'} = 1;
                    }
                    
                    if (isset($optgroup))
                    {
                        $optgroup->add($option);
                    }
                    else
                    {
                        $this->tag->add($option);
                    }                    
                }
            }
        }
    }
    
    /**
     * Shows the widget
     */
    public function show()
    {
        // define the tag properties
        $this->tag->{'name'}  = $this->name.'[]';    // tag name
        $this->tag->{'id'}    = $this->id;
        
        $this->setProperty('style', (strstr($this->size, '%') !== FALSE)   ? "width:{$this->size}"    : "width:{$this->size}px",   false); //aggregate style info
        $this->setProperty('style', (strstr($this->height, '%') !== FALSE) ? "height:{$this->height}" : "height:{$this->height}px", false); //aggregate style info
        
        // verify whether the widget is editable
        if (parent::getEditable())
        {
            if (isset($this->changeAction))
            {
                if (!TForm::getFormByName($this->formName) instanceof TForm)
                {
                    throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
                }
                
                $string_action = $this->changeAction->serialize(FALSE);
                $this->setProperty('changeaction', "__adianti_post_lookup('{$this->formName}', '{$string_action}', this, 'callback')");
                $this->setProperty('onChange', $this->getProperty('changeaction'));
            }
            
            if (isset($this->changeFunction))
            {
                $this->setProperty('changeaction', $this->changeFunction, FALSE);
                $this->setProperty('onChange', $this->changeFunction, FALSE);
            }
        }
        else
        {
            // make the widget read-only
            $this->tag->{'onclick'} = "return false;";
            $this->tag->{'style'}  .= ';pointer-events:none';
            $this->tag->{'class'}   = 'tselect_disabled'; // CSS
        }
        
        // shows the widget
        $this->renderItems( $this->withTitles );
        $this->tag->show();
    }
}
