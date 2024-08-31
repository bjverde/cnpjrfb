<?php
namespace Adianti\Widget\Wrapper;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TAction;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TCheckGroup;
use Adianti\Widget\Form\TRadioGroup;
use Adianti\Widget\Container\TTable;
use Adianti\Widget\Container\THBox;
use Adianti\Validator\TFieldValidator;
use Adianti\Validator\TRequiredValidator;

use Exception;

/**
 * Create quick forms for input data with a standard container for elements
 *
 * @version    7.6
 * @package    widget
 * @subpackage wrapper
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TQuickForm extends TForm
{
    protected $fields; // array containing the form fields
    protected $name;   // form name
    protected $actionButtons;
    protected $inputRows;
    protected $currentRow;
    protected $table;
    protected $actionsContainer;
    protected $hasAction;
    protected $fieldsByRow;
    protected $titleCell;
    protected $actionCell;
    protected $fieldPositions;
    protected $client_validation;
    
    /**
     * Class Constructor
     * @param $name Form Name
     */
    public function __construct($name = 'my_form')
    {
        parent::__construct($name);
        
        // creates a table
        $this->table = new TTable;
        $this->hasAction = FALSE;
        $this->client_validation = FALSE;
        
        $this->fieldsByRow = 1;
        
        $this->setProperty('novalidate','');
        
        // add the table to the form
        parent::add($this->table);
    }
    
    /**
     * Turn on/off client validation
     */
    public function setClientValidation($bool)
    {
        if ($bool)
        {
            $this->unsetProperty('novalidate');
        }
        else
        {
            $this->setProperty('novalidate','');
        }
    }
    
    /**
     * Returns the actions container
     */
    public function getActionsContainer()
    {
        return $this->actionsContainer;
    }
    
    /**
     * Returns the inner table
     */
    public function getTable()
    {
        return $this->table;
    }
    
    /**
     * Define the field quantity per row
     * @param $count Field count
     */
    public function setFieldsByRow($count)
    {
        if (is_int($count) AND $count >=1 AND $count <=3)
        {
            $this->fieldsByRow = $count;
            if (!empty($this->titleCell))
            {
                $this->titleCell->{'colspan'}  = 2 * $this->fieldsByRow;
            }
            if (!empty($this->actionCell))
            {
                $this->actionCell->{'colspan'} = 2 * $this->fieldsByRow;
            }
        }
        else
        {
            throw new Exception(AdiantiCoreTranslator::translate('The method (^1) just accept values of type ^2 between ^3 and ^4', __METHOD__, 'integer', 1, 3));
        }
    }
    
    /**
     * Return the fields by row count
     */
    public function getFieldsByRow()
    {
        return $this->fieldsByRow;
    }
    
    /**
     * Intercepts whenever someones assign a new property's value
     * @param $name     Property Name
     * @param $value    Property Value
     */
    public function __set($name, $value)
    {
        if ($name == 'class')
        {
            $this->table->{'width'} = '100%';
        }
        
        if (method_exists('TForm', '__set'))
        {
            parent::__set($name, $value);
        }
    }
    
    /**
     * Returns the form container
     */
    public function getContainer()
    {
        return $this->table;
    }
    
    /**
     * Add a form title
     * @param $title     Form title
     */
    public function setFormTitle($title)
    {
        // add the field to the container
        $row = $this->table->addRow();
        $row->{'class'} = 'tformtitle';
        $this->table->{'width'} = '100%';
        $this->titleCell = $row->addCell( new TLabel($title) );
        $this->titleCell->{'colspan'} = 2 * $this->fieldsByRow;
    }
    
    /**
     * Returns the input groups
     */
    public function getInputRows()
    {
        return $this->inputRows;
    }
    
    /**
     * Add a form field
     * @param $label     Field Label
     * @param $object    Field Object
     * @param $size      Field Size
     * @param $validator Field Validator
     */
    public function addQuickField($label, AdiantiWidgetInterface $object, $size = 200, TFieldValidator $validator = NULL, $label_size = NULL)
    {
        if ($size && !$object instanceof TRadioGroup && !$object instanceof TCheckGroup)
        {
            $object->setSize($size);
        }
        parent::addField($object);
        
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
        
        $object->setLabel($label_value);
        
        if ( empty($this->currentRow) OR ( $this->fieldPositions % $this->fieldsByRow ) == 0 )
        {
            // add the field to the container
            $this->currentRow = $this->table->addRow();
            $this->currentRow->{'class'} = 'tformrow';
        }
        $row = $this->currentRow;
        
        if ($validator instanceof TRequiredValidator)
        {
            $label_field->setFontColor('#FF0000');
        }
        
        if ($label_size)
        {
            $label_field->setSize($label_size);
        }
        if ($object instanceof THidden)
        {
            $row->addCell( '' );
            $row->{'style'} = 'display:none';
        }
        else
        {
            $cell = $row->addCell( $label_field );
            $cell->{'width'} = '30%';
        }
        $row->addCell( $object );
        
        if ($validator)
        {
            $object->addValidation($label_value, $validator);
        }
        
        $this->inputRows[] = array($label_field, array($object), $validator instanceof TRequiredValidator, $row);
        $this->fieldPositions ++;
        return $row;
    }
    
    /**
     * Add a form field
     * @param $label     Field Label
     * @param $objects   Array of Objects
     * @param $required  Boolean TRUE if required
     */
    public function addQuickFields($label, $objects, $required = FALSE)
    {
        if ( empty($this->currentRow) OR ( $this->fieldPositions % $this->fieldsByRow ) == 0 )
        {
            // add the field to the container
            $this->currentRow = $this->table->addRow();
            $this->currentRow->{'class'} = 'tformrow';
        }
        $row = $this->currentRow;
        
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
        
        if ($required)
        {
            $label_field->setFontColor('#FF0000');
        }
        
        $row->addCell( $label_field );
        
        $hbox = new THBox;
        foreach ($objects as $object)
        {
            parent::addField($object);
            
            if (!$object instanceof TButton)
            {
                $object->setLabel($label_value);
            }
            $hbox->add($object);
        }
        $row->addCell( $hbox );
        
        $this->fieldPositions ++;
        
        $this->inputRows[] = array($label_field, $objects, $required, $row);
        return $row;
    }
    
    /**
     * Add a form action
     * @param $label  Action Label
     * @param $action TAction Object
     * @param $icon   Action Icon
     */
    public function addQuickAction($label, TAction $action, $icon = 'fa:save')
    {
        $name   = 'btn_'.strtolower(str_replace(' ', '_', $label));
        $button = new TButton($name);
        parent::addField($button);
        
        // define the button action
        $button->setAction($action, $label);
        $button->setImage($icon);
        
        if (!$this->hasAction)
        {
            $this->actionsContainer = new THBox;
            
            $row  = $this->table->addRow();
            $row->{'class'} = 'tformaction';
            $this->actionCell = $row->addCell( $this->actionsContainer );
            $this->actionCell->{'colspan'} = 2 * $this->fieldsByRow;
        }
        
        // add cell for button
        $this->actionsContainer->add($button);
        
        $this->hasAction = TRUE;
        $this->actionButtons[] = $button;
        
        return $button;
    }
    
    /**
     * Add a form button
     * @param $label  Action Label
     * @param $action Javascript action
     * @param $icon   Action Icon
     */
    public function addQuickButton($label, $action, $icon = 'fa:save')
    {
        $name   = strtolower(str_replace(' ', '_', $label));
        $button = new TButton($name);
        parent::addField($button);
        
        // define the button action
        $button->addFunction($action);
        $button->setLabel($label);
        $button->setImage($icon);
        
        if (!$this->hasAction)
        {
            $this->actionsContainer = new THBox;
            
            $row  = $this->table->addRow();
            $row->{'class'} = 'tformaction';
            $this->actionCell = $row->addCell( $this->actionsContainer );
            $this->actionCell->{'colspan'} = 2 * $this->fieldsByRow;
        }
        
        // add cell for button
        $this->actionsContainer->add($button);
        $this->hasAction = TRUE;
        
        return $button;
    }
    
    /**
     * Clear actions row
     */
    public function delActions()
    {
        if ($this->actionsContainer)
        {
            foreach ($this->actionButtons as $key => $button)
            {
                parent::delField($button);
                unset($this->actionButtons[$key]);
            }
            $this->actionsContainer->clearChildren();
        }
    }
    
    /**
     * Return an array with action buttons
     */
    public function getActionButtons()
    {
        return $this->actionButtons;
    }
    
    /**
     * Detach action buttons
     */
    public function detachActionButtons()
    {
        $buttons = $this->getActionButtons();
        $this->delActions();
        return $buttons;
    }
    
    /**
     * Add a row
     */
    public function addRow()
    {
        return $this->table->addRow();
    }
    
    /**
     *
     */
    public static function showField($form, $field, $speed = 0)
    {
        TScript::create("tform_show_field('{$form}', '{$field}', {$speed})");
    }
    
    /**
     *
     */
    public static function hideField($form, $field, $speed = 0)
    {
        TScript::create("tform_hide_field('{$form}', '{$field}', {$speed})");
    }
}
