<?php
namespace Adianti\Core;

use Adianti\Core\AdiantiApplicationLoader;
use Adianti\Core\AdiantiClassMap;

/**
 * Framework class autoloader
 *
 * @version    7.4
 * @package    core
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class AdiantiCoreLoader
{
    private static $classMap;
    
    /**
     * Load the class map
     */
    public static function loadClassMap()
    {
        self::$classMap = AdiantiClassMap::getMap();
        $aliases = AdiantiClassMap::getAliases();
        
        if ($aliases)
        {
            foreach ($aliases as $old_class => $new_class)
            {
                if (class_exists($new_class))
                {
                    class_alias($new_class, $old_class);
                }
            }
        }
    }
    
    /**
     * Define the class path
     * @param $class Class name
     * @param $path  Class path
     */
    public static function setClassPath($class, $path)
    {
        self::$classMap[$class] = $path;
    }
    
    /**
     * Core autloader
     * @param $className Class name
     */
    public static function autoload($className)
    {
        $className = ltrim($className, '\\');
        $fileName  = '';
        $namespace = '';
        if (strrpos($className, '\\') !== FALSE)
        {
            $pieces    = explode('\\', $className);
            $className = array_pop($pieces);
            $namespace = implode('\\', $pieces);
        }
        $fileName = 'lib'.'\\'.strtolower($namespace).'\\'.$className.'.php';
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $fileName);
        
        if (file_exists($fileName))
        {
            //echo "PSR: $className <br>";
            require_once $fileName;
            self::globalScope($className);
        }
        else
        {
            if (!self::legacyAutoload($className))
            {
                if (!AdiantiApplicationLoader::autoload($className))
                {
                    if (file_exists('vendor/autoload_extras.php'))
                    {
                        require_once 'vendor/autoload_extras.php';
                    }
                }
            }
        }
    }
    
    /**
     * autoloader
     * @param $class classname
     */
    public static function legacyAutoload($class)
    {
        if (isset(self::$classMap[$class]))
        {
            if (file_exists(self::$classMap[$class]))
            {
                //echo 'Classmap '.self::$classMap[$class] . '<br>';
                require_once self::$classMap[$class];
                
                self::globalScope($class);
                return TRUE;
            }
        }
    }
    
    /**
     * make a class global
     */
    public static function globalScope($class)
    {
        if (isset(self::$classMap[$class]) AND self::$classMap[$class])
        {
            if (!class_exists($class, FALSE))
            {
                $ns = self::$classMap[$class];
                $ns = str_replace('/', '\\', $ns);
                $ns = str_replace('lib\\adianti', 'Adianti', $ns);
                $ns = str_replace('.class.php', '', $ns);
                $ns = str_replace('.php', '', $ns);
                
                //echo "&nbsp;&nbsp;&nbsp;&nbsp;Mapping: $ns, $class<br>";
                if (class_exists($ns) OR interface_exists($ns))
                {
                    class_alias($ns, $class, FALSE);
                }
            }
        }
    }
}
