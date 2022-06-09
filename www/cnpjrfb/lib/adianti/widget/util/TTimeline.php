<?php
namespace Adianti\Widget\Util;

use Adianti\Database\TTransaction;
use Adianti\Widget\Base\TElement;
use Adianti\Control\TAction;
use Adianti\Widget\Form\TDateTime;
use Adianti\Util\AdiantiTemplateHandler;
use Adianti\Widget\Template\THtmlRenderer;

use stdClass;
use ApplicationTranslator;

/**
 * Timeline
 *
 * @version    7.4
 * @package    widget
 * @subpackage util
 * @author     Artur Comunello
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TTimeline extends TElement
{
    protected $useBothSides;
    protected $items;
    protected $finalIcon;
    protected $timeDisplayMask;
    protected $actions;
    protected $itemTemplate;
    protected $templatePath;
    protected $itemDatabase;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct('ul');
        $this->{'id'} = 'ttimeline_'.mt_rand(1000000000, 1999999999);
        $this->{'class'} = 'timeline';
        $this->timeDisplayMask = 'yyyy-mm-dd';
        
        $this->items = [];
        $this->actions = [];
    }
    
    /**
     * Define the final timeline icon
     * @param  $icon icon
     */
    public function setFinalIcon( $icon )
    {
        $this->finalIcon = $icon;
    }
    
    /**
     * Define the labelmask
     * @param  $mask Mask
     */
    public function setTimeDisplayMask( $mask )
    {
        $this->timeDisplayMask = $mask;
    }
    
    /**
     * Define the labelmask
     * @param  $mask Mask
     */
    public function setUseBothSides()
    {
        $this->useBothSides = true;
    }
    
    /**
     * Set card item template for rendering
     * @param  $template   Template content
     */
    public function setItemTemplate($template)
    {
        $this->itemTemplate = $template;
    }
    
    /**
     * Define the item template path
     * @param  $template_path Template path
     */
    public function setTemplatePath( $template_path )
    {
        $this->templatePath = $template_path;
    }
    
    /**
     * Set item min database
     * @param $database min database
     */
    public function setItemDatabase($database)
    {
        $this->itemDatabase = $database;
    }
    
    /**
     * Add Item
     * @param  $id       ID
     * @param  $title    Title
     * @param  $content  Item content
     * @param  $date     Item date
     * @param  $icon     Item icon
     * @param  $align    Item align
     * @param  $object   Item data object
     */
    public function addItem( $id, $title, $content, $date, $icon, $align = null, $object = null  )
    {
        if (is_null($object))
        {
            $object = new stdClass;
        }
        
        if (empty($object->{'id'}))
        {
            $object->{'id'} = $id;
        }
        
        $item = new stdClass;
        $item->{'id'}      = $id;
        $item->{'title'}   = $title;
        $item->{'content'} = $content;
        $item->{'date'}    = $date;
        $item->{'icon'}    = $icon;
        $item->{'align'}   = $align;
        $item->{'object'}  = $object;
        
        $this->items[] = $item;
    }
    
    /**
     * Add Action
     * @param  $label             Action Label
     * @param  $action            Action
     * @param  $field             Action field
     * @param  $icon              Action icon
     * @param  $btn_class         Action button class
     * @param  $display_condition Action display condition
     */
    public function addAction(TAction $action, $label, $icon, $display_condition = null)
    {
        $action->setProperty('label', $label);
        $action->setProperty('icon',  $icon);
        $action->setProperty('display_condition', $display_condition );
        
        $this->actions[] = $action;
    }
    
    /**
     * Render  Action
     * @param  $object Data object
     */
    private function renderItemActions( $object = null )
    {
        if ($this->actions)
        {
            $footer = new TElement( 'div' );
            $footer->{'class'} = 'timeline-footer';
            
            foreach ($this->actions as $action_template)
            {
                if ( empty( $object ) )
                {
                    $action = clone $action_template;
                }
                else
                {
                    $action = $action_template->prepare($object);
                }
                
                // get the action properties
                $icon      = $action->getProperty('icon');
                $label     = $action->getProperty('label');
                $condition = $action->getProperty('display_condition');
                
                if (empty($condition) OR call_user_func($condition, $object))
                {
                    $button = new TElement('button');
                    $button->{'onclick'} = "__adianti_load_page('{$action->serialize()}');return false;";
                    
                    $span = new TElement('span');
                    $span->add( new TImage($icon) );
                    $span->add( $label );
                    $button->add( $span );
                    $button->{'class'} = $action->getProperty('btn-class') ?? 'btn btn-default';
                    
                    $footer->add( $button );
                }
            }
            return $footer;
        }
    }
    
    /**
     * Render label
     * @param $label Label
     */
    private function defaultItemRender( $item )
    {
        $span = new TElement( 'span' );
        $span->{'class'} = 'time';
        
        if (strlen($item->{'date'}) > 10)
        {
            $span->add( new TImage( 'far:clock' ) );
            $span->add( TDateTime::convertToMask( $item->{'date'}, 'yyyy-mm-dd hh:ii:ss', 'hh:ii' ) );
        }
        
        $title = new TElement( 'a' );
        $title->add( AdiantiTemplateHandler::replace( $item->{'title'}, $item->{'object'} ) );
        
        if (!empty($item->{'title'}))
        {
            $h3 = new TElement( 'h3' );
            $h3->{'class'} = 'timeline-header';
            $h3->add( $title );
        }
        
        $div = new TElement( 'div' );
        $div->{'class'} = 'timeline-body';
        
        $div->add( AdiantiTemplateHandler::replace( $item->{'content'}, $item->{'object'} ) );
        
        $item_div = new TElement( 'div' );
        $item_div->{'class'} = 'timeline-item ';
        
        if( $this->useBothSides)
        {
            if ( empty( $item->{'align'} ) )
            {
                $item->{'align'} = 'left';
            }
            
            $item_div->{'class'} .= 'timeline-item-' . $item->{'align'};
        }
        
        $item_div->add( $span );
        if (!empty($h3))
        {
            $item_div->add( $h3 );
        }
        $item_div->add( $div );
        $item_div->add( $this->renderItemActions( $item->{'object'} ) );
        
        return $item_div;
    }
    
    /**
     * Render item
     * @param $item Item
     */
    private function renderItem( $item )
    {
        if ( !empty( $this->templatePath) AND !empty( $item->{'object'}) )
        {
            $template = new THtmlRenderer( $this->templatePath );
            $template->enableSection( 'main' );
            $content = $item->{'object'}->render( $template->getContents() );
        }
        else if (!empty($this->itemTemplate))
        {
            $item_template = ApplicationTranslator::translateTemplate($this->itemTemplate);
            $item_template = AdiantiTemplateHandler::replace($item_template, $item->{'object'});
            $content = $item_template;
        }
        else
        {
            $content = $this->defaultItemRender( $item );
        }
        
        $li = new TElement( 'li' );
        $li->add( new TImage( $item->{'icon'} . ' line-icon') );
        $li->add( $content );
        
        return $li;
    }
    
    /**
     * Render label
     * @param $label Label
     */
    private function renderLabel( $label )
    {
        $li = new TElement( 'li' );
        $li->{'class'} = 'time-label';
        
        if( $this->useBothSides )
        {
            $li->{'class'} .= ' time-label-bothsides';
        }
        
        $li->add( TElement::tag( 'span', $label ) );
        
        return $li;
    }
    
    /**
     * Render items
     */
    private function renderItems()
    {
        if ($this->items)
        {
            if (!empty($this->itemDatabase))
            {
                TTransaction::open($this->itemDatabase);
            }
            
            $first = reset( $this->items );
            $label = TDateTime::convertToMask( $first->{'date'}, strlen($first->{'date'}) > 10 ? 'yyyy-mm-dd hh:ii:ss' : 'yyyy-mm-dd', $this->timeDisplayMask );
            parent::add( $this->renderLabel( $label ) );
            
            foreach ($this->items as $item)
            {
                $newLabel = TDateTime::convertToMask( $item->{'date'}, strlen($item->{'date'}) > 10 ? 'yyyy-mm-dd hh:ii:ss' : 'yyyy-mm-dd', $this->timeDisplayMask );
                
                if( $newLabel != $label)
                {
                    $label = $newLabel;
                    parent::add( $this->renderLabel( $label ) );
                }
                
                parent::add( $this->renderItem( $item ) );
            }
            
            if (!empty($this->itemDatabase))
            {
                TTransaction::close();
            }
        }
    }
    
    /**
     * Render final icon
     */
    private function renderFinalIcon()
    {
        if( $this->finalIcon )
        {
            $li = new TElement( 'li' );
            $li->add( new TImage( $this->finalIcon . ' line-icon'));
            
            parent::add( $li );
        }
    }
    
    /**
     * Show
     */
    public function show()
    {
        $this->renderItems();
        $this->renderFinalIcon();
        
        if( $this->useBothSides )
        {
            $this->{'class'} .= ' timeline-bothsides';
        }
        
        parent::show();
    }
}