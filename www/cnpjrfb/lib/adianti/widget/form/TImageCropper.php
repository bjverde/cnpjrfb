<?php
namespace Adianti\Widget\Form;

use Adianti\Core\AdiantiApplicationConfig;
use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Container\THBox;
use Adianti\Widget\Util\TImage;

/**
 * Image uploader with cropper
 *
 * @version    7.6
 * @package    widget
 * @subpackage form
 * @author     Lucas Tomasi
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TImageCropper extends TField implements AdiantiWidgetInterface
{
    protected $height;
    protected $width;
    protected $value;
    private $extensions;
    private $fileHandling;
    private $base64;
    private $webcam;
    private $uploaderClass;
    private $seed;
    private $title;
    private $buttonText;
    private $cropWidth;
    private $cropHeight;
    private $aspectRatio;
    private $buttonRotate;
    private $buttonDrag;
    private $buttonScale;
    private $buttonReset;
    private $buttonZoom;

    private $imagePlaceholder;
    
    // defaults aspect ratios
    const CROPPER_RATIO_16_9 = 16/9;
    const CROPPER_RATIO_4_3 = 4/3;
    const CROPPER_RATIO_1_1 = 1/1;
    const CROPPER_RATIO_2_3 = 2/3;
    
    public function __construct($name)
    {
        parent::__construct($name);
        $this->id   = 'timagecropper_' . mt_rand(1000000000, 1999999999);
        $this->tag->{'type'}   = 'hidden';
        $this->tag->{'widget'} = 'timagecropper';
        $this->tag->{'name'} = $name;

        $this->buttonText = 'Ajustar';
        $this->title = 'Ajustar imagem';

        $this->uploaderClass = 'AdiantiUploaderService';
        $ini = AdiantiApplicationConfig::get();
        $this->seed = APPLICATION_NAME . ( !empty($ini['general']['seed']) ? $ini['general']['seed'] : 's8dkld83kf73kf094' );
        
        $this->extensions = ['gif', 'png', 'jpg', 'jpeg'];
        
        $this->cropWidth = null;
        $this->cropHeight = null;
        $this->buttonDrag = true;
        $this->buttonZoom = true;
        $this->aspectRatio = null;
        $this->buttonScale = true;
        $this->buttonReset = true;
        $this->buttonRotate = true;
        $this->fileHandling = false;
        $this->base64 = false;
        $this->webcam = false;
        $this->setSize('100%', 100);

        $this->imagePlaceholder = new TImage('fa:image placeholder');
    }

    /**
     * Set image placeholder
     *
     * @param TImage $image image placeholder
     */
    public function setImagePlaceholder(TImage $image)
    {
        $image->{'class'} .= ' placeholder';

        $this->imagePlaceholder = $image;
    }

    /**
     * Set window title
     *
     * @param String $title Window title
     */
    public function setWindowTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Set text button crop
     *
     * @param String $text Text button confirm
     */
    public function setButtonLabel($text)
    {
        $this->buttonText = $text;
    }

    /**
     * Define initial aspect ratio
     *
     * @param double $aspectRatio Aspect ratio crop image
     * @return void
     */
    public function setAspectRatio($aspectRatio)
    {
        $this->aspectRatio = $aspectRatio;
    }

    /**
     * Define usage base64
     */
    public function enableBase64()
    {
        $this->base64 = true;
    }
    
    public function enableWebCam()
    {
        $this->webcam = true;
    }

    /**
     * Define to file handling
     */
    public function enableFileHandling()
    {
        $this->fileHandling = true;
    }

    /**
     * Disable buttons drag move | resize
     */
    public function disableButtonsDrag()
    {
        $this->buttonDrag = false;
    }

    /**
     * Disable buttons zoom
     */
    public function disableButtonsZoom()
    {
        $this->buttonZoom = false;
    }

    /**
     * Disable buttons scale
     */
    public function disableButtonsScale()
    {
        $this->buttonScale = false;
    }

    /**
     * Disable button reset
     */
    public function disableButtonReset()
    {
        $this->buttonReset = false;
    }

    /**
     * Disable buttons rotates
     */
    public function disableButtonsRotate()
    {
        $this->buttonRotate = false;
    }

    /**
     * Define image initial
     *
     * @param String $data Image url or image base64
     */
    public function setValue($value)
    {
        if ($this->fileHandling)
        {
            if (strpos( (string) $value, '%7B') === false)
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
     * Define the allowed extensions
     */
    public function setAllowedExtensions($extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * Get the allowed extensions
     */
    public function getAllowedExtensions()
    {
        return $this->extensions;
    }

    /**
     * Define the service class for response
     */
    public function setService($service)
    {
        $this->uploaderClass = $service;
    }

    /**
     * Define the Field's width
     * @param double $width Field's width in pixels
     * @param double $height Field's heigth in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $width = (strstr($width, '%') !== FALSE) ? $width : "{$width}px";
        $height = (strstr($height, '%') !== FALSE) ? $height : "{$height}px";

        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Returns the field sizes
     */
    public function getSize()
    {
        return [
            str_replace('px', '', $this->width),
            str_replace('px', '', $this->height)
        ];
    }

    /**
     * Set image size after crop
     *
     * @param px $width
     * @param px $height
     * @return void
     */
    public function setCropSize($width, $height)
    {
        $this->cropWidth = $width;
        $this->cropHeight = $height;

        $this->setAspectRatio($this->cropWidth / $this->cropHeight);
    }

    /**
     * Return component specific options
     */
    public function getOptions()
    {
        return json_encode([
            'cropWidth' => $this->cropWidth,
            'cropHeight' => $this->cropHeight,
            'aspectRatio' => $this->aspectRatio,
            'enableButtonDrag' => $this->buttonDrag,
            'enableButtonScale' => $this->buttonScale,
            'enableButtonReset' => $this->buttonReset,
            'enableButtonZoom' => $this->buttonZoom,
            'enableButtonRotate' => $this->buttonRotate,
            'labels' =>  [
                'reset'       => AdiantiCoreTranslator::translate('Reset'),
                'scalex'      => AdiantiCoreTranslator::translate('Scale horizontal'),
                'scaley'      => AdiantiCoreTranslator::translate('Scale vertical'),
                'move'        => AdiantiCoreTranslator::translate('Move'),
                'crop'        => AdiantiCoreTranslator::translate('Crop'),
                'zoomin'      => AdiantiCoreTranslator::translate('Zoom in'),
                'zoomout'     => AdiantiCoreTranslator::translate('Zoom out'),
                'rotateright' => AdiantiCoreTranslator::translate('Rotate right'),
                'rotateleft'  => AdiantiCoreTranslator::translate('Rotate left'),
            ]
        ]);
    }
    
    /**
     * Show
     */
    public function show()
    {
        $label = new TElement("label");
        $label->{'id'} = 'timagecropper_container_' . $this->name;
        $label->{'class'} = 'label_timagecropper';
        $label->{'style'} = "width: {$this->width}; height: {$this->height};";

        $remover = new TElement('i');
        $remover->{'class'} = 'fa fa-trash-alt';

        $editar = new TElement('i');
        $editar->{'class'} = 'fa fa-pen';
        
        $actions = new THBox('div');
        $actions->{'class'} = 'timagecropper_actions';

        if(! $this->value) {
            $actions->{'style'} = 'display: none';
        }            
        
        $actions->add($editar)->{'action'} = 'edit';
        $actions->add($remover)->{'action'} = 'remove';
        
        $img = new TElement('img');
        $img->{'id'}    = 'timagecropper_' . $this->name;
        $img->{'class'} = 'img_imagecropper rounded timagecropper';
        $img->{'style'} = "max-width: {$this->width}; max-height: {$this->height};margin: auto;";

        $src = '';
        $fileName = '';
        $fileExtension = '';

        if ($this->fileHandling && $this->value)
        {
            $dados_file = json_decode(urldecode($this->value));
            
            if (!empty($dados_file->fileName))
            {
                // Get name and extension img
                $fileName = basename($dados_file->fileName);
                $fileExtension = pathinfo($dados_file->fileName)['extension'];
                
                // Set src img
                $src = 'download.php?file=' . $dados_file->fileName . '&v=' . uniqid();
            }
        }
        else if ($this->base64 && $this->value)
        {
            $encodedImgString = explode(',', $this->value, 2)[1];
            $decodedImgString = base64_decode($encodedImgString);
            $info = getimagesizefromstring($decodedImgString);
            $ext = explode('/', $info['mime'])[1];
            
            // Get name and extension img
            $fileName = uniqid().".{$ext}";
            $fileExtension = $ext;
            
            // Set src img
            $src = $this->value;
        }
        else if ($this->value)
        {
            // Get name and extension img
            $fileName = empty($this->value) ? '' : basename($this->value);
            $fileExtension = empty($this->value) ? '' : pathinfo($this->value)['extension'];
            
            // Set src img
            $src = $this->value;
        }
        
        if ($src)
        {
            $img->{'src'} = $src;
            $this->imagePlaceholder->{'style'} = 'display: none;';
        }            

        $this->tag->{'value'} = $this->value;

        $file = new TEntry('tfile_timagecropper_' . $this->name);
        $file->{'accept'} =  '.' . implode(',.', $this->extensions);
        $file->{'type'}   = 'file';
        $file->{'class' } = "sr-only";
        $file->{'id' }    = $file->getName();
        
        $hash = md5("{$this->seed}{$this->name}".base64_encode(serialize($this->extensions)));
        $action = "engine.php?class={$this->uploaderClass}&name={$this->name}&hash={$hash}&extensions=".base64_encode(serialize($this->extensions));

        if(parent::getEditable())
        {
            $label->add($file);
        }
        
        $label->add($img);
        $label->add($actions);
        $label->add($this->tag);
        $label->add($this->imagePlaceholder);

        $label->show();

        $options = $this->getOptions();

        $fileHandling = $this->fileHandling ? '1' : '0';
        $base64 = $this->base64 ? '1' : '0';
        $webcam = $this->webcam ? '1' : '0';

        TScript::create("timagecropper_start('{$this->name}', '{$this->title}', '{$this->buttonText}', '{$action}', {$fileHandling}, {$base64}, {$webcam}, {$options}, '{$fileName}', '{$fileExtension}');");
    }
}
