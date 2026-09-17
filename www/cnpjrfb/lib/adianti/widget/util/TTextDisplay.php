<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;

/**
 * Text Display
 *
 * @version    8.6
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TTextDisplay extends TElement
{
    private $value;
    private $inlineStyle;
    private $transformer;
    private $toggleVisibility;
    private $size = null;
    
    /**
     * Class Constructor
     * @param  $value text content
     * @param  $color text color
     * @param  $fontSize  text size
     * @param  $decoration text decorations (b=bold, i=italic, u=underline)
     */
    public function __construct($value, $color = null, $fontSize = null, $decoration = null)
    {
        parent::__construct('span');
        $this->{'class'} = 'ttd';

        $this->toggleVisibility = FALSE;
        
        $this->inlineStyle = array();
        
        if (!empty($color))
        {
            $this->inlineStyle['color'] = $color;
        }
        
        if (!empty($fontSize))
        {
            $this->inlineStyle['font-size'] = (is_numeric($fontSize) ? $fontSize.'pt' : $fontSize);
        }
        
        if (!empty($decoration))
        {
            if (strpos(strtolower($decoration), 'b') !== FALSE)
            {
                $this->inlineStyle['font-weight'] = 'bold';
            }
            
            if (strpos(strtolower($decoration), 'i') !== FALSE)
            {
                $this->inlineStyle['font-style'] = 'italic';
            }
            
            if (strpos(strtolower($decoration), 'u') !== FALSE)
            {
                $this->inlineStyle['text-decoration'] = 'underline';
            }
        }
        
        $this->value = $value;
    }
    
    /**
     * Replace current label
     */
    public function setLabel($label)
    {
        $this->value = $label;
    }
    
    /**
     * Define the Text's width
     * @param $width Field's width in pixels
     */
    public function setSize($width)
    {
        $this->size = $width;
        $this->inlineStyle['width'] = $width;
    }
    
    /**
     * Returns the field size
     */
    public function getSize()
    {
        return $this->size;
    }
    
    /**
     * Define the text align
     * @param $align Text Align
     */
    public function setTextAlign($align)
    {
        $this->inlineStyle['text-align'] = $align;
    }
    
    /**
     * Change some inline style property
     * @param $property Style property
     * @param $value Style value
     */
    public function setStyleProperty($property, $value)
    {
        $this->inlineStyle[$property] = $value;
    }
    
    /**
     * Enable toggle visible
     */
    public function enableToggleVisibility($toggleVisibility = TRUE)
    {
        $this->toggleVisibility = $toggleVisibility;
    }
    
    /**
     * Define a callback function to be applyed over the column's data
     * @param $callback  A function name of a method of an object
     */
    public function setTransformer(Callable $callback)
    {
        $this->transformer = $callback;
    }
    
    /**
     *
     */
    public function show()
    {
        if ($this->transformer)
        {
            $this->value = call_user_func($this->transformer, $this->value);
        }
        
        $this->{'style'} .= substr( str_replace(['"',','], ['',';'], json_encode($this->inlineStyle) ), 1, -1);
        
        parent::clearChildren();
        parent::add($this->value);
        
        if ($this->toggleVisibility)
        {
            $icon = new TElement('i');
            $icon->{'class'} = 'fa fa-eye-slash';

            $span = new TElement('span');
            $span->{'class'} .= ' label-toggle-visibilty ';

            $spanValue = new TElement('span');
            $spanValue->add(parent::getChildren()[0]);
            $spanValue->{'class'} = $this->class;
            $spanValue->{'style'} = $this->style . ';filter: blur(5px);';

            $span->add($spanValue);
            $span->add($icon);
            $span->id   = 'ttextdisplay_' . mt_rand(1000000000, 1999999999);

            $span->show();

            TScript::create(" tlabel_toggle_visibility( '{$span->id}' ); ");
        }
        else
        {
            parent::show();
        }
    }
}
