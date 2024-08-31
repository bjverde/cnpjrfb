<?php
namespace Adianti\Control;

use Adianti\Core\AdiantiCoreApplication;
use Adianti\Core\AdiantiCoreTranslator;
use Exception;
use ReflectionMethod;

/**
 * Structure to encapsulate an action
 *
 * @version    7.6
 * @package    control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TAction
{
    protected $action;
    protected $param;
    protected $properties;
    
    /**
     * Class Constructor
     * @param $action Callback to be executed
     * @param $parameters = array of parameters
     */
    public function __construct($action, $parameters = null)
    {
        $this->action = $action;
        
        if (is_object($this->action[0]))
        {
            $this->action[0] = get_class($this->action[0]);
        }
        
        if (!$this->validate($action))
        {
            $action_string = $this->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Method ^1 must receive a parameter of type ^2', __METHOD__, 'Callback'). ' <br> '.
                                AdiantiCoreTranslator::translate('Check if the action (^1) exists', $action_string));
        }
        
        if (!empty($parameters))
        {
            // does not override the action
            unset($parameters['class']);
            unset($parameters['method']);
            
            $this->param = $parameters;
        }
    }
    
    /**
     *
     */
    public function cloneWithParameters($parameters = [])
    {
        $clone = clone $this;
        
        if ($parameters)
        {
            foreach ($parameters as $key => $value)
            {
                $clone->setParameter($key, $value);
            }
        }
        
        return $clone;
    }
    
    /**
     * Return fields used in parameters
     */
    public function getFieldParameters()
    {
        $field_parameters = [];
        
        if ($this->param)
        {
            foreach ($this->param as $parameter)
            {
                if (substr($parameter,0,1) == '{' && substr($parameter,-1) == '}')
                {
                    $field_parameters[] = substr($parameter,1,-1);
                }
            }
        }
        
        return $field_parameters;
    }
    
    /**
     * Returns the action as a string
     */
    public function toString()
    {
        $action_string = '';
        if (is_string($this->action))
        {
            $action_string = $this->action;
        }
        else if (is_array($this->action))
        {
            if (is_object($this->action[0]))
            {
                $action_string = get_class($this->action[0]) . '::' . $this->action[1];
            }
            else
            {
                $action_string = $this->action[0] . '::' . $this->action[1];
            }
        }
        return $action_string;
    }
    
    /**
     * Adds a parameter to the action
     * @param  $param = parameter name
     * @param  $value = parameter value
     */
    public function setParameter($param, $value)
    {
        $this->param[$param] = $value;
    }
    
    /**
     * Set the parameters for the action
     * @param  $parameters = array of parameters
     */
    public function setParameters($parameters)
    {
        // does not override the action
        unset($parameters['class']);
        unset($parameters['method']);
        unset($parameters['static']);
        
        $this->param = $parameters;
    }
    
    /**
     * Returns a parameter
     * @param  $param = parameter name
     */
    public function getParameter($param)
    {
        if (isset($this->param[$param]))
        {
            return $this->param[$param];
        }
        return NULL;
    }
    
    /**
     * Return the Action Parameters
     */
    public function getParameters()
    {
        return $this->param;
    }
    
    /**
     * Returns the current calback
     */
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * Set property
     */
    public function setProperty($property, $value)
    {
        $this->properties[$property] = $value;
    }
    
    /**
     * Get property
     */
    public function getProperty($property)
    {
        return $this->properties[$property] ?? null;
    }
    
    /**
     * Prepare action for use over an object
     * @param $object Data Object
     */
    public function prepare($object)
    {
        $parameters = $this->param;
        $action     = clone $this;
        
        if ($parameters)
        {
            if (isset($parameters['*']))
            {
                unset($parameters['*']);
                unset($action->param['*']);
                
                foreach ($object as $attribute => $value)
                {
                    if (is_scalar($value))
                    {
                        $parameters[$attribute] = $value;
                    }
                }
            }
            
            foreach ($parameters as $parameter => $value)
            {
                // replace {attribute}s
                $action->setParameter($parameter, $this->replace($value, $object) );
            }
        }
        
        return $action;
    }
    
    /**
     * Replace a string with object properties within {pattern}
     * @param $content String with pattern
     * @param $object  Any object
     */
    private function replace($content, $object)
    {
        if (preg_match_all('/\{(.*?)\}/', $content, $matches) )
        {
            foreach ($matches[0] as $match)
            {
                $property = substr($match, 1, -1);
                
                if (strpos($property, '->') !== FALSE)
                {
                    $parts = explode('->', $property);
                    $container = $object;
                    foreach ($parts as $part)
                    {
                        if (is_object($container))
                        {
                            $result = $container->$part;
                            $container = $result;
                        }
                        else
                        {
                            throw new Exception(AdiantiCoreTranslator::translate('Trying to access a non-existent property (^1)', $property));
                        }
                    }
                    $content = $result;
                }
                else
                {
                    $value    = isset($object->$property)? $object->$property : null;
                    $content  = str_replace($match, (string) $value, $content);
                }
            }
        }
        
        return $content;
    }
    
    /**
     * Keep REQUEST parameters
     */
    public function preserveRequestParameters($request_parameters = [])
    {
        if ($request_parameters)
        {
            foreach ($request_parameters as $request_parameter)
            {
                if (isset($_REQUEST[$request_parameter]))
                {
                    $this->setParameter($request_parameter, $_REQUEST[$request_parameter]);
                }
            }
        }
    }
    
    /**
     * Converts the action into an URL
     * @param  $format_action = format action with document or javascript (ajax=no)
     */
    public function serialize($format_action = TRUE, $check_permission = FALSE)
    {
        // check if the callback is a method of an object
        if (is_array($this->action))
        {
            // get the class name
            $url['class'] = is_object($this->action[0]) ? get_class($this->action[0]) : $this->action[0];
            // get the method name
            $url['method'] = $this->action[1];
            
            if (isset($_GET['register_state']) AND $_GET['register_state'] == 'false' AND empty($this->param['register_state']))
            {
                $url['register_state'] = 'false';
            }
            
            if (isset($_GET['target_container']) AND !empty($_GET['target_container']) AND empty($this->param['target_container']) AND ($_GET['target_container'] !== 'adianti_div_content'))
            {
                $url['target_container'] = $_GET['target_container'];
            }
            
            if ($this->isStatic())
            {
                $url['static'] = '1';
            }
        }
        // otherwise the callback is a function
        else if (is_string($this->action))
        {
            // get the function name
            $url['method'] = $this->action;
        }
        
        // check if there are parameters
        if ($this->param)
        {
            $url = array_merge($url, $this->param);
        }
        
        if ($check_permission)
        {
            if ($action_verification = AdiantiCoreApplication::getActionVerification())
            {
                if (!$action_verification($url['class'], $url['method']))
                {
                    return '#disabled';
                }
            }
        }
        
        if ($format_action)
        {
            if ($router = AdiantiCoreApplication::getRouter())
            {
                return $router(http_build_query($url));
            }
            else
            {
                return 'index.php?'.http_build_query($url);
            }
        }
        else
        {
            //return http_build_query($url);
            if ($router = AdiantiCoreApplication::getRouter())
            {
                return $router(http_build_query($url), FALSE);
            }
            else
            {
                return http_build_query($url);
            }
        }
    }
    
    /**
     * Validate action
     */
    public function validate()
    {
        $class = is_string($this->action[0])? $this->action[0]: get_class($this->action[0]);
        
        if (class_exists($class))
        {
            $method = $this->action[1];
            
            if (method_exists($class, $method))
            {
                return TRUE;
            }
        }
        
        return FALSE;
    }
    
    /**
     * Returns if the action is static
     */
    public function isStatic()
    {
        if (is_array($this->action))
        {
            $class = is_string($this->action[0])? $this->action[0]: get_class($this->action[0]);
            
            if (class_exists($class))
            {
                $method = $this->action[1];
                
                if (method_exists($class, $method))
                {
                    $rm = new ReflectionMethod( $class, $method );
                    return $rm-> isStatic () || (isset($this->param['static']) && $this->param['static'] == '1');
                }
            }
        }
        return FALSE;
    }
}
