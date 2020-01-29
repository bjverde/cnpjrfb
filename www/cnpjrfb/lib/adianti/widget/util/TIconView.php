<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Util\AdiantiTemplateHandler;

use stdClass;

/**
 * TIconView Widget
 *
 * @version    7.1
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TIconView extends TElement
{
    protected $items;
    protected $options;
    protected $itemProperties;
    protected $labelField;
    protected $iconField;
    protected $infoFields;
    protected $popover;
    protected $poptitle;
    protected $popcontent;
    protected $popside;
    protected $popcondition;
    protected $enableMoving;
    protected $moveAction;
    protected $dragSelector;
    protected $dropSelector;
    
    /**
     * Constructor Method
     */
    public function __construct()
    {
        parent::__construct('ul');
        
        $this->{'id'}    = 'ticonview_' . mt_rand(1000000000, 1999999999);
        $this->{'class'} = 'ticonview';
        $this->items   = [];
        $this->options = [];
        $this->popover = FALSE;
        $this->popside = null;
        $this->enableMoving = FALSE;
    }
    
    /**
     * Enable popover
     * @param $title Title
     * @param $content Content
     */
    public function enablePopover($title, $content, $popside = null, $popcondition = null)
    {
        $this->popover = TRUE;
        $this->poptitle = $title;
        $this->popcontent = $content;
        $this->popside = $popside;
        $this->popcondition = $popcondition;
    }
    
    /**
     * Enable move action
     * @param $source_selector Source item selector
     * @param $target_selector Target item selector
     */
    public function enableMoveAction( $moveAction, $source_selector, $target_selector )
    {
        $this->enableMoving = TRUE;
        $this->moveAction   = $moveAction;
        $this->dragSelector = $source_selector;
        $this->dropSelector = $target_selector;
    }
    
    /**
     * Set info fields
     */
    public function setInfoAttributes($fields)
    {
        $this->infoFields = $fields;
    }
    
    /**
     * Add object item with data
     */
    public function addItem($object)
    {
        $this->items[] = $object;
        
        $itemProperties = new StdClass;
        $this->itemProperties[] = $itemProperties;
        return $itemProperties;
    }
    
    /**
     * Define the field that will identify the icon
     */
    public function setIconAttribute( $iconField )
    {
        $this->iconField = $iconField;
    }
    
    /**
     * Define the field that will identify the label
     */
    public function setLabelAttribute( $labelField )
    {
        $this->labelField = $labelField;
    }
    
    /**
     * Add a context menu option
     */
    public function addContextMenuOption($label, $action = null, $icon = null, /*Callable*/ $displayCondition = null)
    {
        $this->options[] = [$label, $action, $icon, $displayCondition];
    }
    
    /**
     * Show iconview items
     */
    public function show()
    {
        if ($this->items)
        {
            foreach ($this->items as $key => $object)
            {
                $iconField      = $this->iconField;
                $labelField     = $this->labelField;
                $itemProperties = $this->itemProperties[$key];
                $first_action = null;
                
                $li = new TElement('li');
                
                // set info data in the root element for things like fuse search
                if ($this->infoFields)
                {
                    foreach ($this->infoFields as $infoField)
                    {
                        $li->$infoField = isset($object->{$infoField}) ? $object->{$infoField} : NULL;
                        $li->{"data-{$infoField}"} = isset($object->{$infoField}) ? $object->{$infoField} : NULL;
                    }
                }
                
                if ($itemProperties)
                {
                    foreach ($itemProperties as $item_prop_name => $item_prop_value)
                    {
                        $li->$item_prop_name = $item_prop_value;
                    }
                }
                
                if (empty($li->{'id'}))
                {
                    $li->{'id'} = 'ticonitem_' . mt_rand(1000000000, 1999999999);
                }
                
                $id = $li->{'id'};
                
                if ($this->popover && (empty($this->popcondition) OR call_user_func($this->popcondition, $object)))
                {
                    $poptitle   = $this->poptitle;
                    $popcontent = $this->popcontent;
                    $poptitle   = AdiantiTemplateHandler::replace($poptitle, $object);
                    $popcontent = AdiantiTemplateHandler::replace($popcontent, $object, null, true);
                    
                    $li->{'popover'} = 'true';
                    $li->{'poptitle'} = $poptitle;
                    $li->{'popcontent'} = htmlspecialchars(str_replace("\n", '', nl2br($popcontent)));
                    
                    if ($this->popside)
                    {
                        $li->{'popside'} = $this->popside;
                    }
                }
                
                $link = new TElement('a');
                $link->add(new TImage($object->$iconField));
                $link->add(TElement::tag('span', $object->$labelField));
                
                $li->add($link);
                parent::add($li);
                
                if ($this->options)
                {
                    $dropdown = new TElement('ul');
                    $dropdown->{'class'} = 'dropdown-menu pull-left dropdown-iconview';
                    $dropdown->{'style'} = 'position:fixed; display:none;';
                    
                    foreach ($this->options as $index => $option)
                    {
                        $action_label     = $option[0];
                        $action_template  = $option[1];
                        $action_icon      = $option[2];
                        $action_condition = $option[3];
                        
                        if ($action_template)
                        {
                            $item_action = clone $action_template;
                            
                            if ($this->infoFields)
                            {
                                foreach ($this->infoFields as $infoField)
                                {
                                    $info_data = isset($object->{$infoField}) ? $object->{$infoField} : NULL;
                                    $item_action->setParameter($infoField, "{$object->$infoField}");
                                }
                            }
                            $action = $item_action->prepare($object);
                            
                            if (empty($action_condition) OR call_user_func($action_condition, $object))
                            {
                                $option_li = new TElement('li');
                                $option_link = new TElement('a');
                                $option_link->{'onclick'} = "__adianti_load_page('{$action->serialize()}');";
                                $option_link->{'style'} = 'cursor: pointer';
                                
                                if ($action_icon)
                                {
                                    $image = is_object($action_icon) ? clone $action_icon : new TImage($action_icon);
                                    $image->{'style'} .= ';padding: 4px';
                                    $option_link->add($image);
                                }
                                
                                $span = TElement::tag('span', $action_label);
                                $option_link->add($span);
                                $option_li->add($option_link);
                                
                                $dropdown->add($option_li);
                                
                                if (empty($first_action))
                                {
                                    $first_action = $action;
                                }
                            }
                        }
                        else
                        {
                            if ($action_label)
                            {
                                $dropdown->add(TElement::tag('li', $action_label, ['role'=>'presentation', 'class'=>'dropdown-header']));
                            }
                            else
                            {
                                $dropdown->add(TElement::tag('li', '', ['class'=>'divider']));
                            }
                        }
                    }
                    
                    $li->add($dropdown);
                    $li->add(TScript::create("ticonview_contextmenu_start('{$id}')", false));
                    
                    if ($first_action)
                    {
                        $link->{'href'}      = $first_action->serialize();
                        $link->{'generator'} = 'adianti';
                    }
                }
            }
        }
        
        if ($this->enableMoving)
        {
            $wrapper_id = $this->{'id'};
            $source_selectors = [];
            $target_selectors = [];
            
            if ($this->dragSelector)
            {
                foreach ($this->dragSelector as $key => $value)
                {
                    if (is_array($value))
                    {
                        foreach ($value as $val)
                        {
                            $source_selectors[] = "[{$key}={$val}]";
                        }
                    }
                    else
                    {
                        $source_selectors[] = "[{$key}={$value}]";
                    }
                }
            }
            
            if ($this->dropSelector)
            {
                foreach ($this->dropSelector as $key => $value)
                {
                    if (is_array($value))
                    {
                        foreach ($value as $val)
                        {
                            $target_selectors[] = "[{$key}={$val}]";
                        }
                    }
                    else
                    {
                        $target_selectors[] = "[{$key}={$value}]";
                    }
                }
            }
            
            $source_selector_string = implode(',', $source_selectors);
            $target_selector_string = implode(',', $target_selectors);
            $move_action_string = $this->moveAction->serialize();
            
            parent::add(TScript::create("ticonview_move_start( '{$wrapper_id}', '{$move_action_string}', '{$source_selector_string}', '{$target_selector_string}' )", false));
        }
        
        parent::add(TScript::create('ticonview_bind_click()', false));
        parent::show();
    }
}
