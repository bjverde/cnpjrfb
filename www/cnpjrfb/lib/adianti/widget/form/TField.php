<?php
namespace Adianti\Widget\Form;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Validator\TFieldValidator;
use Adianti\Validator\TRequiredValidator;
use Adianti\Validator\TEmailValidator;
use Adianti\Validator\TMinLengthValidator;
use Adianti\Validator\TMaxLengthValidator;

use Exception;
use ReflectionClass;
use Closure;

/**
 * Base class to construct all the widgets
 *
 * @version    7.1
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
abstract class TField
{
    protected $id;
    protected $name;
    protected $size;
    protected $value;
    protected $editable;
    protected $tag;
    protected $formName;
    protected $label;
    protected $properties;
    protected $valueCallback;
    private   $validations;
    
    /**
     * Class Constructor
     * @param  $name name of the field
     */
    public function __construct($name)
    {
        $rc = new ReflectionClass( $this );
        $classname = $rc->getShortName();
        
        if (empty($name))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 constructor is required', 'name', $classname));
        }
        
        // define some default properties
        self::setEditable(TRUE);
        self::setName(trim($name));
        
        // initialize validations array
        $this->validations = [];
        $this->properties  = [];
        
        // creates a <input> tag
        $this->tag = new TElement('input');
        $this->tag->{'class'} = 'tfield';   // classe CSS
        $this->tag->{'widget'} = strtolower($classname);
    }
    
    /**
     * Intercepts whenever someones assign a new property's value
     * @param $name     Property Name
     * @param $value    Property Value
     */
    public function __set($name, $value)
    {
        // objects and arrays are not set as properties
        if (is_scalar($value))
        {              
            // store the property's value
            $this->setProperty($name, $value);
        }
    }
    
    /**
     * Returns a property value
     * @param $name     Property Name
     */
    public function __get($name)
    {
        return $this->getProperty($name);
    }
    
    /**
     * Returns if the property is set
     * @param $name     Property Name
     */
    public function __isset($name)
    {
        return isset($this->tag->$name);
    }
    
    /**
     * Clone the object
     */
    function __clone()
    {
        $this->tag = clone $this->tag;
    }
    
    /**
     * Redirects function call
     * @param $method Method name
     * @param $param  Array of parameters
     */
    public function __call($method, $param)
    {
        if (method_exists($this->tag, $method))
        {
            return call_user_func_array( array($this->tag, $method), $param );
        }
        else
        {
            throw new Exception(AdiantiCoreTranslator::translate("Method ^1 not found", $method.'()'));
        }
    }
    
    /**
     * Set callback for setValue method
     */
    public function setValueCallback($callback)
    {
        $this->valueCallback = $callback;
    }
    
    /**
     * Define the field's label
     * @param $label   A string containing the field's label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Returns the field's label
     */
    public function getLabel()
    {
        return $this->label;
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
     * Define the field's id
     * @param $id A string containing the field's id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the field's id
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Define the field's value
     * @param $value A string containing the field's value
     */
    public function setValue($value)
    {
        $this->value = $value;
        
        if (!empty($this->valueCallback) && ($this->valueCallback instanceof Closure))
        {
            $callback = $this->valueCallback;
            $callback($this, $value);
        }
    }
    
    /**
     * Returns the field's value
     */
    public function getValue()
    {
        return $this->value;
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
     * Define the field's tooltip
     * @param $name   A string containing the field's tooltip
     */
    public function setTip($tip)
    {
        $this->tag->{'title'} = $tip;
    }
    
    /**
     * Return the post data
     */
    public function getPostData()
    {
        if (isset($_POST[$this->name]))
        {
            return $_POST[$this->name];
        }
        else
        {
            return '';
        }
    }
    
    /**
     * Define if the field is editable
     * @param $editable A boolean
     */
    public function setEditable($editable)
    {
        $this->editable= $editable;
    }

    /**
     * Returns if the field is editable
     * @return A boolean
     */
    public function getEditable()
    {
        return $this->editable;
    }
    
    /**
     * Define a field property
     * @param $name  Property Name
     * @param $value Property Value
     */
    public function setProperty($name, $value, $replace = TRUE)
    {
        if ($replace)
        {
            // delegates the property assign to the composed object
            $this->tag->$name = $value;
        }
        else
        {
            if ($this->tag->$name)
            {
            
                // delegates the property assign to the composed object
                $this->tag->$name = $this->tag->$name . ';' . $value;
            }
            else
            {
                // delegates the property assign to the composed object
                $this->tag->$name = $value;
            }
        }
        
        $this->properties[ $name ] = $this->tag->$name;
    }
    
    /**
     * Get properties as string
     */
    public function getPropertiesAsString($filter = null)
    {
        $content = '';
        
        if ($this->properties)
        {
            foreach ($this->properties as $name => $value)
            {
                if ( empty($filter) || ($filter && strpos($name, $filter) !== false))
                {
                    $value = str_replace('"', '&quot;', $value);
                    $content .= " {$name}=\"{$value}\"";
                }
            }
        }
        
        return $content;
    }
    
    /**
     * Return a field property
     * @param $name  Property Name
     * @param $value Property Value
     */
    public function getProperty($name)
    {
        return $this->tag->$name;
    }
    
    /**
     * Define the Field's width
     * @param $width Field's width in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $this->size = $width;
    }
    
    /**
     * Returns the field size
     */
    public function getSize()
    {
        return $this->size;
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
        
        if ($validator instanceof TRequiredValidator)
        {
            $this->tag->{'required'} = '';
        }
        
        if ($validator instanceof TEmailValidator)
        {
            $this->tag->{'type'} = 'email';
        }
        
        if ($validator instanceof TMinLengthValidator)
        {
            $this->tag->{'minlength'} = $parameters[0];
        }
        
        if ($validator instanceof TMaxLengthValidator)
        {
            $this->tag->{'maxlength'} = $parameters[0];
        }
    }
    
    /**
     * Returns field validations
     */
    public function getValidations()
    {
        return $this->validations;
    }
    
    /**
     * Returns if the field is required
     */
    public function isRequired()
    {
        if ($this->validations)
        {
            foreach ($this->validations as $validation)
            {
                $validator = $validation[1];
                if ($validator instanceof TRequiredValidator)
                {
                    return TRUE;
                }
            }
        }
        return FALSE;
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
     * Returns the element content as a string
     */
    public function getContents()
    {
        ob_start();
        $this->show();
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    
    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " tfield_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " tfield_disable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Clear the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function clearField($form_name, $field)
    {
        TScript::create( " tfield_clear_field('{$form_name}', '{$field}'); " );
    }
}
