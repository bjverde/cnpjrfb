<?php
namespace Adianti\Widget\Container;

use Adianti\Wrapper\BootstrapFormWrapper;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Widget\Base\TElement;
use Adianti\Control\TAction;
use Adianti\Widget\Util\TActionLink;

/**
 * Bootstrap native panel for Adianti Framework
 *
 * @version    7.1
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TPanelGroup extends TElement
{
    private $title;
    private $head;
    private $body;
    private $footer;
    private $actionsContainer;
    
    /**
     * Static creator for panels
     * @param $title Panel title
     * @param $element Panel content
     */
    public static function pack($title, $element, $footer = null)
    {
        $panel = new self($title);
        $panel->add($element);
        
        if ($footer)
        {
            $panel->addFooter($footer);
        }
        
        return $panel;
    }
    
    /**
     * Constructor method
     * @param $title  Panel Title
     * @param $footer Panel Footer
     */
    public function __construct($title = NULL, $background = NULL)
    {
        parent::__construct('div');
        $this->{'class'} = 'card panel';
        
        $this->head = new TElement('div');
        $this->head->{'class'} = 'card-header panel-heading';
        $this->head->{'style'} = 'display:none';
        parent::add($this->head);
        
        $panel_title = new TElement('div');
        $panel_title->{'class'} = 'card-title panel-title';
        $this->head->add($panel_title);
        
        $this->title = new TElement('div');
        $this->title->{'style'} = 'width: 100%';
        $this->title->add($title);
        $panel_title->add($this->title);
        
        if (!empty($background))
        {
            $this->head->{'style'} .= ';background:'.$background;
        }
        
        $this->actionsContainer = new TElement('div');
        $this->actionsContainer->{'style'} = 'margin-left: auto';
        $this->head->add( $this->actionsContainer );
        
        if (!empty($title))
        {
            $this->head->{'style'} = str_replace('display:none', '', $this->head->{'style'});
        }
        
        $this->body = new TElement('div');
        $this->body->{'class'} = 'card-body panel-body';
        parent::add($this->body);
        
        $this->footer = new TElement('div');
        $this->footer->{'class'} = 'card-footer panel-footer';
    }
    
    /**
     * Set title
     */
    public function setTitle($title)
    {
        $this->title->clearChildren();
        $this->title->add($title);
    }
    
    /**
     * Add a form header action
     * @param $label Button label
     * @param $action Button action
     * @param $icon Button icon
     */
    public function addHeaderActionLink($label, TAction $action, $icon = 'fa:save')
    {
        $this->head->{'style'} = str_replace('display:none', '', $this->head->{'style'});
        
        $this->title->{'style'} = 'display:inline-block;';
        $label_info = ($label instanceof TLabel) ? $label->getValue() : $label;
        $button = new TActionLink($label_info, $action, null, null, null, $icon);
        $button->{'class'} = 'btn btn-sm btn-default';
        
        $this->actionsContainer->add($button);
        
        return $button;
    }
    
    /**
     * Add a form header widget
     * @param $widget Widget
     */
    public function addHeaderWidget($widget)
    {
        $this->head->{'style'} = str_replace('display:none', '', $this->head->{'style'});
        $this->title->{'style'} = 'display:inline-block;';
        
        $this->actionsContainer->add($widget);
        
        return $widget;
    }
    
    /**
     * Add the panel content
     */
    public function add($content)
    {
        $this->body->add($content);
        
        if ($content instanceof BootstrapFormWrapper)
        {
            $buttons = $content->detachActionButtons();
            if ($buttons)
            {
                foreach ($buttons as $button)
                {
                    $this->footer->add( $button );
                }
                parent::add($this->footer);
            }
        }
        
        return $this->body;
    }
    
    /**
     * Return panel header
     */
    public function getHeader()
    {
        return $this->head;
    }
    
    /**
     * Return panel body
     */
    public function getBody()
    {
        return $this->body;
    }
    
    /**
     * Return panel footer
     */
    public function getFooter()
    {
        return $this->footer;
    }
    
    /**
     * Add footer
     */
    public function addFooter($footer)
    {
        $this->footer->add( $footer );
        parent::add($this->footer);
    }
}
