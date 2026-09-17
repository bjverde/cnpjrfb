<?php
namespace Adianti\Core;

use Adianti\Util\AdiantiStringConversion;

/**
 * Application config
 *
 * @version    8.6
 * @package    core
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class AdiantiApplicationConfig
{
    private static $config;
    
    /**
     * Start and apply all configuration
     */
    public static function start()
    {
        self::validateExtensions();
        
        if (file_exists('app/config/application.ini'))
        {
            $ini = parse_ini_file('app/config/application.ini', true);
        }
        else if (file_exists('app/config/application.php'))
        {
            $ini = require 'app/config/application.php';
        }
        else
        {
            die('Application configuration file not found');
        }
        
        $session_name = AdiantiStringConversion::slug($ini['general']['application'], '');
        
        date_default_timezone_set($ini['general']['timezone']);
        AdiantiCoreTranslator::setLanguage( $ini['general']['language'] );
        \ApplicationTranslator::setLanguage( $ini['general']['language'] );
        
        // custom session name
        session_name('PHPSESSID_'.$session_name);
        define('APPLICATION_NAME', $session_name);
        define('OS', strtoupper(substr(PHP_OS, 0, 3)));
        define('LANG', \ApplicationTranslator::getLanguage());
        
        self::load($ini);
        self::apply();
        
        \AdiantiHelpers::register();
    }
    
    /**
     *
     */
    public static function validateExtensions()
    {
        $core_extensions = ['mbstring', 'xml', 'SimpleXML'];
        foreach ($core_extensions as $extension)
        {
            if (!extension_loaded($extension))
            {
                die('Extension not loaded: '.$extension);
            }
        }
    }
    
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