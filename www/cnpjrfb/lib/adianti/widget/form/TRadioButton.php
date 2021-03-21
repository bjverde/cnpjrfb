<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Form\TField;

/**
 * RadioButton Widget
 *
 * @version    7.3
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TRadioButton extends TField implements AdiantiWidgetInterface
{
    private $checked;
   
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->id = 'tradiobutton_' . mt_rand(1000000000, 1999999999);
        $this->tag->{'class'} = '';
    }
    
    /**
     * Show the widget at the screen
     */
    public function show()
    {
        // define the tag properties
        $this->tag->{'name'}  = $this->name;
        $this->tag->{'value'} = $this->value;
        $this->tag->{'type'}  = 'radio';
        
        if ($this->id and empty($this->tag->{'id'}))
        {
            $this->tag->{'id'} = $this->id;
        }
        
        // verify if the field is not editable
        if (!parent::getEditable())
        {
            // make the widget read-only
            //$this->tag-> disabled   = "1"; // the value don't post
            $this->tag->{'onclick'} = "return false;";
            $this->tag->{'style'}   = 'pointer-events:none';
            $this->tag->{'tabindex'} = '-1';
        }
        // show the tag
        $this->tag->show();
    }
}
