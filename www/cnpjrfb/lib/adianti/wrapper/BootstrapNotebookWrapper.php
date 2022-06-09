<?php
namespace Adianti\Wrapper;
use Adianti\Widget\Container\TNotebook;
use Adianti\Widget\Base\TElement;

/**
 * Bootstrap datagrid decorator for Adianti Framework
 *
 * @version    7.4
 * @package    wrapper
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 * @wrapper    TNotebook
 */
class BootstrapNotebookWrapper
{
    private $decorated;
    private $properties;
    private $direction;
    private $divisions;
    
    /**
     * Constructor method
     */
    public function __construct(TNotebook $notebook)
    {
        $this->decorated = $notebook;
        $this->properties = array();
        $this->direction = '';
        $this->divisions = array(2,10);
    }
    
    /**
     * Redirect calls to decorated object
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->decorated, $method),$parameters);
    }
    
    /**
     * Redirect assigns to decorated object
     */
    public function __set($property, $value)
    {
        $this->properties[$property] = $value;
    }
    
    /**
     * Set tabs direction
     * @param $direction Tabs direction (left right)
     */
    public function setTabsDirection($direction, $divisions = null)
    {
        if ($direction)
        {
            $this->direction = 'tabs-'.$direction;
            if ($divisions)
            {
                $this->divisions = $divisions;
            }
        } 
    }
    
    /**
     * Shows the decorated datagrid
     */
    public function show()
    {
        $rendered = $this->decorated->render();
        $rendered->{'role'} = 'tabpanel';
        unset($rendered->{'class'});
        $rendered->{'class'} = 'tabwrapper';
        
        foreach ($this->properties as $property => $value)
        {
            $rendered->$property = $value;
        }
        
        $sessions = $rendered->getChildren();
        if ($sessions)
        {
            foreach ($sessions as $section)
            {
                if ($section->{'class'} == 'nav nav-tabs')
                {
                    $section->{'class'} = "nav nav-tabs " . $this->direction;
                    if ($this->direction)
                    {
                        $section->{'class'} .= " flex-column";

                    }
                    $section->{'role'}  = "tablist";
                    $tabs = $section;
                }
                if ($section->{'class'} == 'spacer')
                {
                    $section->{'style'} = "display:none";
                }
                if ($section->{'class'}  == 'frame tab-content')
                {
                    $section->{'class'} = 'tab-content';
                    $panel = $section;
                }
            }
        }
        
        if ($this->direction == 'tabs-left')
        {
            $rendered->clearChildren();
            $left_pack = TElement::tag('div', '', array('class'=> 'left-pack col-'.$this->divisions[0], 'style' => 'padding:0'));
            $right_pack = TElement::tag('div', '', array('class'=> 'right-pack col-'.$this->divisions[1], 'style' => 'padding-right:0; margin-right:0'));
            $rendered->add($left_pack);
            $rendered->add($right_pack);
            $left_pack->add($tabs);
            $right_pack->add($panel);
        }
        else if ($this->direction == 'tabs-right')
        {
            $rendered->clearChildren();
            $left_pack = TElement::tag('div', '', array('class'=> 'left-pack col-'.$this->divisions[1]));
            $right_pack = TElement::tag('div', '', array('class'=> 'right-pack col-'.$this->divisions[0]));
            $rendered->add($left_pack);
            $rendered->add($right_pack);
            $left_pack->add($panel);
            $right_pack->add($tabs);
        }
        
        if (!empty($this->direction))
        {
            $rendered->{'style'} .= ';display: flex';
        }

        $rendered->show();
    }
}
