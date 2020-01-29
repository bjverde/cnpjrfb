<?php
namespace Adianti\Core;

/**
 * Application config
 *
 * @version    7.1
 * @package    core
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class AdiantiApplicationConfig
{
    private static $config;
    
    /**
     * Load configuration from array
     */
    public static function load($config)
    {
        if (is_array($config))
        {
            self::$config = $config;
        }
    }
    
    /**
     * Apply some configurations that change env vars
     */
    public static function apply()
    {
        if (!empty(self::$config['general']['debug']) && self::$config['general']['debug'] == '1')
        {
            ini_set('display_errors', '1');
            ini_set('error_reporting', E_ALL);
            ini_set("html_errors", 1); 
            ini_set("error_prepend_string", "<pre>"); 
            ini_set("error_append_string ", "</pre>"); 
        }
    }
    
    /**
     * Export configuration
     */
    public static function get()
    {
        return self::$config;
    }
}