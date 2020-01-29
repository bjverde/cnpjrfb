<?php
namespace Adianti\Widget\Dialog;

use Adianti\Widget\Base\TElement;

/**
 * Alert
 *
 * @version    7.1
 * @package    widget
 * @subpackage dialog
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TAlert extends TElement
{
    /**
     * Class Constructor
     * @param $type    Type of the alert (success, info, warning, danger)
     * @param $message Message to be shown
     */
    public function __construct($type, $message)
    {
        parent::__construct('div');
        $this->{'class'} = 'talert alert alert-dismissible alert-'.$type;
        $this->{'role'}  = 'alert';
        
        $button = new TElement('button');
        $button->{'type'} = 'button';
        $button->{'class'} = 'close';
        $button->{'data-dismiss'} = 'alert';
        $button->{'aria-label'}   = 'Close';
        
        $span = new TElement('span');
        $span->{'aria-hidden'} = 'true';
        $span->add('&times;');
        $button->add($span);
        
        parent::add($button);
        parent::add($message);
    }
}
