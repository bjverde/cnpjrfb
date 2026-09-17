<?php
namespace Adianti\Widget\Util;

use Adianti\Database\TTransaction;
use Adianti\Database\TRecord;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Base\TElement;
use Adianti\Control\TAction;
use Adianti\Util\AdiantiTemplateHandler;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Template\THtmlRenderer;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Util\TDropDown;

use stdClass;
use ApplicationTranslator;

/**
 * Card
 *
 * @version    8.6
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TCardView extends TElement
{
    protected $items;
    protected $itemActions;
    protected $itemActionGroups;
    protected $templatePath;
    protected $itemTemplate;
    protected $itemTemplateCallback;
    protected $titleTemplate;
    protected $useButton;
    protected $titleField;
    protected $contentField;
    protected $colorField;
    protected $searchAttributes;
    protected $itemHeight;
    protected $contentHeight;
    protected $itemDatabase;
    protected $itemClass;
    protected $useDefaultClickBody;
    protected $useDefaultClickHead;
    protected $itemWidth;
    protected $pageNavigation;
    protected $searchForm;
    protected $forPrinting;
    protected $pageSize;
    protected $pageOrientation;
    protected $metadata;
    
    /**
     * Class Constructor
     */
	public function __construct()
    {
        parent::__construct('div');
        $this->items           = [];
        $this->itemActions     = [];
        $this->useButton       = FALSE;
        $this->searchAttributes = [];
        $this->itemHeight      = NULL;
        $this->itemWidth       = NULL;
        $this->contentHeight   = NULL;
        $this->useDefaultClickHead = FALSE;
        $this->useDefaultClickBody = FALSE;
        $this->forPrinting     = false;
        $this->metadata        = [];
        
        $this->{'id'}          = 'tcard_' . mt_rand(1000000000, 1999999999);
        $this->{'class'}       = 'card-wrapper';
    }
    
    /**
     * Enable default click in title and body
     */
    public function enableDefaultClick($head = TRUE, $body = TRUE)
    {
        $this->useDefaultClickHead = $head;
        $this->useDefaultClickBody = $body;
    }
    
    /**
     * Set item min height
     * @param $height min height
     */
    public function setItemHeight($height)
    {
        $this->itemHeight = $height;
    }
    
    /**
     * Set item min width
     * @param $width min width
     */
    public function setItemWidth($width)
    {
        $this->itemWidth = $width;
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
     * Set content min height
     * @param $height min height
     */
    public function setContentHeight($height)
    {
        $this->contentHeight = $height;
    }
    
    /**
     * Set title attribute
     * @param $field attribute name 
     */
    public function setTitleAttribute($field)
    {
        $this->titleField = $field;
    }
    
    /**
     * Set content attribute
     * @param $field attribute name 
     */
    public function setContentAttribute($field)
    {
        $this->contentField = $field;
    }
    
    /**
     * Set color attribute
     * @param $field attribute name 
     */
    public function setColorAttribute($field)
    {
        $this->colorField = $field;
    }

    /**
     * Set custom class card
     * @param $class class name 
     */
    public function setItemClass($class)
    {
        $this->itemClass = $class;
    }
    
    /**
     * Clear items
     */
    public function clear()
    {
        $this->items = [];
    }
    
    /**
     * Add item
     * @param  $object Item data object
     */
    public function addItem($object)
    {
        $this->items[] = $object;
    }
    
    /**
     * Display icons as buttons
     */
    public function setUseButton()
    {
        $this->useButton = TRUE;
    }
    
    /**
     * Set card item template for rendering
     * @param  $path   Template path
     */
    public function setTemplatePath($path)
    {
        $this->templatePath = $path;
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
     * Set card item callback for rendering
     * @param  $callback Callback
     */
    public function setItemTemplateCallback(Callable $callback)
    {
        $this->itemTemplateCallback = $callback;
    }
    
    /**
     * Set card title template for rendering
     * @param  $template   Template content
     */
    public function setTitleTemplate($template)
    {
        $this->titleTemplate = $template;
    }
    
    /**
     * Add item action
     * @param  $label             Action label
     * @param  $action            Action callback (TAction)
     * @param  $icon              Action icon
     * @param  $display_condition Display condition
     */
    public function addAction(TAction $action, $label, $icon = NULL, $display_condition = NULL, $title = NULL)
    {
        $itemAction            = new stdClass;
        $itemAction->label     = $label;
        $itemAction->action    = $action;
        $itemAction->icon      = $icon;
        $itemAction->condition = $display_condition;
        $itemAction->title     = $title;
        
        $this->itemActions[]   = $itemAction;
        
        return $itemAction;
    }
    
    /**
     * Add action group
     * @param  $title             Action group title
     * @param  $actions           Array of actions
     * @param  $icon              Action group icon
     */
    public function addActionGroup($title, $actions, $icon)
    {
        $this->itemActionGroups[] = [$title, $actions, $icon];
    }
    
    /**
     * Render item
     */
    private function renderItem($item)
    {
        if (!empty($this->templatePath))
        {
            $html = new THtmlRenderer($this->templatePath);
            $html->enableSection('main');
            $html->enableTranslation();
            $html = AdiantiTemplateHandler::replace($html->getContents(), $item);
            
            return $html;
        }
        
        $titleField   = $this->titleField;
        $contentField = $this->contentField;
        $colorField   = $this->colorField;
        
        $item_wrapper              = new TElement('div');
        $item_wrapper->{'class'}   = 'panel card panel-default card-item';

        if ($this->itemClass)
        {
            $item_wrapper->{'class'} .= " {$this->itemClass}";
        }
        
        if ($colorField && $item->$colorField)
        {
            $item_wrapper->{'style'}   = 'border-top: 3px solid '.$item->$colorField;
        }
        
        if ($titleField)
        {
            $item_title = new TElement('div');
            $item_title->{'class'} = 'panel-heading card-header card-item-title';
            $titleField = (strpos($titleField, '{') === FALSE) ? ( '{' . $titleField . '}') : $titleField;
            $item_title->add(AdiantiTemplateHandler::replace($titleField, $item));
        }
        
        if (!empty($this->titleTemplate))
        {
            $item_title = new TElement('div');
            $item_title->{'class'} = 'panel-heading card-header card-item-title';
            $title_template = ApplicationTranslator::translateTemplate($this->titleTemplate);
            $title_template = AdiantiTemplateHandler::replace($title_template, $item);
            $item_title->add($title_template);
        }
        
        if ($contentField)
        {
            $item_content = new TElement('div');
            $item_content->{'class'} = 'panel-body card-body card-item-content';
            $contentField = (strpos($contentField, '{') === FALSE) ? ( '{' . $contentField . '}') : $contentField;
            $item_content->add(AdiantiTemplateHandler::replace($contentField, $item));
        }
        
        if (!empty($this->itemTemplate))
        {
            $item_content = new TElement('div');
            $item_content->{'class'} = 'panel-body card-body card-item-content';
            $item_template = ApplicationTranslator::translateTemplate($this->itemTemplate);
            $item_template = AdiantiTemplateHandler::replace($item_template, $item);
            $item_content->add($item_template);
        }
        
        if (!empty($this->itemTemplateCallback))
        {
            $item_content = new TElement('div');
            $item_content->{'class'} = 'panel-body card-body card-item-content';
            $item_template = ApplicationTranslator::translateTemplate(call_user_func($this->itemTemplateCallback, $item));
            $item_template = AdiantiTemplateHandler::replace($item_template, $item);
            $item_content->add($item_template);
        }
        
        if (!empty($item_title))
        {
            $item_wrapper->add($item_title);
        }
        
        if (!empty($item_content))
        {
            $item_wrapper->add($item_content);
            
            if (!empty($this->contentHeight))
            {
                $item_content->{'style'}   = 'min-height:'.$this->contentHeight;
                
                if (strstr((string) $this->size, '%') !== FALSE)
                {
                    $item_content->{'style'}   = 'min-height:'.$this->contentHeight;
                }
                else
                {
                    $item_content->{'style'}   = 'min-height:'.$this->contentHeight.'px';
                }
            }
        }
        
        if (!empty($this->itemHeight))
        {
            $item_wrapper->{'style'}   = 'min-height:'.$this->itemHeight;
            
            if (strstr((string) $this->size, '%') !== FALSE)
            {
                $item_wrapper->{'style'}   = 'min-height:'.$this->itemHeight;
            }
            else
            {
                $item_wrapper->{'style'}   = 'min-height:'.$this->itemHeight.'px';
            }
        }
        
        if (!empty($this->itemWidth))
        {
            $item_wrapper->{'style'} .= ';width:'.$this->itemWidth;
        }
        
        if (count($this->searchAttributes) > 0)
        {
            $item_wrapper->{'id'} = 'row_' . mt_rand(1000000000, 1999999999);
            
            foreach ($this->searchAttributes as $search_att)
            {
                if (isset($item->$search_att))
                {
                    $row_dom_search_att = 'search_' . $search_att;
                    $item_wrapper->$row_dom_search_att = $item->$search_att;
                }
            }
        }
        
        if ( (!empty($this->itemActions) || !empty($this->itemActionGroups)) && !$this->forPrinting)
        {
            $item_actions = $this->renderItemActions($item);
            $first_action = $item_actions[1];
            $item_wrapper->add( $item_actions[0] );
            
            if ( ($this->useDefaultClickHead || $this->useDefaultClickBody) && (!empty($first_action)))
            {
                if ($this->useDefaultClickHead)
                {
                    $item_title->{'style'} .= ';cursor:pointer';
                    $item_title->{'onclick'} = "__adianti_load_page('{$first_action}')";
                }
                if ($this->useDefaultClickBody)
                {
                    $item_content->{'style'} .= ';cursor:pointer';
                    $item_content->{'onclick'} = "__adianti_load_page('{$first_action}')";
                }
            }
        }
        
        return $item_wrapper;
    }
    
    /**
     * Add a search attribute
     */
    public function addSearchAttribute($attribute)
    {
        $this->searchAttributes[] = $attribute;
    }
    
    /**
     * Enable fuse search
     * @param $input Field input for search
     * @param $attribute Attribute name
     */
    public function enableSearch(TField $input, $attribute) 
    {
        $input_id    = $input->getId();
        $card_id = $this->{'id'};
        $this->searchAttributes[] = $attribute;
        TScript::create("__adianti_input_fuse_search('#{$input_id}', 'search_{$attribute}', '#{$card_id} .card-item')");
    }
    
    /**
     * Render item actions
     */
    private function renderItemActions($object = NULL)
    {
        $div            = new TElement('div');
        $div->{'class'} = 'panel-footer card-footer card-item-actions';
        
        $has_action = false;
        $first_action = null;
        
        foreach ($this->itemActions as $key => $action)
        {
            $action_label = $action->label;
            $action_title = $action->title;
            $action_icon  = $action->icon;
            
            if ($object instanceof TRecord)
            {
                $action_label = $object->render($action_label ?? '');
                $action_title = $object->render($action_title ?? '');
                $action_icon  = $object->render($action_icon  ?? '');
            }
            
            if (empty($action->condition) OR call_user_func($action->condition, $object))
            {
                $has_action = true;
                
                $item_action = clone $action->action;
                if ($item_action->getFieldParameters())
                {
                    $key = $item_action->getFieldParameters()[0];
                    $item_action->setParameter('key', $object->$key);
                }
                
                $url = $item_action->prepare($object)->serialize();
                
                if (empty($first_action))
                {
                    $first_action = $url;
                }
                
                if ($this->useButton)
                {
                    $button = new TElement('a');
                    $button->{'class'} = !empty($action->{'buttonClass'}) ? $action->{'buttonClass'} : 'btn btn-default';
                    $button->{'href'} = $url;
                    $button->{'generator'} = 'adianti';
                    $button->add(new TImage($action_icon));
                    $button->add($action_label);
                    
                    if (!empty($action_title))
                    {
                        $button->{'title'} = $action_title;
                        $button->{'titside'} = 'bottom';
                    }
                    
                    if ($item_action->isPopover())
                    {
                        unset($button->{'href'});
                        unset($button->{'generator'});
                        
                        $button->{'popaction'} = $item_action->prepare($object)->serialize(false);
                        $button->{'poptrigger'} = 'click';
                        $button->{'data-popover'} = 'true';
                    }
                    
                    $div->add($button);
                }
                else
                {
                    $icon                = new TImage($action_icon);
                    $icon->{'style'}    .= ';cursor:pointer;margin-right:4px;';
                    $icon->{'title'}     = $action_label;
                    $icon->{'generator'} = 'adianti';
                    $icon->{'href'}      = $url;
                    
                    if ($item_action->isPopover())
                    {
                        unset($icon->{'href'});
                        unset($icon->{'generator'});
                        
                        $icon->{'popaction'} = $item_action->prepare($object)->serialize(false);
                        $icon->{'poptrigger'} = 'click';
                        $icon->{'data-popover'} = 'true';
                    }
                    
                    $div->add($icon);
                }
            }
        }
        
        if (!empty($this->itemActionGroups))
        {
            foreach ($this->itemActionGroups as $itemActionGroup)
            {
                $title   = $itemActionGroup[0];
                $actions = $itemActionGroup[1];
                $icon    = $itemActionGroup[2];
                
                $drop = new TDropDown($title, $icon, FALSE);
                
                foreach ($actions as $action)
                {
                    $item_action = $action[1]->prepare($object);
                    $condition = $action[3] ?? null;
                    
                    if (empty($first_action))
                    {
                        $first_action = $item_action->serialize();
                    }
                    
                    if (empty($condition) OR call_user_func($condition, $object))
                    {
                        $has_action = true;
                        $drop->addAction($action[0], $item_action, $action[2]);
                    }
                }
                
                $div->add($drop);
            }
        }
        
        if (!$has_action)
        {
            $div->hide();
            
        }

        return [$div, $first_action];
    }
    
    /**
     * Assign a PageNavigation object
     * @param $pageNavigation object
     */
    public function setPageNavigation($pageNavigation)
    {
        $this->pageNavigation = $pageNavigation;
    }
    
    /**
     * Return the assigned PageNavigation object
     * @return $pageNavigation object
     */
    public function getPageNavigation()
    {
        return $this->pageNavigation;
    }
    
    /**
     * Assign a TForm object
     * @param $searchForm object
     */
    public function setSearchForm($searchForm)
    {
        $this->searchForm = $searchForm;
    }
    
    /**
     * Return the assigned Search form object
     * @return TForm object
     */
    public function getSearchForm()
    {
        return $this->searchForm;
    }
    
    /**
     * Prepare for printing
     */
    public function prepareForPrinting()
    {
        $this->forPrinting = true;
    }
    
    /**
     * Return if the object is being printed
     */
    public function isPrinting()
    {
        return $this->forPrinting;
    }
    
    /**
     * Set page Size
     * @param $page_size (a3,a4,a5,letter,legal)
     */
    public function setPageSize($page_size)
    {
        $this->pageSize = $page_size;
    }
    
    /**
     * Return the page size
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }
    
    /**
     * Set page orientation
     * @param $page_orientation (portrait, landscape)
     */
    public function setPageOrientation($page_orientation)
    {
        $this->pageOrientation = $page_orientation;
    }
    
    /**
     * Return the page orientation
     */
    public function getPageOrientation()
    {
        return $this->pageOrientation;
    }
    
    /**
     * Set metadata
     */
    public function setMetadata($metadata, $value)
    {
        $this->metadata[$metadata] = $value;
    }
    
    /**
     * Get metadata
     */
    public function getMetadata($metadata)
    {
        return $this->metadata[$metadata] ?? null;
    }
    
    /**
     * Show cards
     */
    public function show()
    {
        if ($this->items)
        {
            if (!empty($this->itemDatabase))
            {
                TTransaction::open($this->itemDatabase);
            }
            
            foreach ($this->items as $item)
            {
                $this->add($this->renderItem($item));
            }
            
            if (!empty($this->itemDatabase))
            {
                TTransaction::close();
            }
        }
        
        parent::show();
    }
}
