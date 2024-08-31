<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Util\TImage;
use Adianti\Control\TAction;

use Exception;

/**
 * Password Widget
 *
 * @version    7.6
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TPassword extends TField implements AdiantiWidgetInterface
{
    private $exitAction;
    private $exitFunction;
    protected $formName;
    protected $innerIcon;
    protected $id;
    private $toggleVisibility;

    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->id = 'tpassword_'.mt_rand(1000000000, 1999999999);
        $this->toggleVisibility = TRUE;
    }
    
    public function enableToggleVisibility($toggleVisibility = TRUE)
    {
        $this->toggleVisibility = $toggleVisibility;
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
     * Define max length
     * @param  $length Max length
     */
    public function setMaxLength($length)
    {
        if ($length > 0)
        {
            $this->tag->{'maxlength'} = $length;
        }
    }
    
    /**
     * Define the Inner icon
     */
    public function setInnerIcon(TImage $image, $side = 'right')
    {
        $this->innerIcon = $image;
        $this->innerIcon->{'class'} .= ' input-inner-icon ' . $side;
        
        if ($side == 'left')
        {
            $this->setProperty('style', "padding-left:23px", false); //aggregate style info
        }
    }
    
    /**
     * Define the javascript function to be executed when the user leaves the form field
     * @param $function Javascript function
     */
    public function setExitFunction($function)
    {
        $this->exitFunction = $function;
    }
    
    /**
     * Disable auto complete
     */
    public function disableAutoComplete()
    {
        $this->tag->{'autocomplete'} = 'new-password';
        
    }
    
    /**
     * Show the widget at the screen
     */
    public function show()
    {
        // define the tag properties
        $this->tag-> name  =  $this->name;   // tag name
        $this->tag-> value =  $this->value;  // tag value
        $this->tag-> type  =  'password';    // input type
        
        if (!empty($this->size))
        {
            if (strstr((string) $this->size, '%') !== FALSE)
            {
                $this->setProperty('style', "width:{$this->size};", FALSE); //aggregate style info
            }
            else
            {
                $this->setProperty('style', "width:{$this->size}px;", FALSE); //aggregate style info
            }
        }
        
        // verify if the field is not editable
        if (parent::getEditable())
        {
            if (isset($this->exitAction))
            {
                if (!TForm::getFormByName($this->formName) instanceof TForm)
                {
                    throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
                }
                
                $string_action = $this->exitAction->serialize(FALSE);
                $this->setProperty('onBlur', "__adianti_post_lookup('{$this->formName}', '{$string_action}', this, 'callback')");
            }
            
            if (isset($this->exitFunction))
            {
                $this->setProperty('onBlur', $this->exitFunction, FALSE );
            }
        }
        else
        {
            // make the field read-only
            $this->tag-> readonly = "1";
            $this->tag->{'class'} .= ' tfield_disabled'; // CSS
            $this->tag->{'tabindex'} = '-1';
        }
        
        if ($this->toggleVisibility)
        {
            $div    = new TElement('div');
            $button = new TElement('button');
            $icon   = new TElement('i');

            $div->{'id'} = $this->id;

            $icon->{'class'} = 'fa fa-eye-slash';
            $div->{'class'} = 'tpassword';

            $button->{'type'} = 'button';

            $button->add($icon);
            $div->add($this->innerIcon);
            $div->add($this->tag);
            $div->add($button);

            $div->show();

            TScript::create("tpassword_start('{$this->id}');");
        }
        else if (!empty($this->innerIcon))
        {
            $icon_wrapper = new TElement('div');
            $icon_wrapper->add($this->tag);
            $icon_wrapper->add($this->innerIcon);
            $icon_wrapper->show();
        }
        else
        {
            // shows the tag
            $this->tag->show();
        }
    }
}
