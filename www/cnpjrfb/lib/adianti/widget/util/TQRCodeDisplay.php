<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;

use Exception;

/**
 * QR CodeDisplay
 *
 * @version    8.6
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TQRCodeDisplay extends TElement
{
    private $value;
    private $height;
    
    /**
     * Class Constructor
     * @param  $value barcode content
     */
    public function __construct($value)
    {
        parent::__construct('img');
        
        $this->value = $value;
        $this->height = 400;
    }
    
    /**
     * Replace current value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    /**
     * Define the bar code height
     * @param $width Barcode height in pixels
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }
    
    /**
     * Returns the field value
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     *
     */
    public function show()
    {
        $renderer = new \BaconQrCode\Renderer\GDLibRenderer($this->height, 0);
        $writer   = new \BaconQrCode\Writer($renderer);
        
        if (!empty($this->value))
        {
            $this->{'src'} = 'data:image/png;base64,' . base64_encode($writer->writeString($this->value));
        }
        
        parent::show();
    }
}
