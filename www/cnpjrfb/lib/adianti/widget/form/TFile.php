<?php
namespace Adianti\Widget\Form;

use Adianti\Core\AdiantiApplicationConfig;
use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\THidden;

use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Service\AdiantiUploaderService;
use Exception;

/**
 * FileChooser widget
 *
 * @version    7.6
 * @package    widget
 * @subpackage form
 * @author     Nataniel Rabaioli
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TFile extends TField implements AdiantiWidgetInterface
{
    protected $id;
    protected $height;
    protected $completeAction;
    protected $errorAction;
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
    protected $limitSize;
    
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
        $this->popcontent = '';
        $this->tag->{'widget'} = 'tfile';
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
     * Define upload size limit
     * @param $limit Size limit MBs
     */
    public function setLimitUploadSize($limit)
    {
        $this->limitSize = $limit * 1024 * 1024;
    }

    /**
     * Define upload size limit
     */
    public function enablePHPFileUploadLimit()
    {
        $this->limitSize = AdiantiUploaderService::getMaximumFileUploadSize();
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
     * Disable file handling
     */
    public function disableFileHandling()
    {
        $this->fileHandling = FALSE;
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
        if (is_scalar($value))
        {
            if ($this->fileHandling)
            {
                if (strpos( (string) $value, '%7B') === false)
                {
                    if (!empty($value))
                    {
                        $this->value = urlencode(json_encode(['fileName'=>$value]));
                    }
                    else
                    {
                        $this->value = $value;
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
            if (strstr((string) $this->size, '%') !== FALSE)
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
        $error_action = "'undefined'";
        
        // verify if the widget is editable
        
        if (isset($this->completeAction) || isset($this->errorAction))
        {
            if (!TForm::getFormByName($this->formName) instanceof TForm)
            {
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
            }
        }
        
        if (isset($this->completeAction))
        {
            $string_action = $this->completeAction->serialize(FALSE);
            $complete_action = "function() { __adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback'); tfile_update_download_link('{$this->name}') }";
        }
        
        if (isset($this->errorAction))
        {
            $string_action = $this->errorAction->serialize(FALSE);
            $error_action = "function() { __adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback'); }";
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
        
        if ($router = AdiantiCoreApplication::getRouter())
        {
	        $action = $router($action, false);
        }

        $fileHandling = $this->fileHandling ? '1' : '0';
        $imageGallery = json_encode(['enabled'=> $this->imageGallery ? '1' : '0', 'width' => $this->galleryWidth, 'height' => $this->galleryHeight]);
        $popover = json_encode(['enabled' => $this->popover ? '1' : '0', 'title' => $this->poptitle, 'content' => base64_encode($this->popcontent)]);
        $limitSize = $this->limitSize ?? 'null';

        TScript::create(" tfile_start( '{$this->tag-> id}', '{$div-> id}', '{$action}', {$complete_action}, {$error_action}, $fileHandling, '$imageGallery', '$popover', {$limitSize});");

        if (!parent::getEditable())
        {
            TScript::create("tfile_disable_field('{$this->formName}', '{$this->name}');");
        }
    }
    
    /**
     * Define the action to be executed when upload is finished
     * @param $action TAction object
     */
    public function setCompleteAction(TAction $action)
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
     * Define the action to be executed when some error occurs
     * @param $action TAction object
     */
    public function setErrorAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->errorAction = $action;
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
