<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Util\TImage;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TAction;

use Exception;

/**
 * A Sortable list
 *
 * @version    7.3
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSortList extends TField implements AdiantiWidgetInterface
{
    private $initialItems;
    private $items;
    private $valueSet;
    private $connectedTo;
    private $itemIcon;
    private $changeAction;
    private $orientation;
    private $limit;
    protected $id;
    protected $changeFunction;
    protected $width;
    protected $height;
    protected $separator;
    
    /**
     * Class Constructor
     * @param  $name widget's name
     */
    public function __construct($name)
    {
        // executes the parent class constructor
        parent::__construct($name);
        $this->id   = 'tsortlist_'.mt_rand(1000000000, 1999999999);
        
        $this->initialItems = array();
        $this->items = array();
        $this->limit = -1;
        
        // creates a <ul> tag
        $this->tag = new TElement('ul');
        $this->tag->{'class'} = 'tsortlist';
        $this->tag->{'itemname'} = $name;
    }
    
    /**
     * Define orientation
     * @param $orienatation (horizontal, vertical)
     */
    public function setOrientation($orientation)
    {
        $this->orientation = $orientation;
    }
    
    /**
     * Define limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
    
    /**
     * Define the item icon
     * @param $image Item icon
     */
    public function setItemIcon(TImage $icon)
    {
        $this->itemIcon = $icon;
    }
    
    /**
     * Define the list size
     */
    public function setSize($width, $height = NULL)
    {
        $this->width = $width;
        $this->height = $height;
    }
    
    /**
     * Define the field's separator
     * @param $sep A string containing the field's separator
     */
    public function setValueSeparator($sep)
    {
        $this->separator = $sep;
    }
    
    /**
     * Define the field's value
     * @param $value An array the field's values
     */
    public function setValue($value)
    {
        if (!empty($this->separator))
        {
            $value = explode($this->separator, $value);
        }
        
        $items = $this->initialItems;
        if (is_array($value))
        {
            $this->items = array();
            foreach ($value as $index)
            {
                if (isset($items[$index]))
                {
                    $this->items[$index] = $items[$index];
                }
                else if (isset($this->connectedTo) AND is_array($this->connectedTo))
                {
                    foreach ($this->connectedTo as $connectedList)
                    {
                        if (isset($connectedList->initialItems[$index] ) )
                        {
                            $this->items[$index] = $connectedList->initialItems[$index];
                        }
                    }
                }
            }
        	$this->valueSet = TRUE;
        }
    }
    
    /**
     * Connect to another list
     * @param $list Another TSortList
     */
    public function connectTo(TSortList $list)
    {
        $this->connectedTo[] = $list;
    }
    
    /**
     * Add items to the sort list
     * @param $items An indexed array containing the options
     */
    public function addItems($items)
    {
        if (is_array($items))
        {
            $this->initialItems += $items;
            $this->items += $items;
        }
    }
    
    /**
     * Return the sort items
     */
    public function getItems()
    {
        return $this->initialItems;
    }
    
    /**
     * Return the post data
     */
    public function getPostData()
    {
        if (isset($_POST[$this->name]))
        {
            if (empty($this->separator))
            {
                return $_POST[$this->name];
            }
            else
            {
                return implode($this->separator, $_POST[$this->name]);
            }
        }
        else
        {
            return array();
        }
    }
    
    /**
     * Define the action to be executed when the user changes the combo
     * @param $action TAction object
     */
    public function setChangeAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->changeAction = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
    }
    
    /**
     * Set change function
     */
    public function setChangeFunction($function)
    {
        $this->changeFunction = $function;
    }
    
    /**
     * Enable the field
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " tsortlist_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " tsortlist_disable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Clear the field
     */
    public static function clearField($form_name, $field)
    {
        TScript::create( " tsortlist_clear_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        $this->tag->{'id'} = $this->id;
        
        $this->setProperty('style', (strstr($this->width, '%') !== FALSE)  ? "width:{$this->width};"   : "width:{$this->width}px;",   false); //aggregate style info
        $this->setProperty('style', (strstr($this->height, '%') !== FALSE) ? "height:{$this->height};" : "height:{$this->height}px;", false); //aggregate style info
        
        if ($this->orientation == 'horizontal')
        {
            $this->tag->{'itemdisplay'} = 'inline-block';
        }
        else
        {
            $this->tag->{'itemdisplay'} = 'block';
        }
        
        if ($this->items)
        {
            $i = 1;
            // iterate the checkgroup options
            foreach ($this->items as $index => $label)
            {
                // control to reduce available options when they are present
                // in another connected list as a post value
	            if ($this->connectedTo AND is_array($this->connectedTo))
	            {
	                foreach ($this->connectedTo as $connectedList)
	                {
                        if (isset($connectedList->items[$index]) AND $connectedList->valueSet )
                        {
                            continue 2;
                        }
	                }
	            }

                // instantiates a new Item
                $item = new TElement('li');
                
                if ($this->itemIcon)
                {
                    $item->add($this->itemIcon);
                }

                $label = new TLabel($label);
                $label->{'style'} = 'width: 100%;';

                $item->add($label);
                $item->{'class'} = 'tsortlist_item btn btn-default';
                $item->{'style'} = 'display:block;';
                $item->{'id'} = "tsortlist_{$this->name}_item_{$i}_li";
                $item->{'title'} = $this->tag->title;
                
                if ($this->orientation == 'horizontal')
                {
                    $item->{'style'} = 'display:inline-block';
                }
                
                $input = new TElement('input');
                $input->{'id'}   = "tsortlist_{$this->name}_item_{$i}_li_input";
                $input->{'type'} = 'hidden';
                $input->{'name'} = $this->name . '[]';
                $input->{'value'} = $index;
                $item->add($input);
                
                $this->tag->add($item);
                $i ++;
            }
        }
        
        if (parent::getEditable())
        {
            $change_action = 'function() {}';
            if (isset($this->changeAction))
            {
                if (!TForm::getFormByName($this->formName) instanceof TForm)
                {
                    throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
                }            
                $string_action = $this->changeAction->serialize(FALSE);
                $change_action = "function() { __adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback'); }";
            }
            
            if (isset($this->changeFunction))
            {
                $change_action = "function() { $this->changeFunction }";
            }
            
            $connect = 'false';
            if ($this->connectedTo AND is_array($this->connectedTo))
            {
                foreach ($this->connectedTo as $connectedList)
                {
                    $connectIds[] =  '#'.$connectedList->getId();
                }
                $connect = implode(', ', $connectIds);
            }
            TScript::create(" tsortlist_start( '#{$this->id}', '{$connect}', $change_action, $this->limit ) ");
        }
        $this->tag->show();
    }
}
