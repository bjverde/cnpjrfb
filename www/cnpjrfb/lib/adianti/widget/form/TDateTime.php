<?php
namespace Adianti\Widget\Form;

use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TEntry;

use DateTime;
use Exception;

/**
 * DateTimePicker Widget
 *
 * @version    7.4
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
    protected $changeFunction;
    protected $changeAction;

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
     * Set change function
     */
    public function setChangeFunction($function)
    {
        $this->changeFunction = $function;
    }

    /**
     * Define the action to be executed when the user changes the field
     * @param $action TAction object
     */
    public function setExitAction(TAction $action)
    {
        $this->setChangeAction($action);
    }

    /**
     * Define the action to be executed when the user changes the field
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
        $newmask = str_replace('ss',   '99',   $newmask);
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
     * Set extra datepicker options
     * @link https://www.malot.fr/bootstrap-datetimepicker/
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
        if (strstr((string) $this->size, '%') !== FALSE)
        {
            $outer_size = $this->size;
            $this->size = '100%';
        }

        if (isset($this->changeAction))
        {
            if (!TForm::getFormByName($this->formName) instanceof TForm)
            {
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
            }

            $string_action = $this->changeAction->serialize(FALSE);
            $this->setProperty('changeaction', "__adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback')");
            $this->setProperty('onChange', $this->getProperty('changeaction'));
        }

        if (isset($this->changeFunction))
        {
            $this->setProperty('changeaction', $this->changeFunction, FALSE);
            $this->setProperty('onChange', $this->changeFunction, FALSE);
        }

        parent::show();

        TScript::create( "tdatetime_start( '#{$this->id}', '{$this->mask}', '{$language}', '{$outer_size}', '{$options}');");

        if (!parent::getEditable())
        {
            TScript::create( " tdate_disable_field( '{$this->formName}', '{$this->name}' ); " );
        }
    }
}
