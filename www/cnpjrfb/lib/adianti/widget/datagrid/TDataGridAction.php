<?php
namespace Adianti\Widget\Datagrid;

use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreTranslator;
use Exception;

/**
 * Represents an action inside a datagrid
 *
 * @version    7.6
 * @package    widget
 * @subpackage datagrid
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TDataGridAction extends TAction
{
    private $field;
    private $fields;
    private $image;
    private $label;
    private $buttonClass;
    private $useButton;
    private $displayCondition;
    
    /**
     * Class Constructor
     * @param $action Callback to be executed
     * @param $parameters = array of parameters
     */
    public function __construct($action, $parameters = null)
    {
        parent::__construct($action, $parameters);
        
        if ($parameters)
        {
            $this->setFields( parent::getFieldParameters() );
        }
    }
    
    /**
     * Define wich Active Record's property will be passed along with the action
     * @param $field Active Record's property
     */
    public function setField($field)
    {
        $this->field = $field;
        
        $this->setParameter('key',  '{'.$field.'}');
        $this->setParameter($field, '{'.$field.'}');
    }
    
    /**
     * Define wich Active Record's properties will be passed along with the action
     * @param $field Active Record's property
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
        
        if ($fields)
        {
            if (empty($this->field) && empty(parent::getParameter('key')))
            {
                $this->setParameter('key', '{'.$fields[0].'}');
            }
            
            foreach ($fields as $field)
            {
                $this->setParameter($field, '{'.$field.'}');
            }
        }
    }
    
    /**
     * Returns the Active Record's property that 
     * will be passed along with the action
     */
    public function getField()
    {
        return $this->field;
    }
    
    /**
     * Returns the Active Record's properties that 
     * will be passed along with the action
     */
    public function getFields()
    {
        return $this->fields;
    }
    
    /**
     * Return if there at least one field defined
     */
    public function fieldDefined()
    {
        return (!empty($this->field) or !empty($this->fields));
    }
    
    /**
     * Define an icon for the action
     * @param $image  The Image path
     */
    public function setImage($image)
    {
        $this->image = $image;
    }
    
    /**
     * Returns the icon of the action
     */
    public function getImage()
    {
        return $this->image;
    }
    
    /**
     * define the label for the action
     * @param $label A string containing a text label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }
    
    /**
     * Returns the text label for the action
     */
    public function getLabel()
    {
        return $this->label;
    }
    
    /**
     * define the buttonClass for the action
     * @param $buttonClass A string containing the button css class
     */
    public function setButtonClass($buttonClass)
    {
        $this->buttonClass = $buttonClass;
    }
    
    /**
     * Returns the buttonClass
     */
    public function getButtonClass()
    {
        return $this->buttonClass;
    }
    
    /**
     * define if the action will use a regular button
     * @param $useButton A boolean
     */
    public function setUseButton($useButton)
    {
        $this->useButton = $useButton;
    }
    
    /**
     * Returns if the action will use a regular button
     */
    public function getUseButton()
    {
        return $this->useButton;
    }
    
    /**
     * Define a callback that must be valid to show the action
     * @param Callback $displayCondition Action display condition
     */
    public function setDisplayCondition( /*Callable*/ $displayCondition )
    {
        $this->displayCondition = $displayCondition;
    }
    
    /**
     * Returns the action display condition
     */
    public function getDisplayCondition()
    {
        return $this->displayCondition;
    }
    
    /**
     * Prepare action for use over an object
     * @param $object Data Object
     */
    public function prepare($object)
    {
        if ( !$this->fieldDefined() )
        {
            throw new Exception(AdiantiCoreTranslator::translate('Field for action ^1 not defined', parent::toString()) . '.<br>' . 
                                AdiantiCoreTranslator::translate('Use the ^1 method', 'setField'.'()').'.');
        }
        
        if ($field = $this->getField())
        {
            if ( !isset( $object->$field ) )
            {
                throw new Exception(AdiantiCoreTranslator::translate('Field ^1 not exists or contains NULL value', $field));
            }
        }
        
        if ($fields = $this->getFields())
        {
            $field = $fields[0];
            
            if ( !isset( $object->$field ) )
            {
                throw new Exception(AdiantiCoreTranslator::translate('Field ^1 not exists or contains NULL value', $field));
            }
        }
        
        return parent::prepare($object);
    }
    
    /**
     * Converts the action into an URL
     * @param  $format_action = format action with document or javascript (ajax=no)
     */
    public function serialize($format_action = TRUE, $check_permission = FALSE)
    {
        if (is_array($this->action) AND is_object($this->action[0]))
        {
            if (isset( $_REQUEST['offset'] ))
            {
                $this->setParameter('offset',     $_REQUEST['offset'] );
            }
            if (isset( $_REQUEST['limit'] ))
            {
                $this->setParameter('limit',      $_REQUEST['limit'] );
            }
            if (isset( $_REQUEST['page'] ))
            {
                $this->setParameter('page',       $_REQUEST['page'] );
            }
            if (isset( $_REQUEST['first_page'] ))
            {
                $this->setParameter('first_page', $_REQUEST['first_page'] );
            }
            if (isset( $_REQUEST['order'] ))
            {
                $this->setParameter('order',      $_REQUEST['order'] );
            }
        }
        if (parent::isStatic())
        {
            $this->setParameter('static',     '1' );
        }
        return parent::serialize($format_action, $check_permission);
    }
}
