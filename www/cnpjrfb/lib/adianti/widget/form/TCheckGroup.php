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
use Adianti\Widget\Form\TCheckButton;

use Exception;

/**
 * A group of CheckButton's
 *
 * @version    7.6
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TCheckGroup extends TField implements AdiantiWidgetInterface
{
    private $layout = 'vertical';
    private $changeAction;
    private $items;
    private $breakItems;
    private $buttons;
    private $labels;
    private $allItemsChecked;
    protected $separator;
    protected $changeFunction;
    protected $formName;
    protected $labelClass;
    protected $useButton;
    protected $useSwitch;
    protected $value;
    
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
        $this->useSwitch  = FALSE;
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
                $button = new TCheckButton("{$this->name}[]");
                $button->setProperty('checkgroup', $this->name);
                $button->setIndexValue($key);
                $button->setProperty('onchange', $oldbuttons[$key]->getProperty('onchange'));
                
                $obj = new TLabel($value);
                $this->buttons[$key] = $button;
                $this->labels[$key] = $obj;
            }
        }
    }
    
    /**
     * Check all options
     */
    public function checkAll()
    {
        $this->allItemsChecked = TRUE;
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
     * Show as switch
     */
    public function setUseSwitch($useSwitch = TRUE, $labelClass = 'blue')
    {
       $this->labelClass = 'tswitch ' . $labelClass . ' ';
       $this->useSwitch  = $useSwitch;
    }
    
    /**
     * Add items to the check group
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
                $button = new TCheckButton("{$this->name}[]");
                $button->setProperty('checkgroup', $this->name);
                $button->setIndexValue($key);

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
            else
            {
                $this->value = null;
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
     * Reload checkbox items after it is already shown
     * @param $formname form name (used in gtk version)
     * @param $name field name
     * @param $items array with items
     * @param $options array of options [layout, size, breakItems, useButton, valueSeparator, value, changeAction, changeFunction, checkAll]
     */
    public static function reload($formname, $name, $items, $options)
    {
        $field = new self($name);
        $field->addItems($items);

        if (! empty($options['layout']))
        {
            $field->setLayout($options['layout']);
        }

        if (! empty($options['size']))
        {
            $field->setSize($options['size']);
        }

        if (! empty($options['breakItems']))
        {
            $field->setBreakItems($options['breakItems']);
        }

        if (! empty($options['useButton']))
        {
            $field->setUseButton($options['useButton']);
        }

        if (! empty($options['valueSeparator']))
        {
            $field->setValueSeparator($options['valueSeparator']);
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

        TScript::create( " tcheckgroup_reload('{$formname}', '{$name}', `{$content}`); " );
    }

    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " tcheckgroup_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " tcheckgroup_disable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * clear the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function clearField($form_name, $field)
    {
        TScript::create( " tcheckgroup_clear_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        $editable_class = (!parent::getEditable()) ? 'tfield_block_events' : '';
        
        if ($this->useButton)
        {
            echo "<div tcheckgroup=\"{$this->name}\" class=\"toggle-wrapper {$editable_class}\" ".$this->getPropertiesAsString('aria').' data-toggle="buttons">';
            echo '<div class="btn-group" style="clear:both;float:left;display:table;">';
        }
        else
        {
            echo "<div tcheckgroup=\"{$this->name}\" class=\"toggle-wrapper {$editable_class}\" ".$this->getPropertiesAsString('aria').' role="group">';
        }
        
        if ($this->items)
        {
            // iterate the checkgroup options
            $i = 0;
            foreach ($this->items as $index => $label)
            {
                $button = $this->buttons[$index];
                $button->setName($this->name.'[]');
                $active = FALSE;
                $id = $button->getId();
                
                // verify if the checkbutton is checked
                if (!(is_null($this->value)) && (@in_array($index, $this->value)) OR $this->allItemsChecked)
                {
                    $button->setValue($index); // value=indexvalue (checked)
                    $active = TRUE;
                }
                
                // create the label for the button
                $obj = $this->labels[$index];
                $obj->{'class'} = $this->labelClass . ($active?'active':'');
                $obj->setTip($this->tag->title);
                
                if ($this->getSize() AND !$obj->getSize())
                {
                    $obj->setSize($this->getSize());
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
                    $classButton = 'filled-in';

                    if ($this->useSwitch)
                    {
                        $classButton .= ' btn-tswitch';
                    }

                    $button->setProperty('class', $classButton);
                    
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
                       echo '<div class="btn-group" style="clear:both;float:left;display:table;">';
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
