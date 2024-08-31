<?php
namespace Adianti\Registry;

use SessionHandlerInterface;
use Adianti\Registry\AdiantiRegistryInterface;

/**
 * Session Data Handler
 *
 * @version    7.6
 * @package    registry
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TSession implements AdiantiRegistryInterface
{
    /**
     * Class Constructor
     */
    public function __construct(SessionHandlerInterface $handler = NULL, $path = NULL)
    {
        if ($path)
        {
            session_save_path($path);
        }
        
        if ($handler)
        {
            session_set_save_handler($handler, true);
        }
		
        // if there's no opened session
        if (!session_id())
        {
            session_start();
        }
    }
    
    /**
     * Returns if the service is active
     */
    public static function enabled()
    {
        if (!session_id())
        {
            return session_start();
        }
        return TRUE;
    }
    
    /**
     * Define the value for a variable
     * @param $var   Variable Name
     * @param $value Variable Value
     */
    public static function setValue($var, $value)
    {
        if (defined('APPLICATION_NAME'))
        {
            $_SESSION[APPLICATION_NAME][$var] = $value;
        }
        else
        {
            $_SESSION[$var] = $value;
        }
    }
    
    /**
     * Returns the value for a variable
     * @param $var Variable Name
     */
    public static function getValue($var)
    {
        if (defined('APPLICATION_NAME'))
        {
            if (isset($_SESSION[APPLICATION_NAME][$var]))
            {
                return $_SESSION[APPLICATION_NAME][$var];
            }
        }
        else
        {
            if (isset($_SESSION[$var]))
            {
                return $_SESSION[$var];
            }
        }
    }
    
    /**
     * Clear the value for a variable
     * @param $var   Variable Name
     */
    public static function delValue($var)
    {
        if (defined('APPLICATION_NAME'))
        {
            unset($_SESSION[APPLICATION_NAME][$var]);
        }
        else
        {
            unset($_SESSION[$var]);
        }
    }
    
    /**
     * Regenerate id
     */
    public static function regenerate()
    {
        session_regenerate_id();
    }
    
    /**
     * Clear session
     */
    public static function clear()
    {
        self::freeSession();
    }
    
    /**
     * Destroy the session data
     * Backward compatibility
     */
    public static function freeSession()
    {
        if (defined('APPLICATION_NAME'))
        {
            $_SESSION[APPLICATION_NAME] = array();
        }
        else
        {
            $_SESSION[] = array();
        }
    }
}
