<?php
namespace Adianti\Widget\Form;

use Adianti\Core\AdiantiApplicationConfig;
use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\THidden;

use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreTranslator;
use Exception;

/**
 * FileChooser widget
 *
 * @version    7.1
 * @package    widget
 * @subpackage form
 * @author     Nataniel Rabaioli
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TFile extends TField implements AdiantiWidgetInterface
{
    protected $id;
    protected $height;
    protected $completeAction;
    protected $uploaderClass;
    protected $placeHolder;
    protected $extensions;
    protected $displayMode;
    protected $seed;
    protected $fileHandling;
    protected $imageGallery;
    protected $galleryWidth;
    protected $galleryHeight;
    protected $popover;
    protected $poptitle;
    protected $popcontent;
    
    /**
     * Constructor method
     * @param $name input name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->id = $this->name . '_' . mt_rand(1000000000, 1999999999);
        $this->uploaderClass = 'AdiantiUploaderService';
        $this->fileHandling = FALSE;
        
        $ini = AdiantiApplicationConfig::get();
        $this->seed = APPLICATION_NAME . ( !empty($ini['general']['seed']) ? $ini['general']['seed'] : 's8dkld83kf73kf094' );
        $this->imageGallery = false;
        $this->popover = false;
    }
    
    /**
     * Enable image gallery view
     */
    public function enableImageGallery($width = null, $height = 100)
    {
        $this->imageGallery  = true;
        $this->galleryWidth  = is_null($width) ? 'unset' : $width;
        $this->galleryHeight = is_null($height) ? 'unset' : $height;
    }
    
    /**
     * Enable popover
     * @param $title Title
     * @param $content Content
     */
    public function enablePopover($title = null, $content = '')
    {
        $this->popover    = TRUE;
        $this->poptitle   = $title;
        $this->popcontent = $content;
    }
    
    /**
     * Define the display mode {file}
     */
    public function setDisplayMode($mode)
    {
        $this->displayMode = $mode;
    }
    
    /**
     * Define the service class for response
     */
    public function setService($service)
    {
        $this->uploaderClass = $service;
    }
    
    /**
     * Define the allowed extensions
     */
    public function setAllowedExtensions($extensions)
    {
        $this->extensions = $extensions;
        $this->tag->{'accept'} = '.' . implode(',.', $extensions);
    }
    
    /**
     * Define to file handling
     */
    public function enableFileHandling()
    {
        $this->fileHandling = TRUE;
    }
    
    /**
     * Set place holder
     */
    public function setPlaceHolder(TElement $widget)
    {
        $this->placeHolder = $widget;
    }
    
    /**
     * Set field size
     */
    public function setSize($width, $height = NULL)
    {
        $this->size   = $width;
    }
    
    /**
     * Set field height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }
    
    /**
     * Return the post data
     */
    public function getPostData()
    {
        $name = str_replace(['[',']'], ['',''], $this->name);
        
        if (isset($_POST[$name]))
        {
            return $_POST[$name];
        }
    }
    
    /**
     * Set field value
     */
    public function setValue($value)
    {
        if ($this->fileHandling)
        {
            if (strpos($value, '%7B') === false)
            {
                if (!empty($value))
                {
                    $this->value = urlencode(json_encode(['fileName'=>$value]));
                }
            }
            else
            {
                $value_object = json_decode(urldecode($value));
                
                if (!empty($value_object->{'delFile'}) AND $value_object->{'delFile'} == $value_object->{'fileName'})
                {
                    $value = '';
                }
                
                parent::setValue($value);
            }
        }
        else
        {
            parent::setValue($value);
        }
    }
    
    /**
     * Show the widget at the screen
     */
    public function show()
    {
        // define the tag properties
        $this->tag->{'id'}       = $this->id;
        $this->tag->{'name'}     = 'file_' . $this->name;  // tag name
        $this->tag->{'receiver'} = $this->name;  // tag name
        $this->tag->{'value'}    = $this->value; // tag value
        $this->tag->{'type'}     = 'file';       // input type
        
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
        
        if (!empty($this->height))
        {
            $this->setProperty('style', "height:{$this->height}px;", false); //aggregate style info
        }
        
        $hdFileName = new THidden($this->name);
        $hdFileName->setValue( $this->value );
        
        $complete_action = "'undefined'";
        
        // verify if the widget is editable
        if (parent::getEditable())
        {
            if (isset($this->completeAction))
            {
                if (!TForm::getFormByName($this->formName) instanceof TForm)
                {
                    throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
                }
                
                $string_action = $this->completeAction->serialize(FALSE);
                $complete_action = "function() { __adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback'); tfile_update_download_link('{$this->name}') }";
            }
        }
        else
        {
            // make the field read-only
            $this->tag->{'readonly'} = "1";
            $this->tag->{'type'} = 'text';
            $this->tag->{'class'} = 'tfield_disabled'; // CSS
        }
        
        $div = new TElement('div');
        $div->{'style'} = "display:inline;width:100%;";
        $div->{'id'} = 'div_file_'.mt_rand(1000000000, 1999999999);
        $div->{'class'} = 'div_file';
        
        $div->add( $hdFileName );
        if ($this->placeHolder)
        {
            $div->add( $this->tag );
            $div->add( $this->placeHolder );
            $this->tag->{'style'} = 'display:none';
        }
        else
        {
            $div->add( $this->tag );
        }
        
        if ($this->displayMode == 'file' AND file_exists($this->value))
        {
            $icon = TElement::tag('i', null, ['class' => 'fa fa-download']);
            $link = new TElement('a');
            $link->{'id'}     = 'view_'.$this->name;
            $link->{'href'}   = 'download.php?file='.$this->value;
            $link->{'target'} = 'download';
            $link->{'style'}  = 'padding: 4px; display: block';
            $link->add($icon);
            $link->add($this->value);
            $div->add( $link );
        }
        
        $div->show();
        
        if (empty($this->extensions))
        {
            $action = "engine.php?class={$this->uploaderClass}";
        }
        else
        {
            $hash = md5("{$this->seed}{$this->name}".base64_encode(serialize($this->extensions)));
            $action = "engine.php?class={$this->uploaderClass}&name={$this->name}&hash={$hash}&extensions=".base64_encode(serialize($this->extensions));
        }
        
        $fileHandling = $this->fileHandling ? '1' : '0';
        $imageGallery = json_encode(['enabled'=> $this->imageGallery ? '1' : '0', 'width' => $this->galleryWidth, 'height' => $this->galleryHeight]);
        $popover = json_encode(['enabled' => $this->popover ? '1' : '0', 'title' => $this->poptitle, 'content' => base64_encode($this->popcontent)]);
        
        TScript::create(" tfile_start( '{$this->tag-> id}', '{$div-> id}', '{$action}', {$complete_action}, $fileHandling, '$imageGallery', '$popover');");
    }
    
    /**
     * Define the action to be executed when the user leaves the form field
     * @param $action TAction object
     */
    function setCompleteAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->completeAction = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
    }
    
    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " tfile_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " tfile_disable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Clear the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function clearField($form_name, $field)
    {
        TScript::create( " tfile_clear_field('{$form_name}', '{$field}'); " );
    }
}