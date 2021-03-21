<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TField;
use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreTranslator;
use Exception;

/**
 * Entry Widget
 *
 * @version    7.3
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
     */
    public function setNumericMask($decimals, $decimalsSeparator, $thousandSeparator, $replaceOnPost = FALSE, $reverse = FALSE)
    {
        if (empty($decimalsSeparator))
        {
            $decimals = 0;
        }
        else if (empty($decimals))
        {
            $decimalsSeparator = '';
        }
        
        $this->{'style'} = 'text-align:right';
        $this->numericMask = TRUE;
        $this->decimals = $decimals;
        $this->reverse = $reverse;
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
        $this->setProperty('style', 'text-transform: lowercase');
        
    }
    
    /**
     * Force upper case
     */
    public function forceUpperCase()
    {
        $this->tag->{'onKeyPress'} = "return tentry_upper(this)";
        $this->tag->{'onBlur'} = "return tentry_upper(this)";
        $this->tag->{'forceupper'} = "1";
        $this->setProperty('style', 'text-transform: uppercase');
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
     * Shows the widget at the screen
     */
    public function show()
    {
        // define the tag properties
        $this->tag->{'name'}  = $this->name;    // TAG name
        $this->tag->{'value'} = htmlspecialchars($this->value, ENT_QUOTES | ENT_HTML5, 'UTF-8');   // TAG value
        
        if (!empty($this->size))
        {
            if (strstr($this->size, '%') !== FALSE)
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
                if (strstr($this->getProperty('onBlur'), 'return') == FALSE)
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
            
            if ($this->mask)
            {
                TScript::create( "tentry_new_mask( '{$this->id}', '{$this->mask}'); ");
            }
        }
        else
        {
            $this->tag->{'readonly'} = "1";
            $this->tag->{'class'} .= ' tfield_disabled'; // CSS
            $this->tag->{'tabindex'} = '-1';
            $this->tag->{'onmouseover'} = "style.cursor='default'";
        }
        
        // shows the tag
        $this->tag->show();
        
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
            TScript::create( "tentry_numeric_mask( '{$this->id}', {$this->decimals}, '{$this->decimalsSeparator}', '{$this->thousandSeparator}', {$this->reverse}); ");
        }
        
        if ($this->exitOnEnterOn)
        {
            TScript::create( "tentry_exit_on_enter( '{$this->id}' ); ");
        }
    }
}
