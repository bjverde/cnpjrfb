<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Base\TElement;
use Adianti\Control\TAction;

/**
 * Page Step
 *
 * @version    7.6
 * @package    widget
 * @subpackage util
 * @author     Matheus Agnes Dias
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TPageStep extends TElement
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
        $this->{'id'} = 'div_steps';
        
        $this->container = new TElement('ul');
        $this->container->{'class'} = 'steps';
        
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
        
        $span_title->{'class'} = 'step-title';
        $span_title->add( $title );
        
        $span_step = new TElement('span');
        $span_step->{'class'} = 'step-number';
        $span_step->add( $this->stepNumber );
        
        $li->add( $span_step );
        $li->add( $span_title );
        
        $this->stepNumber ++;
    }
    
    /**
     * Select current item
     */
    public function select($title)
    {
        $class = 'complete';
        
        if ($this->items)
        {
            foreach ($this->items as $key => $item)
            {
                $item->{'class'} = $class;
                
                if ($key == $title)
                {
                    $item->{'class'} = 'active';
                    $class = '';
                }
            }
        }
    }
}
