<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Base\TElement;
use Adianti\Control\TAction;

/**
 * Action Link
 *
 * @version    7.6
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TActionLink extends TTextDisplay
{
    private $action;
    private $icon;

    /**
     * Class Constructor
     * @param  $value  text content
     * @param  $action TAction Object
     * @param  $color  text color
     * @param  $size   text size
     * @param  $decoration text decorations (b=bold, i=italic, u=underline)
     */
    public function __construct($value, TAction $action, $color = null, $size = null, $decoration = null, $icon = null)
    {
        $this->icon = $icon;
        
        if ($icon)
        {
            $value = new TImage($icon) . $value;
        }
        
        parent::__construct($value, $color, $size, $decoration);
        parent::setName('a');
        
        $this->action = $action;

        $this->{'href'} = $action->serialize(TRUE, TRUE);
        $this->{'generator'} = 'adianti';
        
        if ($this->{'href'} == '#disabled')
        {
            $this->{'disabled'} = '1';
        }
    }

    /**
     * Returns the current calback
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Add a JavaScript function to be executed by the button
     * @param $function A piece of JavaScript code
     * @ignore-autocomplete on
     */
    public function addFunction($function)
    {
        if ($function)
        {
            $this->{'onclick'} = $function.';';
        }
    }
    
    /**
     * Replace current label
     */
    public function setLabel($label)
    {
        if ($this->icon)
        {
            $label = new TImage($this->icon) . $label;
        }
        
        parent::setLabel($label);
    }
    
    /**
     * Add CSS class
     */
    public function addStyleClass($class)
    {
        $this->{'class'} .= " {$class}";
    }
}
