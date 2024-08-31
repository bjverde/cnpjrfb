<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Base\TElement;
use Adianti\Control\TAction;

/**
 * Pillbar
 *
 * @version    7.6
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TPillBar extends TElement
{
    protected $container;
    protected $items;
    protected $stepNumber = 1;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('div');
        $this->{'id'} = 'div_pills';
        $this->{'style'} = 'display:inline-block';
        
        $this->container = new TElement('ul');
        $this->container->{'class'} = 'nav nav-pills';
        
        parent::add( $this->container );
    }
    
    /**
     * Add an item
     * @param $title     Item title
     * @param $completed Item is completed
     * @param $action    Item action
     */
    public function addItem($title, $action = null)
    {
        $li = new TElement('li');
        $li->{'class'} = 'nav-item';
        $this->items[ $title ] = $li;
        $this->container->add( $li );
        
        if ($action)
        {
            $span_title = new TElement('a');
            $span_title->{'href'}      = $action->serialize(true);
            $span_title->{'generator'} = 'adianti';
        }
        else
        {
            $span_title = new TElement('span');
        }
        
        $span_title->{'class'}     = 'nav-link btn-sm';
        $span_title->add( $title );
        
        $li->add( $span_title );
        
        $this->stepNumber ++;
    }
    
    /**
     *
     */
    public function selectIndex($index)
    {
        $n = 0;
        if ($this->items)
        {
            foreach ($this->items as $key => $item)
            {
               unset($item->{'class'});
               if ($n === $index)
               {
                   $item->{'class'} = 'active';
               }
               $n ++; 
            }
        }
    }
    
    /**
     * Select current item
     */
    public function select($title)
    {
        if ($this->items)
        {
            foreach ($this->items as $key => $item)
            {
                if ($key == $title)
                {
                    $item->{'class'} .= ' active';
                    $class = '';
                }
            }
        }
    }
}
