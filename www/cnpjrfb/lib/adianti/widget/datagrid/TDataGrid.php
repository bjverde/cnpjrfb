<?php
namespace Adianti\Widget\Datagrid;

use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Container\TTable;
use Adianti\Widget\Util\TDropDown;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Util\TImage;
use Adianti\Util\AdiantiTemplateHandler;

use Math\Parser;
use Exception;

/**
 * DataGrid Widget: Allows to create datagrids with rows, columns and actions
 *
 * @version    7.6
 * @package    widget
 * @subpackage datagrid
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TDataGrid extends TTable
{
    protected $columns;
    protected $actions;
    protected $action_groups;
    protected $rowcount;
    protected $thead;
    protected $tbody;
    protected $tfoot;
    protected $height;
    protected $scrollable;
    protected $modelCreated;
    protected $pageNavigation;
    protected $defaultClick;
    protected $groupColumn;
    protected $groupTransformer;
    protected $groupTotal;
    protected $groupContent;
    protected $groupMask;
    protected $popover;
    protected $poptitle;
    protected $popside;
    protected $popcontent;
    protected $popcondition;
    protected $objects;
    protected $objectsGroup;
    protected $actionWidth;
    protected $groupCount;
    protected $groupRowCount;
    protected $columnValues;
    protected $columnValuesGroup;
    protected $HTMLOutputConversion;
    protected $searchAttributes;
    protected $outputData;
    protected $hiddenFields;
    protected $prependRows;
    protected $hasInlineEditing;
    protected $hasTotalFunction;
    protected $actionSide;
    protected $mutationAction;
    protected $forPrinting;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->modelCreated = FALSE;
        $this->defaultClick = TRUE;
        $this->popover = FALSE;
        $this->groupColumn = NULL;
        $this->groupContent = NULL;
        $this->groupMask = NULL;
        $this->groupCount = 0;
        $this->actions = array();
        $this->action_groups = array();
        $this->actionWidth = '28px';
        $this->objects = array();
        $this->objectsGroup = array();
        $this->columnValues = array();
        $this->columnValuesGroup = array();
        $this->HTMLOutputConversion = true;
        $this->searchAttributes = [];
        $this->outputData = [];
        $this->hiddenFields = false;
        $this->prependRows = 0;
        $this->hasInlineEditing = false;
        $this->hasTotalFunction = false;
        $this->actionSide = 'left';
        $this->forPrinting = false;
        
        $this->rowcount = 0;
        $this->{'class'} = 'tdatagrid_table';
        $this->{'id'}    = 'tdatagrid_' . mt_rand(1000000000, 1999999999);
    }
    
    /**
     * Set id
     */
    public function setId($id)
    {
        $this->{'id'} = $id;
    }
    
    /**
     * Define mutation action
     */
    public function setMutationAction(TAction $action)
    {
        $this->mutationAction = $action;
    }
    
    /**
     * Set actions side
     */
    public function setActionSide($side)
    {
        $this->actionSide = $side;
    }
    
    /**
     * Generate hidden fields
     */
    public function generateHiddenFields()
    {
        $this->hiddenFields = true;
    }
    
    /**
     * Disable htmlspecialchars on output
     */
    public function disableHtmlConversion()
    {
        $this->HTMLOutputConversion = false;
    }
    
    /**
     * Get raw processed output data
     */
    public function getOutputData()
    {
        return $this->outputData;
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
     * Make the datagrid scrollable
     */
    public function makeScrollable()
    {
        $this->scrollable = TRUE;
        
        if (isset($this->thead))
        {
            $this->thead->{'style'} = 'display: block';
        }
    }
    
    /**
     * Returns if datagrid is scrollable
     */
    public function isScrollable()
    {
        return $this->scrollable;
    }
    
    /**
     * Returns true if has custom width
     */
    private function hasCustomWidth()
    {
        return ( (strpos((string) $this->getProperty('style'), 'width') !== false) OR !empty($this->getProperty('width')));
    }
    
    /**
     * Set the column action width
     */
    public function setActionWidth($width)
    {
        $this->actionWidth = $width;
    }
    
    /**
     * disable the default click action
     */
    public function disableDefaultClick()
    {
        $this->defaultClick = FALSE;
    }
    
    /**
     * Define the Height
     * @param $height An integer containing the height
     */
    public function setHeight($height)
    {
        if (is_numeric($height))
        {
            $this->height = $height . 'px';
        }
        else
        {
            $this->height = $height;
        }
        
        if (!empty($this->tbody) && ($this->scrollable))
        {
            $this->tbody->{'style'} = "height: {$this->height}; display: block; overflow-y:scroll; overflow-x:hidden;";
        }
    }
    
    /**
     * Return datagrid height
     */
    public function getHeight()
    {
        return $this->height;
    }
    
    /**
     * Add a Column to the DataGrid
     * @param $object A TDataGridColumn object
     */
    public function addColumn(TDataGridColumn $object, TAction $action = null)
    {
        if ($this->modelCreated)
        {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before ^2', __METHOD__ , 'createModel') );
        }
        else
        {
            $this->columns[] = $object;
            
            if (!empty($action))
            {
                $object->setAction($action);
            }
        }
        
        return $object;
    }
    
    /**
     * Returns an array of TDataGridColumn
     */
    public function getColumns()
    {
        return $this->columns;
    }
    
    /**
     * Add an Action to the DataGrid
     * @param $object A TDataGridAction object
     */
    public function addAction(TDataGridAction $action, $label = null, $image = null)
    {
        if (!$action->fieldDefined())
        {
            throw new Exception(AdiantiCoreTranslator::translate('You must define the field for the action (^1)', $action->toString()) );
        }
        
        if ($this->modelCreated)
        {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before ^2', __METHOD__ , 'createModel') );
        }
        else
        {
            $this->actions[] = $action;
            
            if (!empty($label))
            {
                $action->setLabel($label);
            }
            
            if (!empty($image))
            {
                $action->setImage($image);
            }
        }
    }

    /**
     * Set actions to the DataGrid
     * @param $actions  TDataGridAction objects
     */
    public function setActions($actions)
    {
        $this->actions = [];

        if (! empty($actions))
        {
            foreach($actions as $action)
            {
                $this->addAction($action);
            }
        }
    }
    
    /**
     * Prepare for printing
     */
    public function prepareForPrinting()
    {
        $this->forPrinting = true;
        parent::clearChildren();
        $this->actions = [];
        $this->prependRows = 0;
        
        if ($this->columns)
        {
            foreach ($this->columns as $column)
            {
                $column->removeAction();
            }
        }
        
        $this->createModel();
    }
    
    /**
     * Add an Action Group to the DataGrid
     * @param $object A TDataGridActionGroup object
     */
    public function addActionGroup(TDataGridActionGroup $object)
    {
        if ($this->modelCreated)
        {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before ^2', __METHOD__ , 'createModel') );
        }
        else
        {
            $this->action_groups[] = $object;
        }
    }
    
    /**
     * Returns the total columns
     */
    public function getTotalColumns()
    {
        return count($this->columns) + count($this->actions) + count($this->action_groups);
    }
    
    /**
     * Set the group column for break
     */
    public function setGroupColumn($column, $mask, $transformer = null)
    {
        $this->groupColumn      = $column;
        $this->groupMask        = $mask;
        $this->groupTransformer = $transformer;
    }

    /**
     * Set the group column for break
     */
    public function useGroupTotal($groupTotal = null)
    {
        $this->groupTotal = $groupTotal;
    }
    
    /**
     * Clear the DataGrid contents
     */
    public function clear( $preserveHeader = TRUE, $rows = 0)
    {
        if ($this->prependRows > 0)
        {
            $rows += $this->prependRows;
        }
        
        if ($this->modelCreated)
        {
            // copy the headers
            $current_header = $this->children[0];
            $current_body   = $this->children[1];
            
            if ($preserveHeader)
            {
                // reset the row array
                $this->children = array();
                // add the header again
                $this->children[] = $current_header;
            }
            else
            {
                // reset the row array
                $this->children = array();
            }
            
            // add an empty body
            $this->tbody = new TElement('tbody');
            $this->tbody->{'class'} = 'tdatagrid_body';
            if ($this->scrollable)
            {
                $this->tbody->{'style'} = "height: {$this->height}; display: block; overflow-y:scroll; overflow-x:hidden;";
            }
            parent::add($this->tbody);
            
            if ($rows)
            {
                for ($n=0; $n < $rows; $n++)
                {
                    $this->tbody->add($current_body->getChildren()[$n]);
                }
            }
            
            // restart the row count
            $this->rowcount = 0;
            $this->objects = array();
            $this->objectsGroup = array();
            $this->columnValues = array();
            $this->columnValuesGroup = array();
            $this->groupContent = NULL;
        }
    }
    
    /**
     * Create header action cells
     */
    private function createHeaderActionCells( $row )
    {
        $actions_count = count($this->actions) + count($this->action_groups);
        
        if ($actions_count >0)
        {
            for ($n=0; $n < $actions_count; $n++)
            {
                $cell = new TElement('th');
                $row->add($cell);
                $cell->add('<span style="min-width:calc('.$this->actionWidth.' - 2px);display:block"></span>');
                $cell->{'class'} = 'tdatagrid_action';
                $cell->{'style'} = 'padding:0';
                $cell->{'width'} = $this->actionWidth;
            }
        }
    }
    
    /**
     * Creates the DataGrid Structure
     */
    public function createModel( $create_header = true )
    {
        if (!$this->columns)
        {
            return;
        }
        
        if ($create_header)
        {
            $this->thead = new TElement('thead');
            $this->thead->{'class'} = 'tdatagrid_head';
            parent::add($this->thead);
            
            $row = new TElement('tr');
            if ($this->scrollable)
            {
                $this->thead->{'style'} = 'display:block';
                if ($this->hasCustomWidth())
                {
                    $row->{'style'} = 'display: inline-table; width: calc(100% - 20px)';
                }
            }
            $this->thead->add($row);
            
            if ($this->actionSide == 'left')
            {
                $this->createHeaderActionCells($row);
            }
            
            // add some cells for the data
            if ($this->columns)
            {
                $output_row = [];
                // iterate the DataGrid columns
                foreach ($this->columns as $column)
                {
                    // get the column properties
                    $name  = $column->getName();
                    $label = $column->getLabel();
                    $align = $column->getAlign();
                    $width = $column->getWidth();
                    $props = $column->getProperties();
                    
                    if ($column->isSearchable())
                    {
                        $input_search = $column->getInputSearch();
                        $this->enableSearch($input_search, $name);
                        $label .= '&nbsp;'.$input_search;
                    }
                    
                    $col_action = $column->getAction();
                    if ($col_action)
                    {
                        $action_params = $col_action->getParameters();
                    }
                    else
                    {
                        $action_params = null;
                    }
                    
                    $output_row[] = $column->getLabel();
                    
                    if (isset($_GET['order']))
                    {
                        if ($_GET['order'] == $name || (isset($action_params['order']) && $action_params['order'] == $_GET['order']))
                        {
                            if (isset($_GET['direction']) AND $_GET['direction'] == 'asc')
                            {
                                $label .= '<span class="fa fa-chevron-down blue" aria-hidden="true"></span>';
                            }
                            else
                            {
                                $label .= '<span class="fa fa-chevron-up blue" aria-hidden="true"></span>';
                            }
                        }
                    }
                    // add a cell with the columns label
                    $cell = new TElement('th');
                    $row->add($cell);
                    $cell->add($label);
                    
                    $cell->{'class'} = 'tdatagrid_col';
                    $cell->{'style'} = "text-align:$align;user-select:none";
                    
                    if ($props)
                    {
                        $cell->setProperties($props);
                    }
                    
                    if ($width)
                    {
                        $cell->{'width'} = (strpos($width, '%') !== false || strpos($width, 'px') !== false) ? $width : ($width + 8).'px';
                    }
                    
                    // verify if the column has an attached action
                    if ($column->getAction())
                    {
                        $action = $column->getAction();
                        if (isset($_GET['direction']) AND $_GET['direction'] == 'asc' AND isset($_GET['order']) AND ($_GET['order'] == $name || (isset($action_params['order']) && $action_params['order'] == $_GET['order'])) )
                        {
                            $action->setParameter('direction', 'desc');
                        }
                        else
                        {
                            $action->setParameter('direction', 'asc');
                        }
                        $url    = $action->serialize();
                        $cell->{'href'}        = htmlspecialchars($url);
                        $cell->{'style'}      .= ";cursor:pointer;";
                        $cell->{'generator'}   = 'adianti';
                    }
                }
                
                $this->outputData[] = $output_row;
            }
            
            if ($this->actionSide == 'right')
            {
                $this->createHeaderActionCells($row);
            }
        }
        
        // add one row to the DataGrid
        $this->tbody = new TElement('tbody');
        $this->tbody->{'class'} = 'tdatagrid_body';
        if ($this->scrollable)
        {
            $this->tbody->{'style'} = "height: {$this->height}; display: block; overflow-y:scroll; overflow-x:hidden;";
        }
        parent::add($this->tbody);
        
        $this->modelCreated = TRUE;
    }
    
    /**
     * Return thead
     */
    public function getHead()
    {
        return $this->thead;
    }
    
    /**
     * Return tbody
     */
    public function getBody()
    {
        return $this->tbody;
    }
    
    /**
     * Prepend row
     */
    public function prependRow($row)
    {
        $this->getBody()->add($row);
        $this->getHead()->{'noborder'} = '1';
        $this->prependRows ++;
    }
    
    /**
     * insert content
     */
    public function insert($position, $content)
    {
        $this->tbody->insert($position, $content);
    }
    
    /**
     * Add objects to the DataGrid
     * @param $objects An array of Objects
     */
    public function addItems($objects)
    {
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $this->addItem($object);
            }
        }
    }
    
    /**
     * Create item actions
     * @param $row DOM Row
     * @param $object Data Object
     */
    private function createItemActions($row, $object)
    {
        $first_url = null;
        
        if ($this->actions)
        {
            // iterate the actions
            foreach ($this->actions as $action_template)
            {
                // validate, clone, and inject object parameters
                $action = $action_template->prepare($object);
                
                // get the action properties
                $label     = $action->getLabel();
                $image     = $action->getImage();
                $condition = $action->getDisplayCondition();
                
                if (empty($condition) OR call_user_func($condition, $object))
                {
                    $url       = $action->serialize(TRUE, TRUE);
                    $first_url = isset($first_url) ? $first_url : $url;
                    
                    // creates a link
                    $link = new TElement('a');
                    $link->{'href'}      = htmlspecialchars($url);
                    $link->{'generator'} = 'adianti';
                    $link->{'title'} = $label;
                    
                    if ($url == '#disabled')
                    {
                        $link->{'disabled'} = '1';
                    }
                    
                    // verify if the link will have an icon or a label
                    if ($image)
                    {
                        $image_tag = is_object($image) ? clone $image : new TImage($image);
                        
                        if ($action->getUseButton())
                        {
                            // add the label to the link
                            $span = new TElement('span');
                            $span->{'class'} = $action->getButtonClass() ? $action->getButtonClass() : 'btn btn-default';
                            $span->add($image_tag);
                            $span->add($label);
                            $link->add($span);
                            
                            $link->{'role'} = 'button';
                        }
                        else
                        {
                            $link->add( $image_tag );
                        }
                    }
                    else
                    {
                        // add the label to the link
                        $span = new TElement('span');
                        $span->{'class'} = $action->getButtonClass() ? $action->getButtonClass() : 'btn btn-default';
                        $span->add($label);
                        $link->add($span);
                    }
                }
                else
                {
                    $link = '';
                }
                
                // add the cell to the row
                $cell = new TElement('td');
                $row->add($cell);
                $cell->add($link);
                $cell->{'style'} = 'min-width:'. $this->actionWidth;
                $cell->{'class'} = 'tdatagrid_cell action';
            }
        }
        
        if ($this->action_groups)
        {
            foreach ($this->action_groups as $action_group)
            {
                $actions    = $action_group->getActions();
                $headers    = $action_group->getHeaders();
                $separators = $action_group->getSeparators();
                
                if ($actions)
                {
                    $dropdown = new TDropDown($action_group->getLabel(), $action_group->getIcon());
                    $last_index = 0;
                    foreach ($actions as $index => $action_template)
                    {
                        $action = $action_template->prepare($object);
                        
                        // add intermediate headers and separators
                        for ($n=$last_index; $n<$index; $n++)
                        {
                            if (isset($headers[$n]))
                            {
                                $dropdown->addHeader($headers[$n]);
                            }
                            if (isset($separators[$n]))
                            {
                                $dropdown->addSeparator();
                            }
                        }
                        
                        // get the action properties
                        $label  = $action->getLabel();
                        $image  = $action->getImage();
                        $condition = $action->getDisplayCondition();
                        
                        if (empty($condition) OR call_user_func($condition, $object))
                        {
                            $url       = $action->serialize(TRUE, TRUE);
                            $first_url = isset($first_url) ? $first_url : $url;
                            
                            if ($url !== '#disabled')
                            {
                                $dropdown->addAction($label, $action, $image);
                            }
                        }
                        $last_index = $index;
                    }
                    // add the cell to the row
                    $cell = new TElement('td');
                    $row->add($cell);
                    $cell->add($dropdown);
                    $cell->{'class'} = 'tdatagrid_cell action';
                }
            }
        }
        
        return $first_url;
    }
    
    /**
     * Add an object to the DataGrid
     * @param $object An Active Record Object
     */
    public function addItem($object)
    {
        if ($this->modelCreated)
        {
            $valueGroup = null;

            if ($this->groupColumn AND
                (is_null($this->groupContent) OR $this->groupContent !== $object->{$this->groupColumn} ) )
            {

                if ($this->groupMask)
                {
                    $valueGroup = AdiantiTemplateHandler::replace($this->groupMask, $object);
                }
                else if ($this->groupTransformer)
                {
                    $valueGroup = call_user_func($this->groupTransformer, $object->{$this->groupColumn}, $object, $this);
                }
                else
                {
                    $valueGroup = $object->{$this->groupColumn};
                }

                if (! is_null($this->groupContent) && $this->groupTotal)
                {
                    $this->processGroupTotals($this->groupContent);
                }

                $row = new TElement('tr');
                $row->{'class'} = 'tdatagrid_group';
                $row->{'level'} = ++ $this->groupCount;
                $this->groupRowCount = 0;
                if ($this->isScrollable() AND $this->hasCustomWidth())
                {
                    $row->{'style'} = 'display: inline-table; width: 100%';
                }
                $this->tbody->add($row);
                $cell = new TElement('td');
                $cell->colspan = count($this->actions)+count($this->action_groups)+count($this->columns);
                $row->add($cell);

                $cell->add($valueGroup);
                
                $this->groupContent = $object->{$this->groupColumn};
            }
            
            // define the background color for that line
            $classname = ($this->rowcount % 2) == 0 ? 'tdatagrid_row_even' : 'tdatagrid_row_odd';
            
            $row = new TElement('tr');
            $this->tbody->add($row);
            $row->{'class'} = $classname;
            
            if ($this->isScrollable() AND $this->hasCustomWidth())
            {
                $row->{'style'} = 'display: inline-table; width: 100%';
            }
            
            if ($this->groupColumn)
            {
                if (empty($this->objectsGroup[$this->groupContent]))
                {
                    $this->objectsGroup[$this->groupContent] = array();
                }

                $this->objectsGroup[$this->groupContent][] = $object;

                $this->groupRowCount ++;
                $row->{'childof'} = $this->groupCount;
                $row->{'level'}   = $this->groupCount . '.'. $this->groupRowCount;
            }
            
            if ($this->actionSide == 'left')
            {
                $first_url = $this->createItemActions( $row, $object );
            }
            
            $output_row = [];
            $used_hidden = [];
            
            if ($this->columns)
            {
                // iterate the DataGrid columns
                foreach ($this->columns as $column)
                {
                    // get the column properties
                    $name     = $column->getName();
                    $align    = $column->getAlign();
                    $width    = $column->getWidth();
                    $function = $column->getTransformer();
                    $props    = $column->getDataProperties();
                    
                    // calculated column
                    if (substr($name,0,1) == '=')
                    {
                        $content = AdiantiTemplateHandler::replace($name, $object, 'float');
                        $content = AdiantiTemplateHandler::evaluateExpression(substr($content,1));
                        $object->$name = $content;
                    }
                    else
                    {
                        try
                        {
                            @$content  = $object->$name; // fire magic methods
                            
                            if (is_null($content))
                            {
                                $content = AdiantiTemplateHandler::replace($name, $object);
                                
                                if ($content === $name)
                                {
                                    $content = '';
                                }
                            }
                        }
                        catch (Exception $e)
                        {
                            $content = AdiantiTemplateHandler::replace($name, $object);
                            
                            if (empty(trim($content)) OR $content === $name)
                            {
                                $content = $e->getMessage();
                            }
                        }
                    }
                    
                    if (isset($this->columnValues[$name]))
                    {
                        $this->columnValues[$name][] = $content;
                    }
                    else
                    {
                        $this->columnValues[$name] = [$content];
                    }

                    if (isset($this->columnValuesGroup[$this->groupContent][$name]))
                    {
                        $this->columnValuesGroup[$this->groupContent][$name][] = $content;
                    }
                    else
                    {
                        $this->columnValuesGroup[$this->groupContent][$name] = [$content];
                    }
                    
                    $data = is_null($content) ? '' : $content;
                    $raw_data = $data;
                    
                    if ( ($this->HTMLOutputConversion && $column->hasHtmlConversionEnabled()) && is_scalar($data))
                    {
                        $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');   // TAG value
                    }
                    
                    $cell = new TElement('td');
                    
                    // verify if there's a transformer function
                    if ($function)
                    {
                        $last_row = isset($this->objects[ $this->rowcount -1 ])? $this->objects[ $this->rowcount -1 ] : null;
                        // apply the transformer functions over the data
                        $data = call_user_func($function, $raw_data, $object, $row, $cell, $last_row, $this->forPrinting);
                    }
                    
                    $output_row[] = is_scalar($data) ? strip_tags($data) : '';
                    
                    if ($editaction = $column->getEditAction())
                    {
                        $editaction_field = $editaction->getField();
                        $div = new TElement('div');
                        $div->{'class'}  = 'inlineediting';
                        $div->{'style'}  = 'padding-left:5px;padding-right:5px';
                        $div->{'action'} = $editaction->serialize();
                        $div->{'field'}  = $name;
                        $div->{'key'}    = isset($object->{$editaction_field}) ? $object->{$editaction_field} : NULL;
                        $div->{'pkey'}   = $editaction_field;
                        $div->add($data);
                        
                        $this->hasInlineEditing = true;
                        
                        $row->add($cell);
                        $cell->add($div);
                        $cell->{'class'} = 'tdatagrid_cell';
                    }
                    else
                    {
                        // add the cell to the row
                        $row->add($cell);
                        $cell->add($data);
                        
                        if ($this->hiddenFields AND !isset($used_hidden[$name]))
                        {
                            $hidden = new THidden($this->id . '_' . $name.'[]');
                            $hidden->{'data-hidden-field'} = 'true';
                            $hidden->setValue($raw_data);
                            $cell->add($hidden);
                            $used_hidden[$name] = true;
                        }
                        
                        $cell->{'class'} = 'tdatagrid_cell';
                        $cell->{'align'} = $align;
                        
                        if (isset($first_url) && $this->defaultClick && empty($cell->{'href'}) && !empty($first_url) && ($first_url !== '#disabled'))
                        {
                            $cell->{'href'}      = $first_url;
                            $cell->{'generator'} = 'adianti';
                            $cell->{'class'}     = 'tdatagrid_cell';
                        }
                    }
                    
                    if ($props)
                    {
                        $cell->setProperties($props);
                    }
                    
                    if ($width)
                    {
                        $cell->{'width'} = (strpos($width, '%') !== false || strpos($width, 'px') !== false) ? $width : ($width + 8).'px';
                    }
                }
                
                $this->outputData[] = $output_row;
            }
            
            if ($this->actionSide == 'right')
            {
                $this->createItemActions( $row, $object );
            }
            
            if ($this->popover && (empty($this->popcondition) OR call_user_func($this->popcondition, $object)))
            {
                $poptitle   = $this->poptitle;
                $popcontent = $this->popcontent;
                $poptitle   = AdiantiTemplateHandler::replace($poptitle, $object);
                $popcontent = AdiantiTemplateHandler::replace($popcontent, $object, null, true);
                
                $row->{'data-popover'} = 'true';
                $row->{'poptitle'} = $poptitle;
                $row->{'popcontent'} = htmlspecialchars(str_replace("\n", '', nl2br($popcontent)));
                
                if ($this->popside)
                {
                    $row->{'popside'} = $this->popside;
                }
            }
            
            if (count($this->searchAttributes) > 0)
            {
                $row->{'id'} = 'row_' . mt_rand(1000000000, 1999999999);
                
                foreach ($this->searchAttributes as $search_att)
                {
                    @$search_content = $object->$search_att; // fire magic methods
                    if (!empty($search_content))
                    {
                        $row_dom_search_att = 'search_' . str_replace(['-', '>'],['_', ''],$search_att);
                        $row->$row_dom_search_att = $search_content;
                    }
                }
            }
            
            $this->objects[ $this->rowcount ] = $object;
            
            // increments the row counter
            $this->rowcount ++;
            
            return $row;
        }
        else
        {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before ^2', 'createModel', __METHOD__ ) );
        }
    }
    
    /**
     * Append table row via Javascript
     */
    public static function appendRow( $table_id, $row )
    {
        $row64 = base64_encode($row->getContents());
        TScript::create("ttable_add_row('{$table_id}', 'body', '{$row64}')");
    }
    
    /**
     * Remove row by id
     */
    public static function removeRowById( $table_id, $id)
    {
        TScript::create("ttable_remove_row_by_id('{$table_id}', '{$id}')");
    }
    
    /**
     * Replace row by id
     */
    public static function replaceRowById( $table_id, $id, $row)
    {
        $row64 = base64_encode($row->getContents());
        TScript::create("ttable_replace_row_by_id('{$table_id}', '{$id}', '{$row64}')");
    }
    
    /**
     * Return datagrid items
     */
    public function getItems()
    {
        return $this->objects;
    }
    
    private function processGroupTotals($valueGroup)
    {  
        $row = new TElement('tr');
        
        if ($this->isScrollable() AND $this->hasCustomWidth())
        {
            $row->{'style'} = 'display: inline-table; width: 100%';
        }
        
        if ($this->actionSide == 'left')
        {
            if ($this->actions)
            {
                // iterate the actions
                foreach ($this->actions as $action)
                {
                    $cell = new TElement('td');
                    $row->add($cell);
                }
            }
            
            if ($this->action_groups)
            {
                foreach ($this->action_groups as $action_group)
                {
                    $cell = new TElement('td');
                    $row->add($cell);
                }
            }
        }
        
        if ($this->columns)
        {
            // iterate the DataGrid columns
            foreach ($this->columns as $column)
            {
                $cell = new TElement('td');
                $row->add($cell);
                
                // get the column total function
                $totalFunction = $column->getTotalFunction();
                $totalMask     = $column->getTotalMask();
                $totalCallback = $column->getTotalCallback();
                $transformer   = $column->getTransformer();
                $name          = $column->getName();
                $align         = $column->getAlign();
                $width         = $column->getWidth();
                $props         = $column->getDataProperties();
                $cell->{'style'} = "text-align:$align";
                
                if ($width)
                {
                    $cell->{'width'} = (strpos($width, '%') !== false || strpos($width, 'px') !== false) ? $width : ($width + 8).'px';
                }
                
                if ($props)
                {
                    $cell->setProperties($props);
                }
                
                if ($totalCallback)
                {
                    $raw_content = 0;
                    $content     = 0;
                    
                    if (count($this->objectsGroup[$valueGroup]) > 0)
                    {
                        $raw_content = $totalCallback($this->columnValuesGroup[$valueGroup][$name], $this->objectsGroup[$valueGroup]);
                        $content     = $raw_content;
                        
                        if ($transformer && $column->totalTransformed())
                        {
                            // apply the transformer functions over the data
                            // $content = call_user_func($transformer, $content, null, null, null, null);
                        }
                    }
                    
                    if (!empty($totalFunction) || !empty($totalCallback))
                    {
                        $this->hasTotalFunction = true;
                        $cell->{'data-total-function'} = $totalFunction;
                        $cell->{'data-column-name'}    = $name;
                        $cell->{'data-total-mask'}     = $totalMask;
                        $cell->{'data-value'}          = $raw_content;
                    }
                    
                    $cell->add($content);
                }
                else
                {
                    $cell->add('&nbsp;');
                }
            }
        }

        $this->tbody->add($row);
    }

    /**
     * Process column totals
     */
    private function processTotals()
    {
        if ($this->groupColumn && $this->groupTotal)
        {
            $this->processGroupTotals($this->groupContent);
        }

        $has_total = false;
        
        $this->tfoot = new TElement('tfoot');
        $this->tfoot->{'class'} = 'tdatagrid_footer';
        
        if ($this->scrollable)
        {
            $this->tfoot->{'style'} = "display: block";
            $this->tfoot->{'style'} = "display: block; padding-right: 15px";
        }
        
        $row = new TElement('tr');
        
        if ($this->isScrollable() AND $this->hasCustomWidth())
        {
            $row->{'style'} = 'display: inline-table; width: 100%';
        }
        $this->tfoot->add($row);
        
        if ($this->actionSide == 'left')
        {
            if ($this->actions)
            {
                // iterate the actions
                foreach ($this->actions as $action)
                {
                    $cell = new TElement('td');
                    $row->add($cell);
                }
            }
            
            if ($this->action_groups)
            {
                foreach ($this->action_groups as $action_group)
                {
                    $cell = new TElement('td');
                    $row->add($cell);
                }
            }
        }
        
        if ($this->columns)
        {
            // iterate the DataGrid columns
            foreach ($this->columns as $column)
            {
                $cell = new TElement('td');
                $row->add($cell);
                
                // get the column total function
                $totalFunction = $column->getTotalFunction();
                $totalMask     = $column->getTotalMask();
                $totalCallback = $column->getTotalCallback();
                $transformer   = $column->getTransformer();
                $name          = $column->getName();
                $align         = $column->getAlign();
                $width         = $column->getWidth();
                $props         = $column->getDataProperties();
                $cell->{'style'} = "text-align:$align";
                
                if ($width)
                {
                    $cell->{'width'} = (strpos($width, '%') !== false || strpos($width, 'px') !== false) ? $width : ($width + 8).'px';
                }
                
                if ($props)
                {
                    $cell->setProperties($props);
                }
                
                if ($totalCallback)
                {
                    $raw_content = 0;
                    $content     = 0;
                    
                    if (count($this->objects) > 0)
                    {
                        $raw_content = $totalCallback($this->columnValues[$name], $this->objects);
                        $content     = $raw_content;
                        
                        if ($transformer && $column->totalTransformed())
                        {
                            // apply the transformer functions over the data
                            $content = call_user_func($transformer, $content, null, null, null, null, null);
                        }
                    }
                    
                    if (!empty($totalFunction) || !empty($totalCallback))
                    {
                        $this->hasTotalFunction = true;
                        $cell->{'data-total-function'} = $totalFunction;
                        $cell->{'data-column-name'}    = $name;
                        $cell->{'data-total-mask'}     = $totalMask;
                        $cell->{'data-value'}          = $raw_content;
                    }
                    $cell->add($content);
                }
                else
                {
                    $cell->add('&nbsp;');
                }
            }
        }
        
        if ($this->hasTotalFunction)
        {
            parent::add($this->tfoot);
        }
    }
    
    /**
     * Find the row index by object attribute
     * @param $attribute Object attribute
     * @param $value Object value
     */
    public function getRowIndex($attribute, $value)
    {
        foreach ($this->objects as $pos => $object)
        {
            if ($object->$attribute == $value)
            {
                return $pos;
            }
        }
        return NULL; 
    }
    
    /**
     * Return the row by position
     * @param $position Row position
     */
    public function getRow($position)
    {
        return $this->tbody->get($position);
    }
    
    /**
     * Returns the DataGrid's width
     * @return An integer containing the DataGrid's width
     */
    public function getWidth()
    {
        $width=0;
        if ($this->actions)
        {
            // iterate the DataGrid Actions
            foreach ($this->actions as $action)
            {
                $width += 22;
            }
        }
        
        if ($this->columns)
        {
            // iterate the DataGrid Columns
            foreach ($this->columns as $column)
            {
                if (is_numeric($column->getWidth()))
                {
                    $width += $column->getWidth();
                }
            }
        }
        return $width;
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
     * Set serach attributes
     */
    public function setSearchAttributes($attributes)
    {
        $this->searchAttributes = $attributes;
    }
    
    /**
     * Enable fuse search
     * @param $input Field input for search
     * @param $attribute Attribute name
     */
    public function enableSearch(TField $input, $attributes) 
    {
        if (count($this->objects)>0)
        {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before ^2', 'enableSearch()', 'addItem()'));
        }
        
        $input_id    = $input->getId();
        $datagrid_id = $this->{'id'};
        $att_names   = explode(',', $attributes);
        $dom_atts    = [];
        
        if ($att_names)
        {
            foreach ($att_names as $att_name)
            {
                $att_name = trim($att_name);
                $this->searchAttributes[] = $att_name;
                $dom_search_atts[] = str_replace(['-', '>'], ['_', ''], "search_{$att_name}");
            }
            
            $dom_att_string = implode(',', $dom_search_atts);
            TScript::create("__adianti_input_fuse_search('#{$input_id}', '{$dom_att_string}', '#{$datagrid_id} tr')");
        }
    }
    
    /**
     * Shows the DataGrid
     */
    public function show()
    {
        $this->processTotals();
        
        if (!$this->hasCustomWidth())
        {
            $this->{'style'} .= ';width:unset';
        }
        
        // shows the datagrid
        parent::show();
        
        $params = $_REQUEST;
        unset($params['class']);
        unset($params['method']);
        // to keep browsing parameters (order, page, first_page, ...)
        $urlparams='&'.http_build_query($params);
        
        // inline editing treatment
        if ($this->hasInlineEditing)
        {
            TScript::create(" tdatagrid_inlineedit( '{$urlparams}' );");
        }
        
        if ($this->groupColumn)
        {
            TScript::create(" tdatagrid_enable_groups();");
        }
        
        if ($this->hasTotalFunction)
        {
            TScript::create(" tdatagrid_update_total('#{$this->{'id'}}');");
        }
        
        if ($this->mutationAction)
        {
            $url = $this->mutationAction->serialize(false);
            TScript::create(" tdatagrid_mutation_action('#{$this->{'id'}}', '$url');");
        }
    }
}
