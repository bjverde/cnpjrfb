<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Base\TElement;

/**
 * Form separator
 *
 * @version    7.6
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TFormSeparator extends TElement
{
    private $fontColor;
    private $separatorColor;
    private $fontSize;
    private $header;
    private $divisor;
    
    /**
     * Class Constructor
     * @param $text Separator title
     */
    public function __construct($text, $fontColor = '#333333', $fontSize = '16', $separatorColor = '#eeeeee')
    {
        parent::__construct('div');
        
        $this->fontColor = $fontColor;
        $this->separatorColor = $separatorColor;
        $this->fontSize = $fontSize;
        
        $this->header = new TElement('h4');
        $this->header->{'class'} = 'tseparator';
        $this->header->{'style'} = "font-size: {$this->fontSize}px; color: {$this->fontColor};";
        
        $this->divisor = new TElement('hr');
        $this->divisor->{'style'} = "border-bottom-color: {$this->separatorColor}";
        $this->divisor->{'class'} = 'tseparator-divisor';
        $this->header->add($text);

        $this->add($this->header);
        $this->add($this->divisor);
    }

    /**
     * Set font size
     * @param $size font size
     */
    public function setFontSize($size)
    {
        $this->fontSize = $size;
        $this->header->{'style'} = "font-size: {$this->fontSize}px; color: {$this->fontColor};";
    }
    
    /**
     * Set font color
     * @param $color font color
     */
    public function setFontColor($color)
    {
        $this->fontColor = $color;
        $this->header->{'style'} = "font-size: {$this->fontSize}px; color: {$this->fontColor};";
    }

    /**
     * Set separator color
     * @param $color separator color
     */
    public function setSeparatorColor($color)
    {
        $this->separatorColor = $color;
        $this->divisor->{'style'} = "border-top-color: {$this->separatorColor}";
    }
}
