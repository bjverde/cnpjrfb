<?php
namespace Adianti\Log;

use Adianti\Log\TLogger;

/**
 * Register LOG in Standard Output
 *
 * @version    7.4
 * @package    log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TLoggerSTD extends TLogger
{
    /**
     * Writes an message in the LOG file
     * @param  $message Message to be written
     */
    public function write($message)
    {
        $level = 'Debug';
        
        $time = date("Y-m-d H:i:s");
        $eol = PHP_SAPI == 'cli' ? "\n" : '<br>';
        
        // define the LOG content
        print "$level: $time - $message" . $eol;
    }
}
