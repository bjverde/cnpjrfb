<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Util\TImage;

/**
 * Image capture
 *
 * @version    8.6
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @author     Lucas Tomasi (up to version 7.5)
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
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
