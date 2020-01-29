<?php
namespace Adianti\Widget\Container;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TStyle;

/**
 * Panel Container: Allows to organize the widgets using fixed (absolute) positions
 *
 * @version    7.1
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TPanel extends TElement
{
    private $style;
    private $width;
    private $height;
    
    /**
     * Class Constructor
     * @param  $width   Panel's width
     * @param  $height  Panel's height
     */
    public function __construct($width, $height)
    {
        parent::__construct('div');
		
        $this->{'id'} = 'tpanel_' . mt_rand(1000000000, 1999999999);
        
        // creates the panel style
        $this->style = new TStyle('style_'.$this->{'id'});
        $this->style-> position = 'relative';
        $this->width = $width;
        $this->height = $height;
        
        $this->{'class'} = 'style_'.$this->{'id'};
    }
    
    /**
     * Set the panel's size
     * @param $width Panel width
     * @param $height Panel height
     */
    public function setSize($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }
    
    /**
     * Returns the frame size
     * @return array(width, height)
     */
    public function getSize()
    {
        return array($this->width, $this->height);
    }
    
    /**
     * Put a widget inside the panel
     * @param  $widget = widget to be shown
     * @param  $col    = column in pixels.
     * @param  $row    = row in pixels.
     */
    public function put($widget, $col, $row)
    {
        // creates a layer to put the widget inside
        $layer = new TElement('div');
        // define the layer position
        $layer-> style = "position:absolute; left:{$col}px; top:{$row}px;";
        // add the widget to the layer
        $layer->add($widget);
        
        // add the widget to the container
        parent::add($layer);
    }
    
    /**
     * Show the widget
     */
    public function show()
    {
        $this->style-> width  = $this->width.'px';
        $this->style-> height = $this->height.'px';
        $this->style->show();
        
        parent::show();
    }
}
