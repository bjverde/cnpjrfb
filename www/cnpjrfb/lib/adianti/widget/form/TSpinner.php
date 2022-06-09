<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Control\TAction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TField;

use Adianti\Core\AdiantiCoreTranslator;
use Exception;

/**
 * Spinner Widget (also known as spin button)
 *
 * @version    7.4
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSpinner extends TField implements AdiantiWidgetInterface
{
    private $min;
    private $max;
    private $step;
    private $exitAction;
    private $exitFunction;
    protected $id;
    protected $formName;
    protected $value;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->id = 'tspinner_'.mt_rand(1000000000, 1999999999);
        $this->tag->{'widget'} = 'tspinner';
    }
    
    /**
     * Define the field's range
     * @param $min Minimal value
     * @param $max Maximal value
     * @param $step Step value
     */
    public function setRange($min, $max, $step)
    {
        $this->min = $min;
        $this->max = $max;
        $this->step = $step;
        
        if ($step == 0)
        {
            throw new Exception(AdiantiCoreTranslator::translate('Invalid parameter (^1) in ^2', $step, 'setRange'));
        }
        
        if (is_int($step) AND $this->getValue() % $step !== 0)
        {
            parent::setValue($min);
        }
    }
    
    /**
     * Define the action to be executed when the user leaves the form field
     * @param $action TAction object
     */
    function setExitAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->exitAction = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
    }
    
    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " tspinner_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " tspinner_disable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Set exit function
     */
    public function setExitFunction($function)
    {
        $this->exitFunction = $function;
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        // define the tag properties
        $this->tag->{'name'}  = $this->name;    // TAG name
        $this->tag->{'value'} = $this->value;   // TAG value
        $this->tag->{'type'}  = 'text';         // input type
        $this->tag->{'data-min'} = $this->min;
        $this->tag->{'data-max'} = $this->max;
        $this->tag->{'data-step'} = $this->step;
        
        if ($this->step > 0 and $this->step < 1)
        {
            $this->tag->{'data-rule'} = 'currency';
        }
        
        $this->setProperty('style', "text-align:right", false); //aggregate style info
        
        if (strstr((string) $this->size, '%') !== FALSE)
        {
            $this->setProperty('style', "width:{$this->size};", false); //aggregate style info
            $this->setProperty('relwidth', "{$this->size}", false); //aggregate style info
        }
        else
        {
            $this->setProperty('style', "width:{$this->size}px;", false); //aggregate style info
        }
        
        if ($this->id)
        {
            $this->tag->{'id'}  = $this->id;
        }
        
        if (isset($this->exitAction))
        {
            if (!TForm::getFormByName($this->formName) instanceof TForm)
            {
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
            }
            $string_action = $this->exitAction->serialize(FALSE);
            $this->setProperty('exitaction', "__adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback')");
        }
        
        $exit_action = "function() {}";
        if (isset($this->exitFunction))
        {
            $exit_action = "function() { {$this->exitFunction} }";
        }
        
        if (!parent::getEditable())
        {
            $this->tag->{'tabindex'} = '-1';
        }
        $this->tag->show();
        TScript::create(" tspinner_start( '#{$this->id}', $exit_action); ");
        
        // verify if the widget is non-editable
        if (!parent::getEditable())
        {
            self::disableField($this->formName, $this->name);
        }
    }
    
    /**
     * Set the value
     */
    public function setValue($value)
    {
        parent::setValue( (float) $value);
    }
}
