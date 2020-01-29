<?php
namespace Adianti\Log;

use Adianti\Log\TLogger;

/**
 * Register LOG in HTML files
 *
 * @version    7.1
 * @package    log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TLoggerXML extends TLogger
{
    /**
     * Writes an message in the LOG file
     * @param  $message Message to be written
     */
    public function write($message)
    {
        $level = 'Debug';
        
        $time = date("Y-m-d H:i:s");
        // define the LOG content
        $text = "<log>\n";
        $text.= "   <level>$level</level>\n";
        $text.= "   <time>$time</time>\n";
        $text.= "   <message>$message</message>\n";
        $text.= "</log>\n";
        // add the message to the end of file
        $handler = fopen($this->filename, 'a');
        fwrite($handler, $text);
        fclose($handler);
    }
}
