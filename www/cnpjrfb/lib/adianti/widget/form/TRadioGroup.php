<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TAction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\TRadioButton;

use Exception;

/**
 * A group of RadioButton's
 *
 * @version    7.4
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TRadioGroup extends TField implements AdiantiWidgetInterface
{
    private $layout = 'vertical';
    private $changeAction;
    private $items;
    private $breakItems;
    private $buttons;
    private $labels;
    private $appearance;
    protected $changeFunction;
    protected $formName;
    protected $labelClass;
    protected $useButton;
    protected $is_boolean;
    
    /**
     * Class Constructor
     * @param  $name name of the field
     */
    public function __construct($name)
    {
        parent::__construct($name);
        parent::setSize(NULL);
        $this->labelClass = 'tcheckgroup_label ';
        $this->useButton  = FALSE;
        $this->is_boolean = FALSE;
    }
    
    /**
     * Clone object
     */
    public function __clone()
    {
        if (is_array($this->items))
        {
            $oldbuttons = $this->buttons;
            $this->buttons = array();
            $this->labels  = array();

            foreach ($this->items as $key => $value)
            {
                $button = new TRadioButton($this->name);
                $button->setValue($key);
                $button->setProperty('onchange', $oldbuttons[$key]->getProperty('onchange'));
                
                $obj = new TLabel($value);
                $this->buttons[$key] = $button;
                $this->labels[$key] = $obj;
            }
        }
    }
    
    /**
     * Enable/disable boolean mode
     */
    public function setBooleanMode()
    {
        $this->is_boolean = true;
        $this->addItems( [ '1' => AdiantiCoreTranslator::translate('Yes'),
                           '2' => AdiantiCoreTranslator::translate('No') ] );
        $this->setLayout('horizontal');
        $this->setUseButton();
        
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
     * Return the post data
     */
    public function getPostData()
    {
        if ($this->is_boolean)
        {
            $data = parent::getPostData();
            return $data == '1' ? true : false;
        }
        else
        {
            return parent::getPostData();
        }
    }
    
    /**
     * Define the direction of the options
     * @param $direction String (vertical, horizontal)
     */
    public function setLayout($dir)
    {
        $this->layout = $dir;
    }
    
    /**
     * Get the direction (vertical or horizontal)
     */
    public function getLayout()
    {
        return $this->layout;
    }
    
    /**
     * Define after how much items, it will break
     */
    public function setBreakItems($breakItems)
    {
        $this->breakItems = $breakItems;
    }
    
    /**
     * Show as button
     */
    public function setUseButton()
    {
       $this->labelClass = 'btn btn-default ';
       $this->useButton  = TRUE;
    }
    
    /**
     * Add items to the radio group
     * @param $items An indexed array containing the options
     */
    public function addItems($items)
    {
        if (is_array($items))
        {
            $this->items = $items;
            $this->buttons = array();
            $this->labels  = array();

            foreach ($items as $key => $value)
            {
                $button = new TRadioButton($this->name);
                $button->setValue($key);

                $obj = new TLabel($value);
                $this->buttons[$key] = $button;
                $this->labels[$key] = $obj;
            }
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
     * Return the option buttons
     */
    public function getButtons()
    {
        return $this->buttons;
    }

    /**
     * Return the option labels
     */
    public function getLabels()
    {
        return $this->labels;
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
     * Reload radio items after it is already shown
     * @param $formname form name (used in gtk version)
     * @param $name field name
     * @param $items array with items
     * @param $options array of options [layout, breakItems, size, useButton, value, changeAction, changeFunction, checkAll]
     */
    public static function reload($formname, $name, $items, $options)
    {
        $field = new self($name);
        $field->addItems($items);

        if (! empty($options['layout']))
        {
            $field->setLayout($options['layout']);
        }

        if (! empty($options['breakItems']))
        {
            $field->setBreakItems($options['breakItems']);
        }

        if (! empty($options['size']))
        {
            $field->setSize($options['size']);
        }

        if (! empty($options['useButton']))
        {
            $field->setUseButton($options['useButton']);
        }

        if (! empty($options['value']))
        {
            $field->setValue($options['value']);
        }

        if (! empty($options['changeAction']))
        {
            $field->setChangeAction($options['changeAction']);
        }

        if (! empty($options['changeFunction']))
        {
            $field->setChangeFunction($options['changeFunction']);
        }

        if (! empty($options['checkAll']))
        {
            $field->checkAll($options['checkAll']);
        }

        $content = $field->getContents();

        TScript::create( " tradiogroup_reload('{$formname}', '{$name}', `{$content}`); " );
    }

    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " tradiogroup_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " tradiogroup_disable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * clear the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function clearField($form_name, $field)
    {
        TScript::create( " tradiogroup_clear_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Show the widget at the screen
     */
    public function show()
    {
        $editable_class = (!parent::getEditable()) ? 'tfield_block_events' : '';
        
        if ($this->useButton)
        {
            echo "<div class=\"toggle-wrapper {$editable_class}\" ".$this->getPropertiesAsString('aria').' data-toggle="buttons">';
            
            if (strpos( (string) $this->getSize(), '%') !== FALSE)
            {
                echo '<div class="btn-group" style="clear:both;float:left;width:100%;display:table" role="group">';
            }
            else
            {
                echo '<div class="btn-group" style="clear:both;float:left;display:table" role="group">';
            }
        }
        else
        {
            echo "<div class=\"toggle-wrapper {$editable_class}\" ".$this->getPropertiesAsString('aria').' role="group">';
        }
        
        if ($this->items)
        {
            // iterate the RadioButton options
            $i = 0;
            foreach ($this->items as $index => $label)
            {
                $button = $this->buttons[$index];
                $button->setName($this->name);
                $active = FALSE;
                $id = $button->getId();
                
                // check if contains any value
                if ( $this->value == $index AND !(is_null($this->value)) AND strlen((string) $this->value) > 0)
                {
                    // mark as checked
                    $button->setProperty('checked', '1');
                    $active = TRUE;
                }
                
                // create the label for the button
                $obj = $this->labels[$index];
                $obj->{'class'} = $this->labelClass. ($active?'active':'');
                
                if ($this->getSize() AND !$obj->getSize())
                {
                    $obj->setSize($this->getSize());
                }
                
                if ($this->getSize() AND $this->useButton)
                {
                    if (strpos($this->getSize(), '%') !== FALSE)
                    {
                        $size = str_replace('%', '', $this->getSize());
                        $obj->setSize( ($size / count($this->items)) . '%');
                    }
                    else
                    {
                        $obj->setSize($this->getSize());
                    }
                }
                
                // check whether the widget is non-editable
                if (parent::getEditable())
                {
                    if (isset($this->changeAction))
                    {
                        if (!TForm::getFormByName($this->formName) instanceof TForm)
                        {
                            throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
                        }
                        $string_action = $this->changeAction->serialize(FALSE);
                        
                        $button->setProperty('changeaction', "__adianti_post_lookup('{$this->formName}', '{$string_action}', '{$id}', 'callback')");
                        $button->setProperty('onChange', $button->getProperty('changeaction'), FALSE);
                    }
                    
                    if (isset($this->changeFunction))
                    {
                        $button->setProperty('changeaction', $this->changeFunction, FALSE);
                        $button->setProperty('onChange', $this->changeFunction, FALSE);
                    }
                }
                else
                {
                    $button->setEditable(FALSE);
                    //$obj->setFontColor('gray');
                }
                
                if ($this->useButton)
                {
                    $obj->add($button);
                    $obj->show();
                }
                else
                {
                    $button->setProperty('class', 'filled-in');
                    $obj->{'for'} = $button->getId();
                    
                    $wrapper = new TElement('div');
                    $wrapper->{'style'} = 'display:inline-flex;align-items:center;';
                    $wrapper->add($button);
                    $wrapper->add($obj);
                    $wrapper->show();
                }
                
                $i ++;
                
                if ($this->layout == 'vertical' OR ($this->breakItems == $i))
                {
                    $i = 0;
                    if ($this->useButton)
                    {
                       echo '</div>';
                       echo '<div class="btn-group" style="clear:both;float:left;display:table">';
                    }
                    else
                    {
                        // shows a line break
                        $br = new TElement('br');
                        $br->show();
                    }
                }
                echo "\n";
            }
        }
        
        if ($this->useButton)
        {
            echo '</div>';
            echo '</div>';
        }
        else
        {
            echo '</div>';
        }
        
        if (!empty($this->getAfterElement()))
        {
            $this->getAfterElement()->show();
        }
    }
}
