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
 * Signature Pad area
 *
 * @version    8.6
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TSignaturePad extends TField implements AdiantiWidgetInterface
{
    protected $id;
    protected $width;
    protected $height;
    protected $drawWidth;
    protected $drawHeight;
    protected $penColor;
    protected $bgColor;
    protected $thickness;
    protected $border;
    protected $base64;
    protected $fileHandling;
    protected $uploaderClass;
    protected $seed;
    protected $buttonLabel;
    protected $title;
    protected $imagePlaceholder;
    
    /**
     * Constructor method
     * @param $name input name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->id       = 'tsignaturepad_' . mt_rand(1000000, 9999999);
        $this->penColor = '#000000';
        $this->bgColor  = '#ffffff';
        $this->thickness = 1.5;
        $this->base64   = false;
        $this->fileHandling = false;
        
        $this->setSize('100%', 100);
        $this->setDrawSize(500,250);
        $this->uploaderClass = 'AdiantiUploaderService';
        $ini = AdiantiApplicationConfig::get();
        $this->seed = APPLICATION_NAME . ( !empty($ini['general']['seed']) ? $ini['general']['seed'] : 's8dkld83kf73kf094' );
        
        $this->buttonLabel = AdiantiCoreTranslator::translate('Send');
        $this->title = AdiantiCoreTranslator::translate('Signature area');

        // default placeholder
        $this->imagePlaceholder = new TImage('fa:file-signature image-placeholder');
    }

    /**
     * Define the service class for signature upload
     * @param $service Service class name
     */
    public function setService($service)
    {
        $this->uploaderClass = $service;
    }
    
    /**
     * Set pen style
     * @param $color Pen color
     * @param $thickness Pen Thickness
     */
    public function setPenStyle($color, $thickness)
    {
        $this->penColor = $color;
        $this->thickness = $thickness;
    }
    
    /**
     * Set image placeholder
     * @param $image TImage object
     */
    public function setImagePlaceholder(TImage $image)
    {
        $image->{'class'} .= ' image-placeholder';
        $this->imagePlaceholder = $image;
    }
    
    /**
     * Define usage base64
     */
    public function enableBase64()
    {
        $this->base64 = true;
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
     * Set draw area size
     *
     * @param px $width
     * @param px $height
     * @return void
     */
    public function setDrawSize($width, $height)
    {
        $this->drawWidth = $width;
        $this->drawHeight = $height;
    }
    
    /**
     * Enable json file handling
     */
    public function enableFileHandling()
    {
        $this->fileHandling = true;
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
     * Render component
     */
    public function show()
    {
        // main container
        $label = new TElement("label");
        $label->{'id'} = 'tsignaturepad_container_' . $this->name;
        $label->{'class'} = 'label_tsignaturepad';
        $label->{'style'} = "width: {$this->width}; height: {$this->height};";

        // hidden input
        $hidden = new TElement('input');
        $hidden->{'type'}  = 'hidden';
        $hidden->{'name'}  = $this->name;
        $hidden->{'id'}    = $this->id;
        $hidden->{'value'} = $this->value;
        
        $remover = new TElement('i');
        $remover->{'class'} = 'fa fa-trash-alt';

        $editar = new TElement('i');
        $editar->{'class'} = 'fa fa-pen';
        
        $actions = new THBox('div');
        $actions->{'class'} = 'tsignaturepad_actions';

        if(! $this->value) {
            $actions->{'style'} = 'display: none';
        }            
        
        // preview area
        $img = new TElement('img');
        $img->{'id'}    = 'tsignaturepad_image_' . $this->id;
        $img->{'class'} = 'img_tsignaturepad rounded tsignaturepad';
        
        if (parent::getEditable())
        {
            $actions->add($editar)->{'action'} = 'edit';
            $actions->add($remover)->{'action'} = 'remove';
        }
        else
        {
            $img->{'data-editable'} = 'false';
        }
        
        $src = '';
        
        if ($this->fileHandling && $this->value)
        {
            $file_data = json_decode(urldecode($this->value));
            
            if (!empty($file_data->fileName))
            {
                // Set src img
                $src = 'download.php?file=' . $file_data->fileName . '&v=' . uniqid();
            }
        }
        else if ($this->base64 && $this->value)
        {
            $src = $this->value;
        }
        else if ($this->value)
        {
            $src = $this->value;
        }
        
        if ($src)
        {
            $img->{'src'} = $src;
            $this->imagePlaceholder->{'style'} = 'display: none;';
            $label->{'style'} .= "background:white";
        }
        
        // add elements
        $label->add($img);
        $label->add($actions);
        $label->add($hidden);
        $label->add($this->imagePlaceholder);

        $label->show();

        // js parameters
        $opts = [
            'field'        => $this->id,
            'title'        => $this->title,
            'buttonLabel'  => $this->buttonLabel,
            'serviceAction'=> 'engine.php?class='.$this->uploaderClass.'&seed='.$this->seed,
            'fileHandling' => $this->fileHandling,
            'base64'       => $this->base64,
            'config'       => [
                'penColor' => $this->penColor,
                'bgColor'  => $this->bgColor,
                'thickness'=> $this->thickness,
                'drawWidth'=> $this->drawWidth,
                'drawHeight'=> $this->drawHeight
            ],
            'name'         => $this->name,
            'extension'    => 'image/png'
        ];

        $json = json_encode($opts);

        TScript::create("tsignaturepad_start({$json});");
    }
}
