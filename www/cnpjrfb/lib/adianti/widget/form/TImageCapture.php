<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Util\TImage;

/**
 * Image capture
 *
 * @version    7.4
 * @package    widget
 * @subpackage form
 * @author     Lucas Tomasi
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TImageCapture extends TImageCropper
{
    public function __construct($name)
    {
        parent::__construct($name);
        // $this->enableFileHandling(TRUE);
        $this->enableWebCam(TRUE);
        $this->setImagePlaceholder(new TImage('fa:camera'));
    }
}
