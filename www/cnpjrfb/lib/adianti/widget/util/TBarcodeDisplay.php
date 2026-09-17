<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Core\AdiantiCoreTranslator;

use Exception;

/**
 * Barcode Display
 *
 * @version    8.6
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TBarcodeDisplay extends TElement
{
    private $value;
    private $type;
    private $height;
    
    /**
     * Class Constructor
     * @param  $value barcode content
     */
    public function __construct($value)
    {
        parent::__construct('img');
        
        $this->value = $value;
        $this->height = 100;
        $this->type = 'code128';
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
     * Define the bar code type
     * @param $type Barcode type ('ean13', 'code128', 'code39', 'inter25', 'codabar', 'upca', 'pharma')
     */
    public function setType($type)
    {
        $this->type = $type;
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
        if (!empty($this->value))
        {
            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $generator->useGd();
            
            $types = [];
            $types['ean13'] = $generator::TYPE_EAN_13;
            $types['code128'] = $generator::TYPE_CODE_128;
            $types['code39'] = $generator::TYPE_CODE_39;
            $types['inter25'] = $generator::TYPE_INTERLEAVED_2_5;
            $types['codabar'] = $generator::TYPE_CODABAR;
            $types['upca'] = $generator::TYPE_UPC_A;
            $types['pharma'] = $generator::TYPE_PHARMA_CODE;
            
            if (!isset($types[$this->type]))
            {
                throw new Exception(AdiantiCoreTranslator::translate('Unsupported type') . ': ' . $this->type);
            }
            
            $this->{'src'} = 'data:image/png;base64,' . base64_encode($generator->getBarcode($this->value, $types[$this->type], 5, $this->height));
        }
        
        parent::show();
    }
}
