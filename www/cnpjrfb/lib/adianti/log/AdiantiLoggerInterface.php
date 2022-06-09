<?php
namespace Adianti\Log;

/**
 * Log Interface
 *
 * @version    7.4
 * @package    log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
interface AdiantiLoggerInterface
{
    function write($message);
}
