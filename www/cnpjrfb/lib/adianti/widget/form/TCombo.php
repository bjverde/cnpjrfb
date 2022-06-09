<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TField;
use Exception;

/**
 * ComboBox Widget
 *
 * @version    7.4
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TCombo extends TField implements AdiantiWidgetInterface
{
    protected $id;
    protected $items; // array containing the combobox options
    protected $formName;
    private   $searchable;
    private   $changeAction;
    private   $defaultOption;
    protected $changeFunction;
    protected $is_boolean;

    /**
     * Class Constructor
     * @param  $name widget's name
     */
    public function __construct($name)
    {
        // executes the parent class constructor
        parent::__construct($name);
        
        $this->id = 'tcombo_'.mt_rand(1000000000, 1999999999);
        $this->defaultOption = '';

        // creates a <select> tag
        $this->tag = new TElement('select');
        $this->tag->{'class'}  = 'tcombo'; // CSS
        $this->tag->{'widget'} = 'tcombo';
        $this->is_boolean = FALSE;
    }
    
    /**
     * Enable/disable boolean mode
     */
    public function setBooleanMode()
    {
        $this->is_boolean = true;
        $this->addItems( [ '1' => AdiantiCoreTranslator::translate('Yes'),
                           '2' => AdiantiCoreTranslator::translate('No') ] );
                           
        // if setValue() was called previously
        if ($this->value === true)
        {
            $this->value = '1';
        }
        else if ($this->value === false)
        {
            $this->value = '2';
        }
    }
    
    /**
     * Define the field's value
     * @param $value A string containing the field's value
     */
    public function setValue($value)
    {
        if ($this->is_boolean)
        {
            $this->value = $value ? '1' : '2';
        }
        else
        {
            parent::setValue($value);
        }
    }
    
    /**
     * Returns the field's value
     */
    public function getValue()
    {
        if ($this->is_boolean)
        {
            return $this->value == '1' ? true : false;
        }
        else
        {
            return parent::getValue();
        }
    }
    
    /**
     * Clear combo
     */
    public function clear()
    {
        $this->items = array();
    }
    
    /**
     * Add items to the combo box
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
     * Return the combo items
     */
    public function getItems()
    {
        return $this->items;
    }
    
    /**
     * Enable search
     */
    public function enableSearch()
    {
        unset($this->tag->{'class'});
        $this->searchable = true;
    }
    
    /**
     * Return the post data
     */
    public function getPostData()
    {
        $name = str_replace(['[',']'], ['',''], $this->name);
        
        if (isset($_POST[$name]))
        {
            $val = $_POST[$name];
            
            if ($val == '') // empty option
            {
                return '';
            }
            else
            {
                if (is_string($val) and strpos($val, '::'))
                {
                    $tmp = explode('::', $val);
                    return trim($tmp[0]);
                }
                else
                {
                    if ($this->is_boolean)
                    {
                        return $val == '1' ? true : false;
                    }
                    else
                    {
                        return $val;
                    }
                }
            }
        }
        else
        {
            return '';
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
     * @param $startEmpty if the combo will have an empty first item
     * @param $fire_events If change action will be fired
     */
    public static function reload($formname, $name, $items, $startEmpty = FALSE, $fire_events = TRUE)
    {
        $fire_param = $fire_events ? 'true' : 'false';
        $code = "tcombo_clear('{$formname}', '{$name}', $fire_param); ";
        if ($startEmpty)
        {
            $code .= "tcombo_add_option('{$formname}', '{$name}', '', ''); ";
        }
        
        if ($items)
        {
            foreach ($items as $key => $value)
            {
                if (substr($key, 0, 3) == '>>>')
                {
                    $code .= "tcombo_create_opt_group('{$formname}', '{$name}', '{$value}'); ";
                }
                else
                {
                    // se exitsir optgroup add nele
                    $value = addslashes($value);
                    $code .= "tcombo_add_option('{$formname}', '{$name}', '{$key}', '{$value}'); ";
                }
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
        TScript::create( " tcombo_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " tcombo_disable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Clear the field
     * @param $form_name   Form name
     * @param $field       Field name
     * @param $fire_events If change action will be fired
     */
    public static function clearField($form_name, $field, $fire_events = TRUE)
    {
        $fire_param = $fire_events ? 'true' : 'false';
        TScript::create( " tcombo_clear('{$form_name}', '{$field}', $fire_param); " );
    }
    
    /**
     * Define the combo default option value
     * @param $option option value
     */
    public function setDefaultOption($option)
    {
        $this->defaultOption = $option;
    }
    
    /**
     * Render items
     */
    public function renderItems()
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
                    $option->add(htmlspecialchars($item)); // add the item label
                    
                    if (substr($chave, 0, 3) == '###')
                    {
                        $option->{'disabled'} = '1';
                        $option->{'class'} = 'disabled';
                    }
                    
                    // verify if this option is selected
                    if (($chave == $this->value) AND !(is_null($this->value)) AND strlen((string) $this->value) > 0)
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
        $this->tag->{'name'}  = $this->name;    // tag name
        
        if ($this->id and empty($this->tag->{'id'}))
        {
            $this->tag->{'id'} = $this->id;
        }
        
        if (!empty($this->size))
        {
            if (strstr((string) $this->size, '%') !== FALSE)
            {
                $this->setProperty('style', "width:{$this->size};", false); //aggregate style info
            }
            else
            {
                $this->setProperty('style', "width:{$this->size}px;", false); //aggregate style info
            }
        }
        
        if (isset($this->changeAction))
        {
            if (!TForm::getFormByName($this->formName) instanceof TForm)
            {
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
            }
            
            $string_action = $this->changeAction->serialize(FALSE);
            $this->setProperty('changeaction', "__adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback')");
            $this->setProperty('onChange', $this->getProperty('changeaction'));
        }
        
        if (isset($this->changeFunction))
        {
            $this->setProperty('changeaction', $this->changeFunction, FALSE);
            $this->setProperty('onChange', $this->changeFunction, FALSE);
        }
        
        // verify whether the widget is editable
        if (!parent::getEditable())
        {
            // make the widget read-only
            $this->tag->{'onclick'}  = "return false;";
            $this->tag->{'style'}   .= ';pointer-events:none';
            $this->tag->{'tabindex'} = '-1';
            $this->tag->{'class'}    = 'tcombo tcombo_disabled'; // CSS
        }
        
        if ($this->searchable)
        {
            $this->tag->{'role'} = 'tcombosearch';
        }
        
        // shows the combobox
        $this->renderItems();
        $this->tag->show();
        
        if ($this->searchable)
        {
            $select = AdiantiCoreTranslator::translate('Select');
            TScript::create("tcombo_enable_search('#{$this->id}', '{$select}')");
            
            if (!parent::getEditable())
            {
                TScript::create(" tmultisearch_disable_field( '{$this->formName}', '{$this->name}', '{$this->tag->{'title'}}'); ");
            }
        }
    }
}
