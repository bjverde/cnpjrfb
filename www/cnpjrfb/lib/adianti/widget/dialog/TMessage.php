<?php
namespace Adianti\Widget\Dialog;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TAction;
use Adianti\Widget\Base\TScript;

/**
 * Message Dialog
 *
 * @version    7.6
 * @package    widget
 * @subpackage dialog
 * @author     Pablo Dall'Oglio
 * @author     Victor Feitoza <vfeitoza [at] gmail.com> (process action after OK)
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TMessage
{
    /**
     * Class Constructor
     * @param $type    Type of the message (info, warning, error)
     * @param $message Message to be shown
     * @param $action  Action to be processed when closing the dialog
     * @param $title_msg  Dialog Title
     */
    public function __construct($type, $message, TAction $action = NULL, $title_msg = '')
    {
        if (!empty($title_msg))
        {
            $title = $title_msg;
        }
        else
        {
            $titles = [];
            $titles['info']    = AdiantiCoreTranslator::translate('Information');
            $titles['error']   = AdiantiCoreTranslator::translate('Error');
            $titles['warning'] = AdiantiCoreTranslator::translate('Warning');
            $title = !empty($titles[$type])? $titles[$type] : '';
        }
        
        $callback = 'undefined';
        
        if ($action)
        {
            $callback = "function () { __adianti_load_page('{$action->serialize()}') }";
        }
        
        $title = addslashes((string) $title);
        $message = addslashes((string) $message);
        
        if ($type == 'info')
        {
            TScript::create("__adianti_message('{$title}', '{$message}', $callback)");
        }
        else if ($type == 'warning')
        {
            TScript::create("__adianti_warning('{$title}', '{$message}', $callback)");
        }
        else
        {
            TScript::create("__adianti_error('{$title}', '{$message}', $callback)");
        }
    }
}
