<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Control\TAction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Util\TImage;

use Adianti\Core\AdiantiCoreTranslator;
use Exception;
use ReflectionClass;

/**
 * Record Lookup Widget: Creates a lookup field used to search values from associated entities
 *
 * @version    7.1
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSeekButton extends TEntry implements AdiantiWidgetInterface
{
    private $action;
    private $useOutEvent;
    private $button;
    private $extra_size;
    protected $auxiliar;
    protected $id;
    protected $formName;
    protected $name;
    
    /**
     * Class Constructor
     * @param  $name name of the field
     */
    public function __construct($name, $icon = NULL)
    {
        parent::__construct($name);
        $this->useOutEvent = TRUE;
        $this->setProperty('class', 'tfield tseekentry', TRUE);   // classe CSS
        $this->extra_size = 24;
        $this->button = self::createButton($this->name, $icon);
    }
    
    /**
     * Create seek button object
     */
    public static function createButton($name, $icon)
    {
        $image = new TImage( $icon ? $icon : 'fa:search');
        $button = new TElement('span');
        $button->{'class'} = 'btn btn-default tseekbutton';
        $button->{'type'} = 'button';
        $button->{'onmouseover'} = "style.cursor = 'pointer'";
        $button->{'name'} = '_' . $name . '_seek';
        $button->{'for'} = $name;
        $button->{'onmouseout'}  = "style.cursor = 'default'";
        $button->add($image);
        
        return $button;
    }
    
    /**
     * Returns a property value
     * @param $name     Property Name
     */
    public function __get($name)
    {
        if ($name == 'button')
        {
            return $this->button;
        }
        else
        {
            return parent::__get($name);
        }
    }
    
    /**
     * Define it the out event will be fired
     */
    public function setUseOutEvent($bool)
    {
        $this->useOutEvent = $bool;
    }
    
    /**
     * Define the action for the SeekButton
     * @param $action Action taken when the user
     * clicks over the Seek Button (A TAction object)
     */
    public function setAction(TAction $action)
    {
        $this->action = $action;
    }
    
    /**
     * Return the action
     */
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * Define an auxiliar field
     * @param $object any TField object
     */
    public function setAuxiliar($object)
    {
        if (method_exists($object, 'show'))
        {
            $this->auxiliar = $object;
            $this->extra_size *= 2;
            
            if ($object instanceof TField)
            {
                $this->action->setParameter('receive_field', $object->getName());
            }
        }
    }
    
    /**
     * Returns if has auxiliar field
     */
    public function hasAuxiliar()
    {
        return !empty($this->auxiliar);
    }
    
    /**
     * Set extra size
     */
    public function setExtraSize($extra_size)
    {
        $this->extra_size = $extra_size;
    }
    
    /**
     * Returns extra size
     */
    public function getExtraSize()
    {
        return $this->extra_size;
    }
    
    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " tseekbutton_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " tseekbutton_disable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Show the widget
     */
    public function show()
    {
        // check if it's not editable
        if (parent::getEditable())
        {
            if (!TForm::getFormByName($this->formName) instanceof TForm)
            {
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
            }
            
            $serialized_action = '';
            if ($this->action)
            {
                // get the action class name
                if (is_array($callback = $this->action->getAction()))
                {
                    if (is_object($callback[0]))
                    {
                        $rc = new ReflectionClass($callback[0]);
                        $classname = $rc->getShortName();
                    }
                    else
                    {
                        $classname  = $callback[0];
                    }
                    
                    if ($this->useOutEvent)
                    {
                        $inst       = new $classname;
                        $ajaxAction = new TAction(array($inst, 'onSelect'));
                        
                        if (in_array($classname, array('TStandardSeek')))
                        {
                            $ajaxAction->setParameter('parent',  $this->action->getParameter('parent'));
                            $ajaxAction->setParameter('database',$this->action->getParameter('database'));
                            $ajaxAction->setParameter('model',   $this->action->getParameter('model'));
                            $ajaxAction->setParameter('display_field', $this->action->getParameter('display_field'));
                            $ajaxAction->setParameter('receive_key',   $this->action->getParameter('receive_key'));
                            $ajaxAction->setParameter('receive_field', $this->action->getParameter('receive_field'));
                            $ajaxAction->setParameter('criteria',      $this->action->getParameter('criteria'));
                            $ajaxAction->setParameter('mask',          $this->action->getParameter('mask'));
                            $ajaxAction->setParameter('operator',      $this->action->getParameter('operator') ? $this->action->getParameter('operator') : 'like');
                        }
                        else
                        {
                            if ($actionParameters = $this->action->getParameters())
                            {
                                foreach ($actionParameters as $key => $value) 
                                {
                                    $ajaxAction->setParameter($key, $value);
                                }                    		
                            }                    	                    
                        }
                        $ajaxAction->setParameter('form_name',  $this->formName);
                        
                        $string_action = $ajaxAction->serialize(FALSE);
                        $this->setProperty('seekaction', "__adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback')");
                        $this->setProperty('onBlur', $this->getProperty('seekaction'), FALSE);
                    }
                }
                $this->action->setParameter('field_name', $this->name);
                $this->action->setParameter('form_name',  $this->formName);
                $serialized_action = $this->action->serialize(FALSE);
            }
            
            $this->button->{'onclick'} = "javascript:serialform=(\$('#{$this->formName}').serialize());__adianti_append_page('engine.php?{$serialized_action}&'+serialform)";
                  
            $wrapper = new TElement('div');
            $wrapper->{'class'} = 'tseek-group';
            $wrapper->open();
            parent::show();
            $this->button->show();
            
            if ($this->auxiliar)
            {
                $this->auxiliar->show();
            }
            $wrapper->close();
        }
        else
        {
            parent::show();
        }
    }
}
