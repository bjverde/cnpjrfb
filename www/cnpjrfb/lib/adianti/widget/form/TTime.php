<?php
namespace Adianti\Widget\Form;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TEntry;

use DateTime;

/**
 * TimePicker Widget
 *
 * @version    7.1
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TTime extends TEntry implements AdiantiWidgetInterface
{
    private $mask;
    protected $id;
    protected $size;
    protected $value;
    protected $options;
    protected $replaceOnPost;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->id   = 'ttime_' . mt_rand(1000000000, 1999999999);
        $this->mask = 'hh:ii';
        $this->options = [];
        
        $this->setOption('startView', 1);
        $this->setOption('pickDate', false);
        $this->setOption('formatViewType', 'time');
        $this->setOption('fontAwesome', true);
    
        $newmask = $this->mask;
        $newmask = str_replace('hh',   '99',   $newmask);
        $newmask = str_replace('ii',   '99',   $newmask);
        parent::setMask($newmask);
        $this->tag->{'widget'} = 'ttime';
    }
    
    /**
     * Define the field's mask
     * @param $mask  Mask for the field (dd-mm-yyyy)
     */
    public function setMask($mask, $replaceOnPost = FALSE)
    {
        $this->mask = $mask;
        $this->replaceOnPost = $replaceOnPost;
        
        $newmask = $this->mask;
        $newmask = str_replace('hh',   '99',   $newmask);
        $newmask = str_replace('ii',   '99',   $newmask);
        
        parent::setMask($newmask, $replaceOnPost);
    }
    
    /**
     * Set extra datepicker options (ex: autoclose, startDate, daysOfWeekDisabled, datesDisabled)
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }
    
    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " tdate_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " tdate_disable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        $language = strtolower( AdiantiCoreTranslator::getLanguage() );
        $options = json_encode($this->options);
        
        if (parent::getEditable())
        {
            $outer_size = 'undefined';
            if (strstr($this->size, '%') !== FALSE)
            {
                $outer_size = $this->size;
                $this->size = '100%';
            }
        }
        
        parent::show();
        
        if (parent::getEditable())
        {
            TScript::create( "tdatetime_start( '#{$this->id}', '{$this->mask}', '{$language}', '{$outer_size}', '{$options}');");
        }
    }
}
