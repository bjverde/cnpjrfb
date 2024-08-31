<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;

/**
 * Text Display
 *
 * @version    7.6
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TTextDisplay extends TElement
{
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
        
        $style = array();
        
        if (!empty($color))
        {
            $style['color'] = $color;
        }
        
        if (!empty($fontSize))
        {
            $style['font-size'] = (strpos($fontSize, 'px') or strpos($fontSize, 'pt')) ? $fontSize : $fontSize.'pt';
        }
        
        if (!empty($decoration))
        {
            if (strpos(strtolower($decoration), 'b') !== FALSE)
            {
                $style['font-weight'] = 'bold';
            }
            
            if (strpos(strtolower($decoration), 'i') !== FALSE)
            {
                $style['font-style'] = 'italic';
            }
            
            if (strpos(strtolower($decoration), 'u') !== FALSE)
            {
                $style['text-decoration'] = 'underline';
            }
        }
        
        parent::add($value);

        $this->{'style'} = substr( str_replace(['"',','], ['',';'], json_encode($style) ), 1, -1);
    }
    
    /**
     * Replace current label
     */
    public function setLabel($label)
    {
        parent::clearChildren();
        parent::add($label);
    }
    
    /**
     * Define the Text's width
     * @param $width Field's width in pixels
     */
    public function setSize($width)
    {
        $this->size = $width;
    }
    
    /**
     * Returns the field size
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Enable toggle visible
     */
    public function enableToggleVisibility($toggleVisibility = TRUE)
    {
        $this->toggleVisibility = $toggleVisibility;
    }

    public function show()
    {
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
