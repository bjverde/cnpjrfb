<?php
namespace Adianti\Widget\Container;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TStyle;
use Adianti\Widget\Util\TSourceCode;

/**
 * Scrolled Window: Allows to add another containers inside, creating scrollbars when its content is bigger than its visual area
 * 
 * @version    7.1
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TScroll extends TElement
{
    private $width;
    private $height;
    private $margin;
    private $transparency;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        $this->{'id'} = 'tscroll_' . mt_rand(1000000000, 1999999999);
        $this->margin = 2;
        $this->transparency = FALSE;
        parent::__construct('div');
    }
    
    /**
     * Set the scroll size
     * @param  $width   Panel's width
     * @param  $height  Panel's height
     */
    public function setSize($width, $height)
    {
        $this->width  = $width;
        $this->height = $height;
    }
    
    /**
     * Set the scrolling margin
     * @param  $margin Margin
     */
    public function setMargin($margin)
    {
        $this->margin = $margin;
    }
    
    /** 
     * compability reasons
     */
    public function setTransparency($bool)
    {
        $this->transparency = $bool;
    }
    
    /**
     * Shows the tag
     */
    public function show()
    {
        if (!$this->transparency)
        {
            $this->{'style'} .= ';border: 1px solid #c2c2c2';
            $this->{'style'} .= ';background: #ffffff';
        }
        $this->{'style'} .= ";padding: {$this->margin}px";
        
        if (!empty($this->width))
        {
            $this->{'style'} .= is_numeric($this->width) ? ";width:{$this->width}px" : ";width:{$this->width}";
        }
        
        if (!empty($this->height))
        {
            $this->{'style'} .= is_numeric($this->height) ? ";height:{$this->height}px" : ";height:{$this->height}";
        }
        
        $this->{'class'} .= " tscroll";
        parent::show();
    }
}
