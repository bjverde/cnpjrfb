<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;

/**
 * TreeView
 * 
 * @version    8.6
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TTreeView extends TElement
{
    private $itemIcon;
    private $itemAction;
    private $collapsed;
    private $callback;
    private $folderCallback;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct('div');
        $this->{'id'} = 'ttreeview_'.mt_rand(1000000000, 1999999999);
        $this->collapsed = FALSE;
        $this->resort = false;
    }
    
    /**
     * Set node transformer
     */
    public function setTransformer($callback)
    {
        $this->callback = $callback;
    }
    
    /**
     * Set node transformer
     */
    public function setFolderTransformer($callback)
    {
        $this->folderCallback = $callback;
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
     * Fill treeview from an multi-dimensional array
     * @param multi-dimensional array
     */
    public function fromArray($tree)
    {
        $ul = new TElement('ul');
        $ul->{'class'} = 'ttreeview';
        
        if ($tree)
        {
            foreach ($tree as $key => $item)
            {
                $ul->add( $this->buildOptions($key, $item) );
            }
        }
        parent::add($ul);
    }
    
    /**
     * Fill one level of the treeview
     * @param $options array of options
     * @ignore-autocomplete on
     */
    private function buildOptions($key, $option)
    {
        $li = new TElement('li');
        $li->{'class'} = 'menu-item';
        
        if (is_scalar($option))
        {
            $span = new TElement('span');
            $span->{'class'} = 'item-spam';
            $span->add($option);
            $li->add($span);
            
            if ($this->itemAction)
            {
                $this->itemAction->setParameter('key', $key);
                $this->itemAction->setParameter('value', $option);
                $string_action = $this->itemAction->serialize(FALSE);
                $span->{'onclick'} = "__adianti_ajax_exec('{$string_action}')";
            }
            $span->{'key'} = $key;
            
            if (is_callable($this->callback))
            {
                $span = call_user_func($this->callback, $span);
            }
        }
        else if (is_array($option))
        {
            $details = new TElement('details');
            $summary = new TElement('summary');
            $summary->add( $key );
            $details->add( $summary );
            if (!$this->collapsed)
            {
                $details->{'open'} = '1';
            }
            $li->add($details);
            
            $sub_ul = new TElement('ul');
            $sub_ul->{'class'} = 'submenu';
            $details->add($sub_ul);
            
            foreach ($option as $subkey => $suboption)
            {
                $sub_ul->add( $this->buildOptions($subkey, $suboption) );
            }
        }
        else if (is_object($option))
        {
            $li = new TElement('li');
            $li->add($option);
        }
        
        return $li;
    }
}
