<?php
namespace Adianti\Widget\Dialog;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Util\AdiantiStringConversion;

use Exception;

/**
 * Toast
 *
 * @version    7.6
 * @package    widget
 * @subpackage dialog
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TToast
{
    /**
     * Class Constructor
     * @param $message Message to be shown
     */
    public static function show($type, $message, $place = 'bottom center', $icon = null)
    {
        if (in_array($type, ['show', 'info', 'success', 'warning', 'error']))
        {
            $message64 = base64_encode(AdiantiStringConversion::utf8Decode($message));
            TScript::create("__adianti_show_toast64('{$type}', '{$message64}', '{$place}', '{$icon}')");
        }
        else
        {
            throw new Exception(AdiantiCoreTranslator::translate('Invalid parameter (^1) in ^2', $type, __METHOD__));
        }
    }
}
