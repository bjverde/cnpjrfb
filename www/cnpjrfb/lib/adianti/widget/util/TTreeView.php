<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;

/**
 * TreeView
 * 
 * @version    7.4
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TTreeView extends TElement
{
    private $itemIcon;
    private $itemAction;
    private $collapsed;
    private $callback;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct('ul');
        $this->{'id'} = 'ttreeview_'.mt_rand(1000000000, 1999999999);
        $this->collapsed = FALSE;
    }
    
    /**
     * Set node transformer
     */
    public function setTransformer($callback)
    {
        $this->callback = $callback;
    }
    
    /**
     * Set size
     * @param $size width
     */
    public function setSize($width)
    {
        $this->{'style'} = "width: {$width}px";
    }
    
    /**
     * Set item icon
     * @param $icon icon location
     */
    public function setItemIcon($icon)
    {
        $this->itemIcon = $icon;
    }
    
    /**
     * Set item action
     * @param $action icon action
     */
    public function setItemAction($action)
    {
        $this->itemAction = $action;
    } 
    
    /**
     * Collapse the Tree
     */
    public function collapse()
    {
        $this->collapsed = TRUE;
    }
    
    /**
     * Expand to Tree Node
     * @param $key Node key
     */
    public function expandTo($key)
    {
        $objectId = $this->{'id'};
        $id = md5($key);
        $script = new TElement('script');
        $script->{'type'} = 'text/javascript';
        $script->add("setTimeout(function(){ \$('#{$objectId}_{$id}').parents('ul').show()  },1);");
        $script->show();
    }
    
    /**
     * Fill treeview from an multi-dimensional array
     * @param multi-dimensional array
     */
    public function fromArray($array)
    {
        if (is_array($array))
        {
            foreach ($array as $key => $option)
            {
                if (is_scalar($option))
                {
                    $element = new TElement('li');
                    $span = new TElement('span');
                    $span->{'class'} = 'file';
                    $span->add($option);
                    if ($this->itemIcon)
                    {
                        $element->{'style'} = "background-image:url(app/images/{$this->itemIcon})";
                    }
                    
                    if ($this->itemAction)
                    {
                        $this->itemAction->setParameter('key', $key);
                        $this->itemAction->setParameter('value', $option);
                        $string_action = $this->itemAction->serialize(FALSE);
                        $element->{'onClick'} = "__adianti_ajax_exec('{$string_action}')";
                        $element->{'id'} = $this->{'id'} . '_' . md5($key);
                    }
                    $span->{'key'} = $key;
                    
                    if (is_callable($this->callback))
                    {
                        $span = call_user_func($this->callback, $span);
                    }

                    $element->add($span);
                    
                    parent::add($element);
                }
                else if (is_array($option))
                {
                    $element = new TElement('li');
                    $span = new TElement('span');
                    $span->{'class'} = 'folder';
                    $span->add($key);
                    $element->add($span);
                    $element->add($this->fromOptions($option));
                    parent::add($element);
                }
            }
        }
    }
    
    /**
     * Fill one level of the treeview
     * @param $options array of options
     * @ignore-autocomplete on
     */
    private function fromOptions($options)
    {
        if (is_array($options))
        {
            $ul = new TElement('ul');
            foreach ($options as $key => $option)
            {
                if (is_scalar($option))
                {
                    $element = new TElement('li');
                    $span = new TElement('span');
                    $span->{'class'} = 'file';
                    $span->add($option);
                    if ($this->itemIcon)
                    {
                        $element->{'style'} = "background-image:url(app/images/{$this->itemIcon})";
                    }
                    
                    if ($this->itemAction)
                    {
                        $this->itemAction->setParameter('key', $key);
                        $this->itemAction->setParameter('value', $option);
                        $string_action = $this->itemAction->serialize(FALSE);
                        $element->{'onClick'} = "__adianti_ajax_exec('{$string_action}')";
                        $element->{'id'} = $this->{'id'} . '_' . md5($key);
                    }
                    $span->{'key'} = $key;
                    
                    if (is_callable($this->callback))
                    {
                        $span = call_user_func($this->callback, $span);
                    }

                    $element->add($span);
                }
                else if (is_array($option))
                {
                    $element = new TElement('li');
                    $span = new TElement('span');
                    $span->{'class'} = 'folder';
                    $span->add($key);
                    $element->add($span);
                    $element->add($this->fromOptions($option));
                }
                else if (is_object($option))
                {
                    $element = new TElement('li');
                    $element->add($option);
                }
                $ul->add($element);
            }
            return $ul;
        }
    }
    
    /**
     * Shows the tag
     */
    public function show()
    {
        $objectId = $this->{'id'};
        $collapsed = $this->collapsed ? 'true' : 'false';
        
        parent::add(TScript::create(" ttreeview_start( '#{$objectId}', {$collapsed} ); ", FALSE));
        parent::show();
    }
}
