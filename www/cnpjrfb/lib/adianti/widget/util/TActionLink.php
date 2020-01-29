<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Base\TElement;
use Adianti\Control\TAction;

/**
 * Action Link
 *
 * @version    7.1
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TActionLink extends TTextDisplay
{
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
        if ($icon)
        {
            $value = new TImage($icon) . $value;
        }
        
        parent::__construct($value, $color, $size, $decoration);
        parent::setName('button');
        
        $this->{'href'} = $action->serialize();
        $this->{'generator'} = 'adianti';
    }
    
    /**
     * Add CSS class
     */
    public function addStyleClass($class)
    {
        $this->{'class'} .= " {$class}";
    }
}
