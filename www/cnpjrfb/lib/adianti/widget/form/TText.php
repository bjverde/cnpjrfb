<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Control\TAction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TField;

use Adianti\Core\AdiantiCoreTranslator;
use Exception;

/**
 * Text Widget (also known as Memo)
 *
 * @version    7.1
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TText extends TField implements AdiantiWidgetInterface
{
    private   $exitAction;
    private   $exitFunction;
    protected $id;
    protected $formName;
    protected $size;
    protected $height;
    
    /**
     * Class Constructor
     * @param $name Widet's name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->id   = 'ttext_' . mt_rand(1000000000, 1999999999);
        
        // creates a <textarea> tag
        $this->tag = new TElement('textarea');
        $this->tag->{'class'} = 'tfield';       // CSS
        $this->tag->{'widget'} = 'ttext';
        // defines the text default height
        $this->height= 100;
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
     * Set exit function
     */
    public function setExitFunction($function)
    {
        $this->exitFunction = $function;
    }
    
    /**
     * Return the post data
     */
    public function getPostData()
    {
        $name = str_replace(['[',']'], ['',''], $this->name);
        
        if (isset($_POST[$name]))
        {
            return $_POST[$name];
        }
        else
        {
            return '';
        }
    }
    
    /**
     * Show the widget
     */
    public function show()
    {
        $this->tag->{'name'}  = $this->name;   // tag name
        
        if ($this->size)
        {
            $size = (strstr($this->size, '%') !== FALSE) ? $this->size : "{$this->size}px";
            $this->setProperty('style', "width:{$size};", FALSE); //aggregate style info
        }
        
        if ($this->height)
        {
            $height = (strstr($this->height, '%') !== FALSE) ? $this->height : "{$this->height}px";
            $this->setProperty('style', "height:{$height}", FALSE); //aggregate style info
        }
        
        if ($this->id and empty($this->tag->{'id'}))
        {
            $this->tag->{'id'} = $this->id;
        }
        
        // check if the field is not editable
        if (!parent::getEditable())
        {
            // make the widget read-only
            $this->tag->{'readonly'} = "1";
            $this->tag->{'class'} = $this->tag->{'class'} == 'tfield' ? 'tfield_disabled' : $this->tag->{'class'} . ' tfield_disabled'; // CSS
            $this->tag->{'tabindex'} = '-1';
        }
        
        if (isset($this->exitAction))
        {
            if (!TForm::getFormByName($this->formName) instanceof TForm)
            {
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
            }
            $string_action = $this->exitAction->serialize(FALSE);
            $this->setProperty('exitaction', "__adianti_post_lookup('{$this->formName}', '{$string_action}', this, 'callback')");
            $this->setProperty('onBlur', $this->getProperty('exitaction'), FALSE);
        }
        
        if (isset($this->exitFunction))
        {
            $this->setProperty('onBlur', $this->exitFunction, FALSE );
        }
        
        // add the content to the textarea
        $this->tag->add(htmlspecialchars($this->value));
        // show the tag
        $this->tag->show();
    }
}
