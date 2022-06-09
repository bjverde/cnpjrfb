<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TEntry;
use Adianti\Control\TAction;

/**
 * Color Widget
 *
 * @version    7.4
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TIcon extends TEntry implements AdiantiWidgetInterface
{
    protected $id;
    protected $changeFunction;
    protected $formName;
    protected $name;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->id = 'ticon_'.mt_rand(1000000000, 1999999999);
        $this->tag->{'autocomplete'} = 'off';
    }
    
    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " ticon_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " ticon_disable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Set change function
     */
    public function setChangeFunction($function)
    {
        $this->changeFunction = $function;
    }

    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        $wrapper = new TElement('div');
        $wrapper->{'class'} = 'input-group';
        $span = new TElement('span');
        $span->{'class'} = 'input-group-addon';
        
        if (!empty($this->exitAction))
        {
            $this->setChangeFunction( $this->changeFunction . "; tform_fire_field_actions('{$this->formName}', '{$this->name}'); " );
        }
        
        $i = new TElement('i');
        $span->add($i);
        
        if (strstr((string) $this->size, '%') !== FALSE)
        {
            $outer_size = $this->size;
            $this->size = '100%';
            $wrapper->{'style'} = "width: $outer_size";
        }
        
        ob_start();
        parent::show();
        $child = ob_get_contents();
        ob_end_clean();
        
        $wrapper->add($child);
        $wrapper->add($span);
        $wrapper->show();
        
        if (parent::getEditable())
        {
            if($this->changeFunction)
            {
                TScript::create(" ticon_start('{$this->id}',function(icon){ {$this->changeFunction} }); ");   
            }
            else
            {
                TScript::create(" ticon_start('{$this->id}',false); ");
            }
        }
    }
}
