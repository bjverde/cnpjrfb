<?php
namespace Adianti\Log;

use Adianti\Log\TLogger;

/**
 * Register LOG for Template Debugger
 *
 * @version    8.6
 * @package    log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TLoggerTPL extends TLogger
{
    /**
     * Writes an message in the LOG file
     * @param  $message Message to be written
     */
    public function write($message)
    {
        a_dump($message);
    }
}
