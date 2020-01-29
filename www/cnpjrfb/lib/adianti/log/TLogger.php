<?php
namespace Adianti\Log;

/**
 * Provides an abstract interface to register LOG files
 *
 * @version    7.1
 * @package    log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
abstract class TLogger implements AdiantiLoggerInterface
{
    protected $filename; // path for LOG file
    
    /**
     * Class Constructor
     * @param  $filename path for LOG file
     */
    public function __construct($filename = NULL)
    {
        if ($filename)
        {
            $this->filename = $filename;
            // clear the file contents
            file_put_contents($filename, '');
        }
    }
    
    /**
     * Write abstract method
     * Must be declared in child classes
     */
    abstract function write($message);
}
