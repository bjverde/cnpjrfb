<?php
namespace Adianti\Widget\Util;

use Adianti\Database\TTransaction;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Base\TElement;
use Adianti\Control\TAction;
use Adianti\Util\AdiantiTemplateHandler;
use Adianti\Widget\Template\THtmlRenderer;

use stdClass;
use ApplicationTranslator;

/**
 * Kanban
 *
 * @version    7.3
 * @package    widget
 * @subpackage util
 * @author     Artur Comunello
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TKanban extends TElement
{
    protected $kanban;
    protected $items;
    protected $stages;
    protected $itemActions;
    protected $stageActions;
    protected $stageShortcuts;
    protected $itemDropAction;
    protected $stageDropAction;
    protected $templatePath;
    protected $itemTemplate;
    protected $itemDatabase;
    
    /**
     * Class Constructor
     */
	public function __construct()
    {
        parent::__construct('div');
        $this->items          = [];
        $this->stages         = [];
        $this->itemActions    = [];
        $this->stageActions   = [];
        $this->stageShortcuts = [];
        
        $this->kanban                 = new TElement('div');
        $this->kanban->{'id'}         = 'tkanban_' . mt_rand(1000000000, 1999999999);
        $this->kanban->{'item_class'} = 'kanban-item-wrapper';
        $this->kanban->{'class'}      = 'kanban-board';
    }
    
    /**
     * Add stage to kanban board
     * @param  $id     Stage id
     * @param  $title  Stage title
     * @param  $color  Stage color
     * @param  $object Stage data object
     */
    public function addStage($id, $title, $object = null, $color = null)
    {
        if (is_null($object))
        {
            $object = new stdClass;
        }
        
        $stage             = new stdClass;
        $stage->{'id'}     = $id;
        $stage->{'title'}  = $title;
        $stage->{'object'} = $object;
        $stage->{'color'}  = $color;
        
        $this->stages[] = $stage;
    }
    
    /**
     * Add item to stage
     * @param  $id       Item id
     * @param  $stage_id Stage id
     * @param  $title    Item title
     * @param  $content  Item content
     * @param  $color    Item color
     * @param  $object   Item data object
     */
    public function addItem($id, $stage_id, $title, $content, $color = null, $object = null)
    {
        if (is_null($object))
        {
            $object = new stdClass;
            $object->{'title'} = $title;
            $object->{'content'} = $content;
            $object->{'color'} = $color;
        }

        if (empty($object->{'id'}))
        {
            $object->{'id'} = $id;
        }

        $item              = new stdClass;
        $item->{'id'}      = $id;
        $item->{'title'}   = $title;
        $item->{'color'}   = $color;
        $item->{'content'} = $content;
        $item->{'object'}  = $object;
        
        $this->items[$stage_id][] = $item;
    }
    
    /**
     * Set kanban item template for rendering
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
     * Set item min database
     * @param $database min database
     */
    public function setItemDatabase($database)
    {
        $this->itemDatabase = $database;
    }
    
    /**
     * Set item drop action
     * @param  $action  TAction object
     */
    public function setItemDropAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->itemDropAction = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
    }
    
    /**
     * Set stage drop action
     * @param  $action  TAction object
     */
    public function setStageDropAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->stageDropAction = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
    }
    
    /**
     * Add item action
     * @param  $label             Action label
     * @param  $action            Action callback (TAction)
     * @param  $icon              Action icon
     * @param  $display_condition Display condition
     */
    public function addItemAction($label, TAction $action, $icon = NULL, $display_condition = NULL)
    {
        $itemAction            = new stdClass;
        $itemAction->label     = $label;
        $itemAction->action    = $action;
        $itemAction->icon      = $icon;
        $itemAction->condition = $display_condition;
        
        $this->itemActions[]   = $itemAction;
    }
    
    /**
     * Add stage action
     * @param  $label             Action label
     * @param  $action            Action callback (TAction)
     * @param  $icon              Action icon
     */
    public function addStageAction($label, TAction $action, $icon = NULL, $display_condition = NULL)
    {
        $stageAction            = new stdClass;
        $stageAction->label     = $label;
        $stageAction->action    = $action;
        $stageAction->icon      = $icon;
        $stageAction->condition = $display_condition;
        
        $this->stageActions[] = $stageAction;
    }
    
    /**
     * Add stage shortcut
     * @param  $label             Action label
     * @param  $action            Action callback (TAction)
     * @param  $icon              Action icon
     */
    public function addStageShortcut($label, TAction $action, $icon = NULL)
    {
        $stageAction          = new stdClass;
        $stageAction->label   = $label;
        $stageAction->action  = $action;
        $stageAction->icon    = $icon;
        
        $this->stageShortcuts[] = $stageAction;
    }
    
    /**
     * Render stage items
     */
    private function renderStageItems($stage)
    {
        $itemSortable               = new TElement('div');
        $itemSortable->{'class'}    = 'kanban-item-sortable ' . $this->kanban->item_class;
        $itemSortable->{'stage_id'} = $stage->{'stage_id'};
        
        if (!empty($this->itemDatabase))
        {
            TTransaction::open($this->itemDatabase);
        }
        
        if (!empty($this->items[$stage->{'stage_id'}]))
        {
            foreach ($this->items[$stage->{'stage_id'}] as $key => $item)
            {
                $itemSortable->add(self::renderItem($item));
            }
        }
        
        if (!empty($this->itemDatabase))
        {
            TTransaction::close();
        }
        
        $stage->add($itemSortable);
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
            $html = AdiantiTemplateHandler::replace($html->getContents(), $item->{'object'});
            return $html;
        }
        
        $item_wrapper              = new TElement('div');
        $item_wrapper->{'item_id'} = $item->id;
        $item_wrapper->{'class'}   = 'kanban-item';
        
        if (!empty($item->color))
        {
            $item_wrapper->{'style'}   = 'border-top: 3px solid '.$item->color;
        }
        
        $item_title = new TElement('div');
        $item_title->{'class'} = 'kanban-item-title';
        $item_title->add(AdiantiTemplateHandler::replace($item->{'title'}, $item->{'object'}));
        
        $item_content = new TElement('div');
        $item_content->{'class'} = 'kanban-item-content';
        $item_content->add(AdiantiTemplateHandler::replace($item->{'content'}, $item->{'object'}));
        
        if (!empty($this->itemTemplate))
        {
            $item_content = new TElement('div');
            $item_content->{'class'} = 'kanban-item-content';
            $item_template = ApplicationTranslator::translateTemplate($this->itemTemplate);
            $item_template = AdiantiTemplateHandler::replace($item_template, $item);
            $item_content->add($item_template);
        }
        
        $item_wrapper->add($item_title);
        $item_wrapper->add($item_content);

        if (!empty($this->itemActions))
        {
            $item_wrapper->add($this->renderItemActions($item->id, $item->object));
        }

        return $item_wrapper;
    }
    
    /**
     * Render stages
     */
    private function renderStages()
    {
        foreach ($this->stages as $key => $stage)
        {
            $title            = new TElement('span');
            $title->{'class'} = 'kanban-title';
            $title->add(AdiantiTemplateHandler::replace($stage->{'title'}, $stage->{'object'}));

            $stageDiv                = new TElement('div');
            $stageDiv->{'stage_id'}  = $stage->{'id'};
            $stageDiv->{'class'}     = 'kanban-stage';
            if (!empty($stage->{'color'}))
            {
                $stageDiv->{'style'} = 'background:'.$stage->{'color'};
            }
            $stageDiv->add($title);

            if (!empty($this->stageActions))
            {
                $title = $stageDiv->children[0];
                $title->add($this->renderStageActions( $stage->{'id'}, $stage ));
            }
            
            $this->renderStageItems($stageDiv);
            $this->kanban->add($stageDiv);
            $stageDiv->add($this->renderStageShortcuts( $stage->{'id'} ));
        }
    }
    
    /**
     * Render item actions
     */
    private function renderItemActions($itemId, $object = NULL)
    {
        $div            = new TElement('div');
        $div->{'class'} = 'kanban-item-actions';
        
        foreach ($this->itemActions as $key => $actionTemplate)
        {
            $itemAction = $actionTemplate->action->prepare($object);
            
            if (empty($actionTemplate->condition) OR call_user_func($actionTemplate->condition, $object))
            {
                $itemAction->setParameter('id', $itemId);
                $itemAction->setParameter('key', $itemId);
                $url = $itemAction->serialize();
                
                $icon                = new TImage($actionTemplate->icon);
                $icon->{'style'}    .= ';cursor:pointer;margin-right:4px;';
                $icon->{'title'}     = $actionTemplate->label;
                $icon->{'generator'} = 'adianti';
                $icon->{'href'}      = $url;
                
                $div->add($icon);
            }
        }
        
        return $div;
    }
    
    /**
     * Render stage actions
     */
    private function renderStageActions($stage_id, $stage)
    {
        $icon                  = new TImage('mi:more_vert');
        $icon->{'data-toggle'} = 'dropdown';

        $ul = new TElement('ul');
        $ul->{'class'} = 'dropdown-menu pull-right';
        
        foreach ($this->stageActions as $key => $stageActionTemplate)
        {
            $stageAction = $stageActionTemplate->action->prepare($stage);
            
            if (empty($stageActionTemplate->condition) OR call_user_func($stageActionTemplate->condition, $stage))
            {
                $stageAction->setParameter('id',  $stage_id);
                $stageAction->setParameter('key', $stage_id);
                $url = $stageAction->serialize();
                
                $action                = new TElement('a');
                $action->{'generator'} = 'adianti';
                $action->{'href'}      = $url;
                if (!empty($stageActionTemplate->icon))
                {
                    $action->add(new TImage($stageActionTemplate->icon));
                }
                $action->add($stageActionTemplate->label);
                
                $li = new TElement('li');
                $li->add($action);
                $ul->add($li);
            }
        }
        
        $dropWrapper = new TElement('div');
        $dropWrapper->{'style'} = 'cursor:pointer;';
        $dropWrapper->{'class'} = 'btn-group user-helper-dropdown';
        $dropWrapper->add($icon);
        $dropWrapper->add($ul);

        $stageActions = new TElement('span');
        $stageActions->{'style'} = 'float: right;';
        $stageActions->{'class'} = 'kanban-stage-actions';
        $stageActions->add($dropWrapper);
        
        return $stageActions;
    }
    
    /**
     * Render stage shortcuts
     */
    private function renderStageShortcuts($stage_id)
    {
        $actions_wrapper = new TElement('div');
        $actions_wrapper->{'class'} = 'kanban-shortcuts';
        
        foreach ($this->stageShortcuts as $key => $stageAction)
        {
            $stageAction->action->setParameter('id',  $stage_id);
            $stageAction->action->setParameter('key', $stage_id);
            $url = $stageAction->action->serialize();
            
            $action                = new TElement('a');
            $action->{'generator'} = 'adianti';
            $action->{'href'}      = $url;
            if (!empty($stageAction->icon))
            {
                $action->add(new TImage($stageAction->icon));
            }
            $action->add($stageAction->label);
            
            $actions_wrapper->add($action);
        }
        
        return $actions_wrapper;
    }
    
    /**
     * Show kanban
     */
    public function show()
    {
        $this->renderStages();
        $this->add($this->kanban);
        $this->{'style'} .= ';overflow-x:auto';
        
        if (!empty($this->stageDropAction))
        {
            $stage_drop_action_string = $this->stageDropAction->serialize();
            TScript::create("kanban_start_board('{$this->kanban->id}', '{$stage_drop_action_string}');");
        }

        if (!empty($this->itemDropAction))
        {
            $item_drop_action_string = $this->itemDropAction->serialize();
            TScript::create("kanban_start_item('{$this->kanban->item_class}', '{$item_drop_action_string}');");
        }

        parent::show();
    }
}