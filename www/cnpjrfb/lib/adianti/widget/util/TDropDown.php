<?php
namespace Adianti\Widget\Util;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TAction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Util\TImage;

/**
 * TDropDown Widget
 *
 * @version    7.3
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDropDown extends TElement
{
    protected $elements;
    private $button;
    
    /**
     * Class Constructor
     * @param $title Dropdown title
     * @param $icon  Dropdown icon
     */
    public function __construct($label, $icon = NULL, $use_caret = FALSE, $title = '', $height = null)
    {
        parent::__construct('div');
        $this->{'class'} = 'btn-group';
        $this->{'style'} = 'display:inline-block; -moz-user-select: none; -webkit-user-select:none; user-select:none;';
        
        $button = new TElement('button');
        $button->{'data-toggle'} = 'dropdown';
        $button->{'class'}       = 'btn btn-default btn-sm dropdown-toggle';
        $this->button = $button;
        
        if ($icon)
        {
            $button->add(new TImage($icon));
        }
        
        if ($title)
        {
            $button->{'title'} = $title;
        }
        $button->add($label);
        
        if ($use_caret)
        {
            $span = new TElement('span');
            $span->{'class'} = 'fa fa-chevron-down';
            $span->{'style'} = 'margin-left: 3px';
            $button->add($span);
        }
        
        parent::add($button);
        
        //$this->id = 'tdropdown_' . mt_rand(1000000000, 1999999999);
        $this->elements = new TElement('ul');
        $this->elements->{'class'} = 'dropdown-menu pull-left';
        $this->elements->{'aria-labelledby'} = 'drop2';
        $this->elements->{'widget'} = 'tdropdown';
        
        if (!empty($height))
        {
            $this->elements->{'style'} = "height:{$height}px;overflow:auto";
        }
        parent::add($this->elements);
    }
    
    /**
     * Define the pull side
     * @side left/right
     */
    public function setPullSide($side)
    {
        $this->elements->{'class'} = "dropdown-menu pull-{$side} dropdown-menu-{$side}";
    }

    /**
     * Define the button size
     * @size sm (small) lg (large)
     */
    public function setButtonSize($size)
    {
        $this->button->{'class'} = "btn btn-default btn-{$size} dropdown-toggle";
    }
    
    /**
     * Define the button class
     * @class CSS class
     */
    public function setButtonClass($class)
    {
        $this->button->{'class'} = $class;
    }
    
    /**
     * Returns the dropdown button
     */
    public function getButton()
    {
        return $this->button;
    }
    
    /**
     * Add an action
     * @param $title  Title
     * @param $action Action (TAction or string Javascript action)
     * @param $icon   Icon
     */
    public function addAction($title, $action, $icon = NULL, $popover = '', $add = true)
    {
        $li = new TElement('li');
        // $li->{'class'} = "dropdown-item";
        $link = new TElement('a');
        
        if ($action instanceof TAction)
        { 
            $link->{'onclick'} = "__adianti_load_page('{$action->serialize()}');";
        }
        else if (is_string($action))
        {
            $link->{'onclick'} = $action;
        }
        $link->{'style'} = 'cursor: pointer';
        
        if ($popover)
        {
            $link->{'title'} = $popover;
        }
        
        if ($icon)
        {
            $image = is_object($icon) ? clone $icon : new TImage($icon);
            $image->{'style'} .= ';padding: 4px';
            $link->add($image);
        }
        
        $span = new TElement('span');
        $span->add($title);
        $link->add($span);
        $li->add($link);
        
        if ($add)
        {
            $this->elements->add($li);
        }
        return $li;
    }
    
    /**
     * Add an action
     * @param $title  Title
     * @param $action Action (TAction or string Javascript action)
     * @param $icon   Icon
     */
    public function addPostAction($title, $action, $form, $icon = NULL, $popover = '', $add = true)
    {
        $li = new TElement('li');
        
        $link = new TElement('a');
        
        if ($action instanceof TAction)
        { 
            $url = $action->serialize(FALSE);
            
            if ($action->isStatic())
            {
                $url .= '&static=1';
            }
            $url = htmlspecialchars($url);
            $wait_message = AdiantiCoreTranslator::translate('Loading');
            
            // define the button's action (ajax post)
            $action = "Adianti.waitMessage = '$wait_message';";
            $action.= "{$this->functions}";
            $action.= "__adianti_post_data('{$form}', '{$url}');";
            $action.= "return false;";
            
            $link->{'onclick'} = $action;
        }
        else if (is_string($action))
        {
            $link->{'onclick'} = $action;
        }
        $link->{'style'} = 'cursor: pointer';
        
        if ($popover)
        {
            $link->{'title'} = $popover;
        }
        
        if ($icon)
        {
            $image = is_object($icon) ? clone $icon : new TImage($icon);
            $image->{'style'} .= ';padding: 4px';
            $link->add($image);
        }
        
        $span = new TElement('span');
        $span->add($title);
        $link->add($span);
        $li->add($link);
        
        if ($add)
        {
            $this->elements->add($li);
        }
        return $li;
    }
    
    /**
     * Add an action group
     */
    public function addActionGroup($title, $actions, $icon)
    {
        $li = new TElement('li');
        $li->{'class'} = "dropdown-submenu";
        $link = new TElement('a');
        $span = new TElement('span');
        
        if ($icon)
        {
            $image = is_object($icon) ? clone $icon : new TImage($icon);
            $image->{'style'} .= ';padding: 4px';
            $link->add($image);
        }
        
        $span->add($title);
        $link->add($span);
        $li->add($link);
        
        $ul = new TElement('ul');
        $ul->{'class'} = "dropdown-menu";
        $li->add($ul);
        if ($actions)
        {
            foreach ($actions as $action)
            {
                $ul->add( $this->addAction( $action[0], $action[1], $action[2], '', false ) );
            }
        }
        
        $this->elements->add($li);
    }
    
    /**
     * Add a header
     * @param $header Options Header
     */
    public function addHeader($header)
    {
        $li = new TElement('li');
        $li->{'role'} = 'presentation';
        $li->{'class'} = 'dropdown-header';
        $li->add($header);
        $this->elements->add($li);
    }
    
    /**
     * Add a separator
     */
    public function addSeparator()
    {
        $li = new TElement('li');
        $li->{'class'} = 'divider';
        $this->elements->add($li);
    }
    
    /**
     * Clear child elements
     */
    public function clearItems()
    {
        $this->elements->clearChildren();
    }
}
