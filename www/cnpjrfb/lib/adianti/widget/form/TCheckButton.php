<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\TLabel;

/**
 * CheckButton widget
 *
 * @version    7.6
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TCheckButton extends TField implements AdiantiWidgetInterface
{
    private $indexValue;
    private $useSwitch;
    private $labelClass;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->id = 'tcheckbutton_' . mt_rand(1000000000, 1999999999);
        $this->tag->{'class'} = '';
        $this->useSwitch  = FALSE;
    }
    
    /**
     * Show as switch
     */
    public function setUseSwitch($useSwitch = TRUE, $labelClass = 'blue')
    {
       $this->labelClass = 'tswitch ' . $labelClass;
       $this->useSwitch  = $useSwitch;
    }

    /**
     * Define the index value for check button
     * @index Index value
     */
    public function setIndexValue($index)
    {        
        $this->indexValue = $index;
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        // define the tag properties for the checkbutton
        $this->tag->{'name'}  = $this->name;    // tag name
        $this->tag->{'type'}  = 'checkbox';     // input type
        $this->tag->{'value'} = $this->indexValue;   // value
        
        if ($this->id and empty($this->tag->{'id'}))
        {
            $this->tag->{'id'} = $this->id;
        }
        
        // compare current value with indexValue
        if ($this->indexValue == $this->value AND !(is_null($this->value)) AND strlen((string) $this->value) > 0)
        {
            $this->tag->{'checked'} = '1';
        }
        
        // check whether the widget is non-editable
        if (!parent::getEditable())
        {
            // make the widget read-only
            //$this->tag-> disabled   = "1"; // the value don't post
            $this->tag->{'onclick'} = "return false;";
            $this->tag->{'style'}   = 'pointer-events:none';
            $this->tag->{'tabindex'} = '-1';
        }
        
        if ($this->useSwitch)
        {
            $obj = new TLabel('');
            $obj->{'class'} = 'tswitch ' . $this->labelClass;
            $obj->{'for'} = $this->id;

            $this->tag->{'class'} = 'filled-in btn-tswitch';

            $wrapper = new TElement('div');
            $wrapper->{'style'} = 'display:inline-flex;align-items:center;';
            $wrapper->add($this->tag);
            $wrapper->add($obj);
            $wrapper->show();
        }
        else
        {
            // shows the tag
            $this->tag->show();
        }

    }
}
