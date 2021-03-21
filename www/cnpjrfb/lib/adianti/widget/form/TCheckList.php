<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Base\TScript;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Database\TTransaction;
use Adianti\Database\TCriteria;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Widget\Wrapper\AdiantiDatabaseWidgetTrait;
use Adianti\Validator\TFieldValidator;

/**
 * Checklist
 *
 * @version    7.3
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TCheckList implements AdiantiWidgetInterface
{
    protected $datagrid;
    protected $idColumn;
    protected $fields;
    protected $formName;
    protected $name;
    protected $value;
    protected $validations;
    protected $checkColumn;
    protected $checkAllButton;
    protected $width;
    
    use AdiantiDatabaseWidgetTrait;
    
    /**
     * Construct method
     */
    public function __construct($name)
    {
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->{'style'} = 'width: 100%';
        $this->datagrid->{'widget'} = 'tchecklist';
        $this->datagrid->disableDefaultClick(); // important!
        
        $id = $this->datagrid->{'id'};
        
        $check = new TCheckButton('check_all_'.$id);
        $check->setIndexValue('on');
        $check->{'onclick'} = "tchecklist_select_all(this, '{$id}')";
        $check->{'style'} = 'cursor:pointer';
        $check->setProperty('class', 'filled-in');
        $this->checkAllButton = $check;
        
        $label = new TLabel('');
        $label->{'style'} = 'margin:0';
        $label->{'class'} = 'checklist-label';
        $check->after($label);
        $label->{'for'} = $check->getId();
        
        $this->checkColumn = $this->datagrid->addColumn( new TDataGridColumn('check',   $check->getContents(),   'center',  '1%') );
        
        $this->setName($name);
        $this->value = [];
        $this->fields = [];
        $this->width = '100%';
    }
    
    /**
     * Set checklist size
     */
    public function setSize($size)
    {
        $this->width = $size;
        
        if (strstr($size, '%') !== FALSE)
        {
            $this->datagrid->{'style'} .= ";width: {$size}";
        }
        else
        {
            $this->datagrid->{'style'} .= ";width: {$size}px";
        }
    }
    
    /**
     * Returns checklist size
     */
    function getSize()
    {
        return [$this->width, $this->datagrid->getHeight()];
    }
    
    /**
     * Change Id
     */
    public function setId($id)
    {
        $this->checkAllButton->{'onclick'} = "tchecklist_select_all(this, '{$id}')";
        $this->checkColumn->setLabel( $this->checkAllButton->getContents() );
        $this->datagrid->setId($id);
    }
    
    /**
     * Disable check all button
     */
    public function disableCheckAll()
    {
        $this->checkColumn->setLabel('');
    }
    
    /**
     * Define the field's name
     * @param $name   A string containing the field's name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the field's name
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Define the checklist selected ids
     * @param $value 
     */
    public function setValue($value)
    {
        $this->value = $value;
        $id_column = $this->idColumn;
        $items = $this->datagrid->getItems();
        
        if ($items)
        {
            foreach ($items as $item)
            {
                $item->{'check'}->setValue(null);
                $position = $this->datagrid->getRowIndex( $id_column, $item->$id_column );
                if (is_int($position))
                {
                    $row = $this->datagrid->getRow($position);
                    $row->{'className'} = '';
                }
                
                if ($this->value)
                {
                    if (in_array($item->$id_column, $this->value))
                    {
                        $item->{'check'}->setValue('on');
                        
                        if (is_int($position))
                        {
                            $row = $this->datagrid->getRow($position);
                            $row->{'className'} = 'selected';
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Returns the selected ids
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * Define the Identification column
     */
    public function setIdColumn($name)
    {
        $this->idColumn = $name;
    }
    
    /**
     * Add list column
     * @param  $name  = Name of the column in the database
     * @param  $label = Text label that will be shown in the header
     * @param  $align = Column align (left, center, right)
     * @param  $width = Column Width (pixels)
     */
    public function addColumn($name, $label, $align, $width)
    {
        if (empty($this->idColumn))
        {
            $this->idColumn = $name;
        }
        
        return $this->datagrid->addColumn( new TDataGridColumn($name, $label, $align, $width) );
    }
    
    /**
     * Add item
     */
    public function addItem($object)
    {
        $id_column = $this->idColumn;
        $object->{'check'} = new TCheckButton('check_' . $this->name . '_' . base64_encode($object->$id_column));
        $object->{'check'}->setIndexValue('on');
        $object->{'check'}->setProperty('class', 'filled-in');
        $object->{'check'}->{'style'} = 'cursor:pointer';
        
        $label = new TLabel('');
        $label->{'style'} = 'margin:0';
        $label->{'class'} = 'checklist-label';
        $object->{'check'}->after($label);
        $label->{'for'} = $object->{'check'}->getId();
        
        if (count($this->datagrid->getItems()) == 0)
        {
            $this->datagrid->createModel();
        }
        
        $row = $this->datagrid->addItem($object);
        
        if (in_array($object->$id_column, $this->value))
        {
            $object->{'check'}->setValue('on');
            $row->{'className'} = 'selected';
        }
        
        $this->fields[] = $object->{'check'};
        
        $form = TForm::getFormByName($this->formName);
        if ($form)
        {
            $form->addField($object->{'check'});
        }
    }
    
    /**
     * add Items
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
     * Fill with model objects
     */
    public function fillWith($database, $model, $key, $ordercolumn = NULL, TCriteria $criteria = NULL)
    {
        TTransaction::open($database);
        $this->addItems( $this->getObjectsFromModel($database, $model, $key, $ordercolumn, $criteria) );
        TTransaction::close();
    }
    
    /**
     * Clear datagrid
     */
    public function clear()
    {
        $this->datagrid->clear();
    }
    
    /**
     * Get fields
     */
    public function getFields()
    {
        return $this->fields;
    }
    
    /**
     * Define the name of the form to wich the field is attached
     * @param $name    A string containing the name of the form
     * @ignore-autocomplete on
     */
    public function setFormName($name)
    {
        $this->formName = $name;
    }
    
    /**
     * Return the name of the form to wich the field is attached
     */
    public function getFormName()
    {
        return $this->formName;
    }
    
    /**
     * Redirect calls to decorated object
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->datagrid, $method),$parameters);
    }
    
    /**
     * Get post data
     */
    public function getPostData()
    {
        $value = [];
        $items = $this->datagrid->getItems();
        
        $id_column = $this->idColumn;
        if ($items)
        {
            foreach ($items as $item)
            {
                $field_name = 'check_'.$this->name . '_' . base64_encode($item->$id_column);
                
                if (!empty($_POST[$field_name]) && $_POST[$field_name] == 'on')
                {
                    $value[] = $item->$id_column;
                }
            }
        }
        
        return $value;
    }
    
    /**
     * Add a field validator
     * @param $label Field name
     * @param $validator TFieldValidator object
     * @param $parameters Aditional parameters
     */
    public function addValidation($label, TFieldValidator $validator, $parameters = NULL)
    {
        $this->validations[] = array($label, $validator, $parameters);
    }
    
    /**
     * Returns field validations
     */
    public function getValidations()
    {
        return $this->validations;
    }
    
    /**
     * Validate a field
     */
    public function validate()
    {
        if ($this->validations)
        {
            foreach ($this->validations as $validation)
            {
                $label      = $validation[0];
                $validator  = $validation[1];
                $parameters = $validation[2];
                
                $validator->validate($label, $this->getValue(), $parameters);
            }
        }
    }
    
    /**
     * Show checklist
     */
    public function show()
    {
        if (count($this->datagrid->getItems()) == 0)
        {
            $this->datagrid->createModel();
        }
        
        $this->datagrid->show();
        
        $id = $this->datagrid->{'id'};
        TScript::create("tchecklist_row_enable_check('{$id}')");
    }
}
