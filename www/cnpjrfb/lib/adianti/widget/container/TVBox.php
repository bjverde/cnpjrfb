<?php
namespace Adianti\Widget\Container;

use Adianti\Widget\Base\TElement;

/**
 * Vertical Box
 *
 * @version    7.4
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TVBox extends TElement
{
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct('div');
        $this->{'style'} = 'display: inline-block';
    }
    
    /**
     * Add an child element
     * @param $child Any object that implements the show() method
     */
    public function add($child)
    {
        $wrapper = new TElement('div');
        $wrapper->{'style'} = 'clear:both';
        $wrapper->add($child);
        parent::add($wrapper);
        return $wrapper;
    }
    
    /**
     * Add a new col with many cells
     * @param $cells Each argument is a row cell
     */
    public function addColSet()
    {
        $args = func_get_args();
        if ($args)
        {
            foreach ($args as $arg)
            {
                $this->add($arg);
            }
        }
    }
    
    /**
     * Static method for pack content
     * @param $cells Each argument is a cell
     */
    public static function pack()
    {
        $box = new self;
        $args = func_get_args();
        if ($args)
        {
            foreach ($args as $arg)
            {
                $box->add($arg);
            }
        }
        return $box;
    }
}
