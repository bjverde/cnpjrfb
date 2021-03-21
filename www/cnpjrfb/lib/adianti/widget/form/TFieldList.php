<?php
namespace Adianti\Widget\Form;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TAction;
use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Container\TTable;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Util\TImage;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Exception;
use stdClass;

/**
 * Create a field list
 *
 * @version    7.3
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TFieldList extends TTable
{
    private $fields;
    private $labels;
    private $body_created;
    private $detail_row;
    private $remove_function;
    private $remove_action;
    private $clone_function;
    private $sort_action;
    private $sorting;
    private $fields_properties;
    private $row_functions;
    private $row_actions;
    private $automatic_aria;
    private $summarize;
    private $totals;
    private $total_functions;
    private $remove_enabled;
    private $remove_icon;
    private $remove_title;
    private $field_prefix;
    private $thead;
    private $tfoot;
    private $tbody;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->{'id'}     = 'tfieldlist_' . mt_rand(1000000000, 1999999999);
        $this->{'class'}  = 'tfieldlist';
        
        $this->fields = [];
        $this->fields_properties = [];
        $this->row_functions = [];
        $this->row_actions = [];
        $this->body_created = false;
        $this->detail_row = 0;
        $this->sorting = false;
        $this->automatic_aria = false;
        $this->remove_function = 'ttable_remove_row(this)';
        $this->clone_function  = 'ttable_clone_previous_row(this)';
        $this->summarize = false;
        $this->total_functions = null;
        $this->remove_enabled = true;
    }
    
    /**
     * Get post data as object list 
     */
    public function getPostData()
    {
        $data = [];
        
        foreach($this->fields as $field)
        {
            $field_name = $field->getName();
            $name  = str_replace( ['[', ']'], ['', ''], $field->getName());
            
            $data[$name] = $field->getPostData();
        }
        
        $results = [];
        
        foreach ($data as $name => $values)
        {
            $field_name = $name;
            
            if (!empty($this->field_prefix))
            {
                $field_name = str_replace($this->field_prefix . '_', '', $field_name);
            }
            
            foreach ($values as $row => $value)
            {
                $results[$row] = $results[$row] ?? new stdClass;
                $results[$row]->$field_name = $value;
            }
        }
        
        return $results;
    }
    
    /**
     * Get post row count
     */
    public function getRowCount($field_name = null)
    {
        if (count($this->fields) > 0)
        {
            if (isset($this->fields[$field_name]))
            {
                $field = $this->fields[$field_name];
            }
            else if (isset($this->fields[$field_name.'[]']))
            {
                $field = $this->fields[$field_name.'[]'];
            }
            else
            {
                $field = array_values($this->fields)[0];
            }
            
            return count(array_filter($field->getPostData(), function($value){
                return $value !== '';
            }));
        }
        
        return 0;
    }
    
    /**
     * Disable remove button
     */
    public function disableRemoveButton()
    {
        $this->remove_enabled = false;
    }
    
    /**
     * Enable sorting
     */
    public function enableSorting()
    {
        $this->sorting = true;
    }
    
    /**
     * Generate automatic aria-labels
     */
    public function generateAria()
    {
        $this->automatic_aria = true;
    }
    
    /**
     * Define the action to be executed when the user sort rows
     * @param $action TAction object
     */
    public function setSortAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->sort_action = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
    }
    
    /**
     * Set the remove javascript action
     */
    public function setRemoveFunction($action, $icon = null, $title = null)
    {
        $this->remove_function = $action;
        $this->remove_icon     = $icon;
        $this->remove_title    = $title;
    }
    
    /**
     * Set the remove action
     */
    public function setRemoveAction(TAction $action = null, $icon = null, $title = null)
    {
        if ($action)
        {
            if ($action->isStatic())
            {
                $this->remove_action = $action;
            }
            else
            {
                $string_action = $action->toString();
                throw new Exception(AdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
            }
        }
        
        $this->remove_icon  = $icon;
        $this->remove_title = $title;
    }
    
    /**
     * Set the clone javascript action
     */
    public function setCloneFunction($action)
    {
        $this->clone_function = $action;
    }
    
    /**
     * Add function
     */
    public function addButtonFunction($function, $icon, $title)
    {
        $this->row_functions[] = [$function, $icon, $title];
    }
    
    /**
     * Add action
     */
    public function addButtonAction(TAction $action, $icon, $title)
    {
        $this->row_actions[] = [$action, $icon, $title];
    }
    
    /**
     * Set field prefix
     */
    public function setFieldPrefix($prefix)
    {
        $this->field_prefix = $prefix;
    }
    
    /**
     * Get field prefix
     */
    public function getFieldPrefix()
    {
        return $this->field_prefix;
    }
    
    /**
     * Add a field
     * @param $label  Field Label
     * @param $object Field Object
     */
    public function addField($label, AdiantiWidgetInterface $field, $properties = null)
    {
        if ($field instanceof TField)
        {
            $name = $field->getName();
            
            if (!empty($this->field_prefix) && strpos($name, $this->field_prefix) === false)
            {
                $name = $this->field_prefix . '_' . $name;
                $field->setName($name);
            }
            
            if (isset($this->fields[$name]) AND substr($name,-2) !== '[]')
            {
                throw new Exception(AdiantiCoreTranslator::translate('You have already added a field called "^1" inside the form', $name));
            }
            
            if ($name)
            {
                $this->fields[$name] = $field;
                $this->fields_properties[$name] = $properties;
            }
            
            if (isset($properties['sum']) && $properties['sum'] == true)
            {
                $this->summarize = true;
            }
            
            if (isset($properties['uniqid']) && $properties['uniqid'] == true)
            {
                $field->{'uniqid'} = 'true';
            }
            
            if ($label instanceof TLabel)
            {
                $label_field = $label;
                $label_value = $label->getValue();
            }
            else
            {
                $label_field = new TLabel($label);
                $label_value = $label;
            }
            
            $field->setLabel($label_value);
            $this->labels[$name] = $label_field;
        }
    }
    
    /**
     * Add table header
     */
    public function addHeader()
    {
        $this->thead = $section = parent::addSection('thead');
        
        if ($this->fields)
        {
            $row = parent::addRow();
            
            if ($this->sorting)
            {
                $row->addCell( '' );
            }
            
            foreach ($this->fields as $name => $field)
            {
                if ($field instanceof THidden)
                {
                    $cell = $row->addCell( '' );
                    $cell->{'style'} = 'display:none';
                }
                else
                {
                    $cell = $row->addCell( $this->labels[ $field->getName()] );
                    
                    if (!empty($this->fields_properties[$name]))
                    {
                        foreach ($this->fields_properties[$name] as $property => $value)
                        {
                            $cell->setProperty($property, $value);
                        }
                    }
                }
            }
            
            $all_actions = array_merge( (array) $this->row_functions, (array) $this->row_actions );
            
            if ($all_actions)
            {
                foreach ($all_actions as $row_action)
                {
                    $cell = $row->addCell( '' );
                    $cell->{'style'} = 'display:none';
                }
            }
            
            if ($this->remove_enabled)
            {
                // aligned with remove button
                $cell = $row->addCell( '' );
                $cell->{'style'} = 'display:none';
            }
        }
        
        return $section;
    }
    
    /**
     * Add detail row
     * @param $item Data object
     */
    public function addDetail( $item )
    {
        $uniqid = mt_rand(1000000, 9999999);
        
        if (!$this->body_created)
        {
            $this->tbody = parent::addSection('tbody');
            $this->body_created = true;
        }
        
        if ($this->fields)
        {
            $row = parent::addRow();
            $row->{'id'} = $uniqid;
            
            if ($this->sorting)
            {
                $move = new TImage('fas:arrows-alt gray');
                $move->{'class'} .= ' handle';
                $move->{'style'} .= ';font-size:100%;cursor:move';
                $row->addCell( $move );
            }
            
            foreach ($this->fields as $field)
            {
                $field_name = $field->getName();
                $name  = str_replace( ['[', ']'], ['', ''], $field->getName());
                
                if ($this->detail_row == 0)
                {
                    $clone = $field;
                }
                else
                {
                    $clone = clone $field;
                }
                
                if (isset($this->fields_properties[$field_name]['sum']) && $this->fields_properties[$field_name]['sum'] == true)
                {
                    $field->{'exitaction'} = "tfieldlist_update_sum('{$name}', 'callback')";
                    $field->{'onBlur'}     = "tfieldlist_update_sum('{$name}', 'callback')";
                    
                    $this->total_functions .= $field->{'exitaction'} . ';';
                    
                    $value = isset($item->$name) ? $item->$name : 0;
                    
                    if (isset($field->{'data-nmask'}))
                    {
                        $dec_sep = substr($field->{'data-nmask'},1,1);
                        $tho_sep = substr($field->{'data-nmask'},2,1);
                        
                        if ( (strpos($value, $tho_sep) !== false) && (strpos($value, $dec_sep) !== false) )
                        {
                            $value   = str_replace($tho_sep, '', $value);
                            $value   = str_replace($dec_sep, '.', $value);
                        }
                    }
                    
                    if (isset($this->totals[$name]))
                    {
                        $this->totals[$name] += $value;
                    }
                    else
                    {
                        $this->totals[$name] = $value;
                    }
                }
                
                if ($this->automatic_aria)
                {
                    $label = $this->labels[ $field->getName() ];
                    $aria_label = $label->getValue();
                    $field->{'aria-label'} = $aria_label;
                }
                
                $clone->setId($name.'_'.$uniqid);
                $clone->{'data-row'} = $this->detail_row;
                
                $cell = $row->addCell( $clone );
                $cell->{'class'} = 'field';
                
                if (!empty($this->fields_properties[$field_name]))
                {
                    foreach ($this->fields_properties[$field_name] as $property => $value)
                    {
                        $cell->setProperty($property, $value);
                    }
                }
                
                if ($clone instanceof THidden)
                {
                    $cell->{'style'} = 'display:none';
                }
                
                if (!empty($item->$name) OR (isset($item->$name) AND $item->$name == '0'))
                {
                    $clone->setValue( $item->$name );
                }
                else
                {
                    if ($field->{'uniqid'} == true)
                    {
                        $clone->setValue( mt_rand(1000000000, 1999999999) );
                    }
                    else
                    {
                        $clone->setValue( null );
                    }
                }
            }
            
            if ($this->row_actions)
            {
                foreach ($this->row_actions as $row_action)
                {
                    $string_action = $row_action[0]->serialize(FALSE);
                    
                    $btn = new TElement('div');
                    $btn->{'class'} = 'btn btn-default btn-sm';
                    $btn->{'onclick'} = "__adianti_post_exec('{$string_action}', tfieldlist_get_row_data(this), null, undefined, '1')";
                    $btn->{'title'} = $row_action[2];
                    $btn->add(new TImage($row_action[1]));
                    $row->addCell( $btn );
                }
            }
            
            if ($this->row_functions)
            {
                foreach ($this->row_functions as $row_function)
                {
                    $btn = new TElement('div');
                    $btn->{'class'} = 'btn btn-default btn-sm';
                    $btn->{'onclick'} = $row_function[0];
                    $btn->{'title'} = $row_function[2];
                    $btn->add(new TImage($row_function[1]));
                    $row->addCell( $btn );
                }
            }
            
            if ($this->remove_enabled)
            {
                $del = new TElement('div');
                $del->{'class'} = 'btn btn-default btn-sm';
                $del->{'onclick'} = $this->total_functions . $this->remove_function;
                
                if (isset($this->remove_action))
                {
                    $string_action = $this->remove_action->serialize(FALSE);
                    $del->{'onclick'} .= ";__adianti_post_exec('{$string_action}', tfieldlist_get_row_data(this), null, undefined, '1')";
                }
                
                $del->{'title'} = $this->remove_title ? $this->remove_title : AdiantiCoreTranslator::translate('Delete');
                $del->add($this->remove_icon ? new TImage($this->remove_icon) : '<i class="fa fa-times red"></i>');
                $row->addCell( $del );
            }
        }
        
        $this->detail_row ++;
        
        return $row;
    }
    
    /**
     * Add clone action
     */
    public function addCloneAction(TAction $clone_action = null, $icon = null, $title = null)
    {
        if (!$this->body_created)
        {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before ^2', 'addDetail', 'addCloneAction'));
        }
        
        $this->tfoot = parent::addSection('tfoot');
        
        $row = parent::addRow();
        
        if ($this->sorting)
        {
            $row->addCell( '' );
        }
        
        if ($this->fields)
        {
            foreach ($this->fields as $field)
            {
                $field_name = $field->getName();
                
                $cell = $row->addCell('');
                if ($field instanceof THidden)
                {
                    $cell->{'style'} = 'display:none';
                }
                else if (isset($this->fields_properties[$field_name]['sum']) && $this->fields_properties[$field_name]['sum'] == true)
                {
                    $field_name = str_replace('[]', '', $field_name);
                    $grand_total = clone $field;
                    $grand_total->setId($field_name.'_'.mt_rand(1000000, 9999999));
                    $grand_total->setName('grandtotal_'.$field_name);
                    $grand_total->{'field_name'} = $field_name;
                    $grand_total->setEditable(FALSE);
                    $grand_total->{'style'}  .= ';font-weight:bold;border:0 !important;background:none';
                    
                    if (!empty($this->totals[$field_name]))
                    {
                        $grand_total->setValue($this->totals[$field_name]);
                    }
                    
                    $cell->add($grand_total);
                }
            }
        }
        
        $all_actions = array_merge( (array) $this->row_functions, (array) $this->row_actions );
        
        if ($all_actions)
        {
            foreach ($all_actions as $row_action)
            {
                $cell = $row->addCell('');
            }
        }
        
        $add = new TElement('div');
        $add->{'class'} = 'btn btn-default btn-sm';
        $add->{'onclick'} = $this->clone_function;
        $add->{'title'} = $title ? $title : AdiantiCoreTranslator::translate('Add');
        
        if ($clone_action)
        {
            $string_action = $clone_action->serialize(FALSE);
            $add->{'onclick'} = "__adianti_post_exec('{$string_action}', tfieldlist_get_last_row_data(this), null, undefined, '1');".$add->{'onclick'};
        }
        
        $add->add($icon ? new TImage($icon) : '<i class="fa fa-plus green"></i>');
        
        // add buttons in table
        $row->addCell($add);
    }
    
    /**
     * Clear field list
     * @param $name field list name
     */
    public static function clear($name)
    {
        TScript::create( "tfieldlist_clear('{$name}');" );
    }
    
    /**
     * Clear some field list rows
     * @param $name     field list name
     * @param $index    field list name
     * @param $quantity field list name
     */
    public static function clearRows($name, $start = 0, $length = 0)
    {
        TScript::create( "tfieldlist_clear_rows('{$name}', {$start}, {$length});" );
    }
    
    /**
     * Clear some field list rows
     * @param $name     field list name
     * @param $index    field list name
     * @param $quantity field list name
     */
    public static function addRows($name, $rows)
    {
        TScript::create( "tfieldlist_add_rows('{$name}', {$rows});" );
    }
    
    /**
     * Enable scrolling
     */
    public function makeScrollable($height)
    {
        if (empty($this->tfoot))
        {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before ^2', 'addCloneAction()', 'makeScrollable()'));
        }
        else
        {
            $this->thead->{'style'} .= ';display:block';
            $this->tbody->{'style'} .= ';display:block;overflow-y: scroll;height:'.$height.'px';
            $this->tbody->{'class'} .= ' thin-scroll';
            $this->tfoot->{'style'} .= ';display:block;float:right;margin-right:10px';
        }
    }
    
    /**
     * Show component
     */
    public function show()
    {
        parent::show();
        $id = $this->{'id'};
        
        if ($this->sorting)
        {
            if (empty($this->sort_action))
            {
                TScript::create("ttable_sortable_rows('{$id}', '.handle')");
            }
            else
            {
                if (!empty($this->fields))
                {
                    $first_field = array_values($this->fields)[0];
                    $this->sort_action->setParameter('static', '1');
                    $form_name   = $first_field->getFormName();
                    $string_action = $this->sort_action->serialize(FALSE);
                    $sort_action = "function() { __adianti_post_data('{$form_name}', '{$string_action}'); }";
                    TScript::create("ttable_sortable_rows('{$id}', '.handle', $sort_action)");
                }
            }
        }
    }
}
