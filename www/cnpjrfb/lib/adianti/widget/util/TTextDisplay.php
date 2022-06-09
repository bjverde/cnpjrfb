<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Base\TElement;

/**
 * Text Display
 *
 * @version    7.4
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TTextDisplay extends TElement
{
    /**
     * Class Constructor
     * @param  $value text content
     * @param  $color text color
     * @param  $size  text size
     * @param  $decoration text decorations (b=bold, i=italic, u=underline)
     */
    public function __construct($value, $color = null, $size = null, $decoration = null)
    {
        parent::__construct('span');
        $this->{'class'} = 'ttd';
        
        $style = array();
        
        if (!empty($color))
        {
            $style['color'] = $color;
        }
        
        if (!empty($size))
        {
            $style['font-size'] = (strpos($size, 'px') or strpos($size, 'pt')) ? $size : $size.'pt';
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
}
