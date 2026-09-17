<?php
namespace Adianti\Control;

use Exception;
use ReflectionClass;

/**
 * AdiantiPageControlTrait
 *
 * @version    8.6
 * @package    control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
trait AdiantiPageControlTrait
{
    /**
     * Interprets an action based at the URL parameters
     */
    public function run()
    {
        if ($_GET)
        {
            $class  = isset($_GET['class'])  ? $_GET['class']  : NULL;
            $method = isset($_GET['method']) ? $_GET['method'] : NULL;
            
            if ($class)
            {
                $object = ($class == get_class($this)) ? $this : new $class;
                if (is_callable(array($object, $method) ) )
                {
                    call_user_func(array($object, $method), $_REQUEST);
                }
            }
            else if (function_exists($method))
            {
                call_user_func($method, $_REQUEST);
            }
        }
    }
    
    /**
     * Set page name
     */
    public function setPageName($name)
    {
        $this->setProperty('page-name', $name);
        $this->setProperty('page_name', $name);
    }
    
    /**
     * Set page type
     */
    public function setPageType($type)
    {
        $this->setProperty('page-type', $type);
    }
    
    /**
     * Return the Page name
     */
    public function getClassName()
    {
        $rc = new ReflectionClass( $this );
        return $rc-> getShortName ();
    }
    
    /**
     * Define if the element is wrapped inside another one
     * @param @bool Boolean TRUE if is wrapped
     */
    public function setIsWrapped($bool)
    {
        parent::setIsWrapped($bool);
    }
    
    /**
     * Intercepts whenever someones assign a new property's value
     * @param $name     Property Name
     * @param $value    Property Value
     */
    public function __set($name, $value)
    {
        parent::__set($name, $value);
        $this->$name = $value;
    }
    
    /**
     * Call method only if exists
     */
    public function callIfExists($method, $param)
    {
        if (method_exists($this, $method))
        {
            $this->$method($param);
        }
    }
}
