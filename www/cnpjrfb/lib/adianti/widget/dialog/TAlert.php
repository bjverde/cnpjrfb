<?php
namespace Adianti\Widget\Dialog;

use Adianti\Widget\Base\TElement;

/**
 * Alert
 *
 * @version    8.6
 * @package    widget
 * @subpackage dialog
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TAlert extends TElement
{
    /**
     * Class Constructor
     * @param $type    Type of the alert (success, info, warning, danger)
     * @param $message Message to be shown
     */
    public function __construct($type, $message, $with_close_button = true)
    {
        parent::__construct('div');
        $this->{'class'} = 'talert alert alert-dismissible fade show alert-'.$type;
        $this->{'role'}  = 'alert';
        
        $button = new TElement('button');
        $button->{'type'} = 'button';
        $button->{'class'} = 'btn-close';
        $button->{'data-dismiss'} = 'alert';
        $button->{'data-bs-dismiss'} = 'alert';
        $button->{'aria-label'}   = 'Close';
        
        if ($with_close_button)
        {
            parent::add($button);
        }
        parent::add($message);
    }
}
