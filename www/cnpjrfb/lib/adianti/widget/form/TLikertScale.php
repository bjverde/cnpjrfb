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
 * Likert Scale
 *
 * @version    7.6
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TLikertScale extends TField implements AdiantiWidgetInterface
{
    private $changeAction;
    private $items;
    private $buttons;
    private $labels;
    protected $changeFunction;
    protected $formName;
    protected $labelClass;
    protected $fullWidth;
    
    /**
     * Class Constructor
     * @param  $name name of the field
     */
    public function __construct($name)
    {
        parent::__construct($name);
        parent::setSize(NULL);
        $this->fullWidth = false;
        $this->labelClass = 'tlikertscale_label ';
    }
    
    /**
     *
     */
    public function enableFullWidth()
    {
        $this->fullWidth = true;
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
     * @param $options array of options [value, changeAction, changeFunction]
     */
    public static function reload($formname, $name, $items, $options = [])
    {
        $field = new self($name);
        $field->addItems($items);
        
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
        
        $content = $field->getContents();

        TScript::create( " tlikertscale_reload('{$formname}', '{$name}', `{$content}`); " );
    }
    
    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " tlikertscale_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " tlikertscale_disable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * clear the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function clearField($form_name, $field)
    {
        TScript::create( " tlikertscale_clear_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Show the widget at the screen
     */
    public function show()
    {
        if ($this->fullWidth)
        {
            echo "<ul class='likert likert-wrapper full' tlikertscale=\"{$this->name}\">";
        }
        else
        {
            echo "<ul class='likert likert-wrapper' tlikertscale=\"{$this->name}\">";
        }
        
        if ($this->items)
        {
            // iterate the items
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
                }
                
                $obj->{'for'} = $button->getId();
                
                $li = new TElement('li');
                $li->add($button);
                $li->add($obj);
                $li->show();
                
                $i ++;
                
                echo "\n";
            }
        }
        
        echo "</ul>";
        
        if (!empty($this->getAfterElement()))
        {
            $this->getAfterElement()->show();
        }
    }
}
