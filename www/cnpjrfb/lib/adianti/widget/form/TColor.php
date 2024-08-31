<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TEntry;
use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreTranslator;
use Exception;

/**
 * Color Widget
 *
 * @version    7.6
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TColor extends TEntry implements AdiantiWidgetInterface
{
    const THEME_CLASSIC  = 'classic';
    const THEME_NANO     = 'nano';
    const THEME_MONOLITH = 'monolith';

    protected $formName;
    protected $name;
    protected $id;
    protected $size;
    protected $changeFunction;
    protected $changeAction;
    protected $theme;
    protected $options;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->id = 'tcolor_'.mt_rand(1000000000, 1999999999);
        $this->tag->{'widget'} = 'tcolor';
        $this->tag->{'autocomplete'} = 'off';
        $this->setSize('100%');
        
        $this->theme = self::THEME_CLASSIC;
        $this->options = [
            'swatches' => [
                '#F44336', '#E91E63', '#9C27B0', '#673AB7', '#3F51B5', '#2196F3', '#03A9F4',
                '#00BCD4', '#009688', '#4CAF50', '#8BC34A', '#CDDC39', '#ffe821', '#FFC107',
                '#FF9800', '#FF5722', '#795548', '#9E9E9E', '#607D8B', '#000000', '#ffffff',
            ],
            'components' => [
                'preview' => true,
                'opacity' => true,
                'hue' => true,
                'interaction' => [
                    'hex' => false,
                    'rgba' => false,
                    'hsla' => false,
                    'hsva' => false,
                    'cmyk' => false,
                    'input' => false,
                    'clear' => true,
                    'save' => true
                ]
            ],
        ];
    }

    /**
     * Set extra option TColor
     *
     * @see Component documentation https://github.com/Simonwep/pickr#options
     *
     * @param $option Key name option
     * @param $value Option value
     */
    public function setOption($option, $value)
    {
        if (is_array($value))
        {
            $oldOptions = $this->options[$option]??[];

            $value = array_merge($oldOptions, $value);
        }

        $this->options[$option] = $value;
    }

    /**
     * Get options TColor
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get option TColor
     * 
     */
    public function getOption($option)
    {
        if (empty($this->options[$option]))
        {
            return null;
        }

        return $this->options[$option];
    }

    /**
     * Set theme
     */
    public function setTheme($theme)
    {
        if (! in_array($theme, [self::THEME_CLASSIC, self::THEME_NANO, self::THEME_MONOLITH]) )
        {
            $theme = self::THEME_CLASSIC;
        }

        $this->theme = $theme;
    }
    
    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " tcolor_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " tcolor_disable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Set change function
     */
    public function setChangeFunction($function)
    {
        $this->changeFunction = $function;
    }
    
    /**
     * Define the action to be executed when the user changes the content
     * @param $action TAction object
     */
    public function setChangeAction(TAction $action)
    {
        $this->changeAction = $action;
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        $wrapper = new TElement('div');
        $wrapper->{'class'} = 'input-group color-div colorpicker-component';
        $wrapper->{'style'} = 'float:inherit';
        
        $span = new TElement('span');
        $span->{'class'} = 'input-group-addon tcolor';
        
        $outer_size = 'undefined';
        if (strstr((string) $this->size, '%') !== FALSE)
        {
            $outer_size = $this->size;
            $this->size = '100%';
        }
        
        if ($this->changeAction)
        {
            if (!TForm::getFormByName($this->formName) instanceof TForm)
            {
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
            }
            
            $string_action = $this->changeAction->serialize(FALSE);
            $this->setProperty('changeaction', "__adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback')");
            $this->changeFunction = $this->getProperty('changeaction');
        }
        
        $i = new TElement('i');
        $i->{'class'} = 'tcolor-icon';
        $span->add($i);
        ob_start();
        parent::show();
        $child = ob_get_contents();
        ob_end_clean();
        $wrapper->add($child);
        $wrapper->add($span);
        $wrapper->show();
        
        $options = json_encode($this->options);

        TScript::create("tcolor_start('{$this->id}', '{$outer_size}', '{$this->theme}', function(color) { {$this->changeFunction} }, {$options}); ");
        
        if (!parent::getEditable())
        {
            self::disableField($this->formName, $this->name);
        }
    }
}
