<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Util\TImage;
use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreTranslator;
use Exception;

/**
 * Entry Widget
 *
 * @version    7.4
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TEntry extends TField implements AdiantiWidgetInterface
{
    private $mask;
    protected $completion;
    protected $numericMask;
    protected $decimals;
    protected $decimalsSeparator;
    protected $thousandSeparator;
    protected $reverse;
    protected $allowNegative;
    protected $replaceOnPost;
    protected $exitFunction;
    protected $exitAction;
    protected $id;
    protected $formName;
    protected $name;
    protected $value;
    protected $minLength;
    protected $delimiter;
    protected $exitOnEnterOn;
    protected $innerIcon;
    
    /**
     * Class Constructor
     * @param  $name name of the field
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->id   = 'tentry_' . mt_rand(1000000000, 1999999999);
        $this->numericMask = FALSE;
        $this->replaceOnPost = FALSE;
        $this->minLength = 1;
        $this->exitOnEnterOn = FALSE;
        $this->tag->{'type'}   = 'text';
        $this->tag->{'widget'} = 'tentry';
    }
    
    /**
     * Define input type
     */
    public function setInputType($type)
    {
        $this->tag->{'type'}  = $type;
    }
    
    /**
     * Define the Inner icon
     */
    public function setInnerIcon(TImage $image, $side = 'right')
    {
        $this->innerIcon = $image;
        $this->innerIcon->{'class'} .= ' input-inner-icon ' . $side;
        
        if ($side == 'left')
        {
            $this->setProperty('style', "padding-left:23px", false); //aggregate style info
        }
    }
    
    /**
     * Turn on exit on enter
     */
    public function exitOnEnter()
    {
        $this->exitOnEnterOn = true;
    }
    
    /**
     * Define the field's mask
     * @param $mask A mask for input data
     */
    public function setMask($mask, $replaceOnPost = FALSE)
    {
        $this->mask = $mask;
        $this->replaceOnPost = $replaceOnPost;
    }
    
    /**
     * Define the field's numeric mask (available just in web)
     * @param $decimals Sets the number of decimal points.
     * @param $decimalsSeparator Sets the separator for the decimal point.
     * @param $thousandSeparator Sets the thousands separator.
     * @param $allowNegative Sets negative allowed.
     */
    public function setNumericMask($decimals, $decimalsSeparator, $thousandSeparator, $replaceOnPost = FALSE, $reverse = FALSE, $allowNegative = TRUE)
    {
        if (empty($decimalsSeparator))
        {
            $decimals = 0;
        }
        else if (empty($decimals))
        {
            $decimalsSeparator = '';
        }
        
        $this->setProperty('style', "text-align:right;", false); //aggregate style info
        $this->numericMask = TRUE;
        $this->decimals = $decimals;
        $this->reverse = $reverse;
        $this->allowNegative = $allowNegative;
        $this->decimalsSeparator = $decimalsSeparator;
        $this->thousandSeparator = $thousandSeparator;
        $this->replaceOnPost = $replaceOnPost;
        
        $dec_pattern = $decimalsSeparator == '.' ? '\\.' : $decimalsSeparator;
        $tho_pattern = $thousandSeparator == '.' ? '\\.' : $thousandSeparator;
        
        //$this->tag->{'pattern'}   = '^\\$?(([1-9](\\d*|\\d{0,2}('.$tho_pattern.'\\d{3})*))|0)('.$dec_pattern.'\\d{1,2})?$';
        $this->tag->{'pattern'}   = '^\\$?(([1-9](\\d*|\\d{0,'.$decimals.'}('.$tho_pattern.'\\d{3})*))|0)('.$dec_pattern.'\\d{1,'.$decimals.'})?$';
        $this->tag->{'inputmode'} = 'numeric';
        $this->tag->{'data-nmask'}  = $decimals.$decimalsSeparator.$thousandSeparator;
    }
    
    /**
     * Define the field's value
     * @param $value A string containing the field's value
     */
    public function setValue($value)
    {
        if ($this->replaceOnPost)
        {
            if ($this->numericMask && is_numeric($value))
            {
                parent::setValue(number_format($value, $this->decimals, $this->decimalsSeparator, $this->thousandSeparator));
            }
            else if ($this->mask)
            {
                parent::setValue($this->formatMask($this->mask, $value));
            }
            else
            {
                parent::setValue($value);
            }
        }
        else
        {
            parent::setValue($value);
        }
    }
    
    /**
     * Return the post data
     */
    public function getPostData()
    {
        $name = str_replace(['[',']'], ['',''], $this->name);
        
        if (isset($_POST[$name]))
        {
            if ($this->replaceOnPost)
            {
                $value = $_POST[$name];
                
                if ($this->numericMask)
                {
                    $value = str_replace( $this->thousandSeparator, '', $value);
                    $value = str_replace( $this->decimalsSeparator, '.', $value);
                    return $value;
                }
                else if ($this->mask)
                {
                    return preg_replace('/[^a-z\d]+/i', '', $value);
                }
                else
                {
                    return $value;
                }
            }
            else
            {
                return $_POST[$name];
            }
        }
        else
        {
            return '';
        }
    }
    
    /**
     * Define max length
     * @param  $length Max length
     */
    public function setMaxLength($length)
    {
        if ($length > 0)
        {
            $this->tag->{'maxlength'} = $length;
        }
    }
    
    /**
     * Define options for completion
     * @param $options array of options for completion
     */
    function setCompletion($options)
    {
        $this->completion = $options;
    }
    
    /**
     * Define the action to be executed when the user leaves the form field
     * @param $action TAction object
     */
    function setExitAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->exitAction = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
    }
    
    /**
     * Define the javascript function to be executed when the user leaves the form field
     * @param $function Javascript function
     */
    public function setExitFunction($function)
    {
        $this->exitFunction = $function;
    }
    
    /**
     * Force lower case
     */
    public function forceLowerCase()
    {
        $this->tag->{'onKeyPress'} = "return tentry_lower(this)";
        $this->tag->{'onBlur'} = "return tentry_lower(this)";
        $this->tag->{'forcelower'} = "1";
        $this->setProperty('style', "text-transform: lowercase;", false); //aggregate style info
        
    }
    
    /**
     * Force upper case
     */
    public function forceUpperCase()
    {
        $this->tag->{'onKeyPress'} = "return tentry_upper(this)";
        $this->tag->{'onBlur'} = "return tentry_upper(this)";
        $this->tag->{'forceupper'} = "1";
        $this->setProperty('style', "text-transform: uppercase;", false); //aggregate style info
    }
    
    /**
     * Set autocomplete delimiter
     * @param $delimiter autocomplete delimiter
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
    }
    
    /**
     * Define the minimum length for search
     */
    public function setMinLength($length)
    {
        $this->minLength = $length;
    }
    
    /**
     * Reload completion
     * 
     * @param $field Field name or id
     * @param $options array of options for autocomplete
     */
    public static function reloadCompletion($field, $list, $options = null)
    {
        $list_json = json_encode($list);
        if (is_null($options))
        {
            $options = [];
        }
        
        $options_json = json_encode( $options );
        TScript::create(" tentry_autocomplete_by_name( '{$field}', {$list_json}, '{$options_json}'); ");
    }
    
    /**
     * Apply mask
     * 
     * @param $mask  Mask
     * @param $value Value
     */
    protected function formatMask($mask, $value)
    {
        if ($value)
        {
            $value_index  = 0;
            $clear_result = '';
        
            $value = preg_replace('/[^a-z\d]+/i', '', $value);
            
            for ($mask_index=0; $mask_index < strlen($mask); $mask_index ++)
            {
                $mask_char = substr($mask, $mask_index,  1);
                $text_char = substr($value, $value_index, 1);
        
                if (in_array($mask_char, array('-', '_', '.', '/', '\\', ':', '|', '(', ')', '[', ']', '{', '}', ' ')))
                {
                    $clear_result .= $mask_char;
                }
                else
                {
                    $clear_result .= $text_char;
                    $value_index ++;
                }
            }
            return $clear_result;
        }
    }
    
    /**
     * Change mask dynamically
     */
    public static function changeMask($formName, $name, $mask)
    {
        TScript::create("tentry_change_mask( '{$formName}', '{$name}', '{$mask}');");
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        // define the tag properties
        $this->tag->{'name'}  = $this->name;    // TAG name
        $this->tag->{'value'} = htmlspecialchars( (string) $this->value, ENT_QUOTES | ENT_HTML5, 'UTF-8');   // TAG value
        
        if (!empty($this->size))
        {
            if (strstr((string) $this->size, '%') !== FALSE)
            {
                $this->setProperty('style', "width:{$this->size};", false); //aggregate style info
            }
            else
            {
                $this->setProperty('style', "width:{$this->size}px;", false); //aggregate style info
            }
        }
        
        if ($this->id and empty($this->tag->{'id'}))
        {
            $this->tag->{'id'} = $this->id;
        }
        
        if (isset($this->exitAction))
        {
            if (!TForm::getFormByName($this->formName) instanceof TForm)
            {
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
            }
            $string_action = $this->exitAction->serialize(FALSE);
            $this->setProperty('exitaction', "__adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback')");
        }
        
        // verify if the widget is non-editable
        if (parent::getEditable())
        {
            if (isset($this->exitAction))
            {
                // just aggregate onBlur, if the previous one does not have return clause
                if (strstr((string) $this->getProperty('onBlur'), 'return') == FALSE)
                {
                    $this->setProperty('onBlur', $this->getProperty('exitaction'), FALSE);
                }
                else
                {
                    $this->setProperty('onBlur', $this->getProperty('exitaction'), TRUE);
                }
            }
            
            if (isset($this->exitFunction))
            {
                if (strstr($this->getProperty('onBlur'), 'return') == FALSE)
                {
                    $this->setProperty('onBlur', $this->exitFunction, FALSE);
                }
                else
                {
                    $this->setProperty('onBlur', $this->exitFunction, TRUE);
                }
            }
        }
        else
        {
            $this->tag->{'readonly'} = "1";
            $this->tag->{'class'} .= ' tfield_disabled'; // CSS
            $this->tag->{'tabindex'} = '-1';
            $this->tag->{'onmouseover'} = "style.cursor='default'";
        }
        
        if ($this->mask)
        {
            TScript::create( "tentry_new_mask( '{$this->id}', '{$this->mask}'); ");
        }
        
        if (!empty($this->innerIcon))
        {
            $icon_wrapper = new TElement('div');
            $icon_wrapper->add($this->tag);
            $icon_wrapper->add($this->innerIcon);
            $icon_wrapper->show();
        }
        else
        {
            // shows the tag
            $this->tag->show();
        }
        
        if (isset($this->completion))
        {
            $options = [ 'minChars' => $this->minLength ];
            if (!empty($this->delimiter))
            {
                $options[ 'delimiter'] = $this->delimiter;
            }
            $options_json = json_encode( $options );
            $list = json_encode($this->completion);
            TScript::create(" tentry_autocomplete( '{$this->id}', $list, '{$options_json}'); ");
        }
        if ($this->numericMask)
        {
            $reverse = $this->reverse ? 'true' : 'false';
            $allowNegative = $this->allowNegative ? 'true' : 'false';

            TScript::create( "tentry_numeric_mask( '{$this->id}', {$this->decimals}, '{$this->decimalsSeparator}', '{$this->thousandSeparator}', {$reverse}, {$allowNegative}); ");
        }
        
        if ($this->exitOnEnterOn)
        {
            TScript::create( "tentry_exit_on_enter( '{$this->id}' ); ");
        }
    }
}
