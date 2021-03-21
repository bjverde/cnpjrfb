<?php
namespace Adianti\Widget\Form;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TEntry;

use DateTime;

/**
 * DateTimePicker Widget
 *
 * @version    7.3
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDateTime extends TEntry implements AdiantiWidgetInterface
{
    private $mask;
    private $dbmask;
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
        $this->id   = 'tdatetime_' . mt_rand(1000000000, 1999999999);
        $this->mask = 'yyyy-mm-dd hh:ii';
        $this->dbmask = null;
        $this->options = [];
        $this->replaceOnPost = FALSE;
        
        $this->setOption('fontAwesome', true);
        
        $newmask = $this->mask;
        $newmask = str_replace('dd',   '99',   $newmask);
        $newmask = str_replace('hh',   '99',   $newmask);
        $newmask = str_replace('ii',   '99',   $newmask);
        $newmask = str_replace('mm',   '99',   $newmask);
        $newmask = str_replace('yyyy', '9999', $newmask);
        parent::setMask($newmask);
        $this->tag->{'widget'} = 'tdatetime';
    }
    
    /**
     * Store the value inside the object
     */
    public function setValue($value)
    {
        $value = str_replace('T', ' ', $value);
        if (!empty($this->dbmask) and ($this->mask !== $this->dbmask) )
        {
            return parent::setValue( self::convertToMask($value, $this->dbmask, $this->mask) );
        }
        else
        {
            return parent::setValue($value);
        }
    }
    
    /**
     * Return the post data
     */
    public function getPostData()
    {
        $value = parent::getPostData();
        
        if (!empty($this->dbmask) and ($this->mask !== $this->dbmask) )
        {
            return self::convertToMask($value, $this->mask, $this->dbmask);
        }
        else
        {
            return $value;
        }
    }
    
    /**
     * Convert from one mask to another
     * @param $value original date
     * @param $fromMask source mask
     * @param $toMask target mask
     */
    public static function convertToMask($value, $fromMask, $toMask)
    {
        if (is_array($value)) // vector fields (field list)
        {
            foreach ($value as $key => $item)
            {
                $value[$key] = self::convertToMask($item, $fromMask, $toMask);
            }
            
            return $value;
        }
        else if ($value)
        {
            $value = substr($value,0,strlen($fromMask));
            
            $phpFromMask = str_replace( ['dd','mm', 'yyyy', 'hh', 'ii', 'ss'], ['d','m','Y', 'H', 'i', 's'], $fromMask);
            $phpToMask   = str_replace( ['dd','mm', 'yyyy', 'hh', 'ii', 'ss'], ['d','m','Y', 'H', 'i', 's'], $toMask);
            
            $date = DateTime::createFromFormat($phpFromMask, $value);
            if ($date)
            {
                return $date->format($phpToMask);
            }
        }
        
        return $value;
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
        $newmask = str_replace('dd',   '99',   $newmask);
        $newmask = str_replace('hh',   '99',   $newmask);
        $newmask = str_replace('ii',   '99',   $newmask);
        $newmask = str_replace('mm',   '99',   $newmask);
        $newmask = str_replace('yyyy', '9999', $newmask);
        
        parent::setMask($newmask);
    }
    
    /**
     * Set the mask to be used to colect the data
     */
    public function setDatabaseMask($mask)
    {
        $this->dbmask = $mask;
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
        $js_mask = str_replace('yyyy', 'yy', $this->mask);
        $language = strtolower( AdiantiCoreTranslator::getLanguage() );
        $options = json_encode($this->options);
        
        $outer_size = 'undefined';
        if (strstr($this->size, '%') !== FALSE)
        {
            $outer_size = $this->size;
            $this->size = '100%';
        }
        
        parent::show();
        
        TScript::create( "tdatetime_start( '#{$this->id}', '{$this->mask}', '{$language}', '{$outer_size}', '{$options}');");
        
        if (!parent::getEditable())
        {
            TScript::create( " tdate_disable_field( '{$this->formName}', '{$this->name}' ); " );
        }
    }
}
