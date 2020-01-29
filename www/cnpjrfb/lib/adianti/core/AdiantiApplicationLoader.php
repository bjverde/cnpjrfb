<?php
namespace Adianti\Core;

use Adianti\Widget\Dialog\TMessage;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

use Exception;

/**
 * Application loader
 *
 * @version    7.1
 * @package    core
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class AdiantiApplicationLoader
{
    public static function autoload($class)
    {
        // echo "&nbsp;&nbsp;App loader $class<br>";
        $folders = array();
        $folders[] = 'app/model';
        $folders[] = 'app/control';
        $folders[] = 'app/view';
        $folders[] = 'app/lib';
        $folders[] = 'app/helpers';
        $folders[] = 'app/service';
        
        // search in app root
        if (file_exists("{$class}.class.php"))
        {
            require_once "{$class}.class.php";
            return TRUE;
        }
        
        // search in app root
        if (file_exists("{$class}.php"))
        {
            require_once "{$class}.php";
            return TRUE;
        }
        
        foreach ($folders as $folder)
        {
            if (file_exists("{$folder}/{$class}.class.php"))
            {
                require_once "{$folder}/{$class}.class.php";
                return TRUE;
            }
            if (file_exists("{$folder}/{$class}.php"))
            {
                require_once "{$folder}/{$class}.php";
                return TRUE;
            }
            else if (file_exists("{$folder}/{$class}.iface.php"))
            {
                require_once "{$folder}/{$class}.iface.php";
                return TRUE;
            }
            else
            {
                try
                {
                    if (file_exists($folder))
                    {
                        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder),
                                                               RecursiveIteratorIterator::SELF_FIRST) as $entry)
                        {
                            if (is_dir($entry))
                            {
                                if (file_exists("{$entry}/{$class}.class.php"))
                                {
                                    require_once "{$entry}/{$class}.class.php";
                                    return TRUE;
                                }
                                else if (file_exists("{$entry}/{$class}.php"))
                                {
                                    require_once "{$entry}/{$class}.php";
                                    return TRUE;
                                }
                                else if (file_exists("{$entry}/{$class}.iface.php"))
                                {
                                    require_once "{$entry}/{$class}.iface.php";
                                    return TRUE;
                                }
                            }
                        }
                    }
                }
                catch(Exception $e)
                {
                    new TMessage('error', $e->getMessage());
                }
            }
        }
    }
}
