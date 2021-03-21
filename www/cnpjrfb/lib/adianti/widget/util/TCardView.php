<?php
namespace Adianti\Widget\Util;

use Adianti\Database\TTransaction;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Base\TElement;
use Adianti\Control\TAction;
use Adianti\Util\AdiantiTemplateHandler;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Template\THtmlRenderer;
use Adianti\Widget\Form\TButton;

use stdClass;
use ApplicationTranslator;

/**
 * Card
 *
 * @version    7.3
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TCardView extends TElement
{
    protected $items;
    protected $itemActions;
    protected $templatePath;
    protected $itemTemplate;
    protected $titleTemplate;
    protected $useButton;
    protected $titleField;
    protected $contentField;
    protected $colorField;
    protected $searchAttributes;
    protected $itemHeight;
    protected $contentHeight;
    protected $itemDatabase;
    
    /**
     * Class Constructor
     */
	public function __construct()
    {
        parent::__construct('div');
        $this->items          = [];
        $this->itemActions    = [];
        $this->useButton      = FALSE;
        $this->searchAttributes = [];
        $this->itemHeight     = NULL;
        $this->contentHeight  = NULL;
        $this->{'id'}         = 'tcard_' . mt_rand(1000000000, 1999999999);
        $this->{'class'}      = 'card-wrapper';
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
    public function addAction(TAction $action, $label, $icon = NULL, $display_condition = NULL)
    {
        $itemAction            = new stdClass;
        $itemAction->label     = $label;
        $itemAction->action    = $action;
        $itemAction->icon      = $icon;
        $itemAction->condition = $display_condition;
        
        $this->itemActions[]   = $itemAction;
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
            $item_title->add(AdiantiTemplateHandler::replace($this->titleTemplate, $item));
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
                
                if (strstr($this->size, '%') !== FALSE)
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
            
            if (strstr($this->size, '%') !== FALSE)
            {
                $item_wrapper->{'style'}   = 'min-height:'.$this->itemHeight;
            }
            else
            {
                $item_wrapper->{'style'}   = 'min-height:'.$this->itemHeight.'px';
            }
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
        
        if (!empty($this->itemActions))
        {
            $item_wrapper->add($this->renderItemActions($item));
        }
        
        return $item_wrapper;
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
        
        foreach ($this->itemActions as $key => $action)
        {
            if (empty($action->condition) OR call_user_func($action->condition, $object))
            {
                $item_action = clone $action->action;
                if ($item_action->getFieldParameters())
                {
                    $key = $item_action->getFieldParameters()[0];
                    $item_action->setParameter('key', $object->$key);
                }
                
                $url = $item_action->prepare($object)->serialize();
                
                if ($this->useButton)
                {
                    $button = new TElement('a');
                    $button->{'class'} = 'btn btn-default';
                    $button->{'href'} = $url;
                    $button->{'generator'} = 'adianti';
                    $button->add(new TImage($action->icon));
                    $button->add($action->label); 
                    $div->add($button);
                }
                else
                {
                    $icon                = new TImage($action->icon);
                    $icon->{'style'}    .= ';cursor:pointer;margin-right:4px;';
                    $icon->{'title'}     = $action->label;
                    $icon->{'generator'} = 'adianti';
                    $icon->{'href'}      = $url;
                    
                    $div->add($icon);
                }
            }
        }
        
        return $div;
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