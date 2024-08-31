<?php
namespace Adianti\Core;

use ReflectionClass;
use ReflectionMethod;
use Exception;
use Error;
use ErrorException;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TPage;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Util\TExceptionView;

/**
 * Basic structure to run a web application
 *
 * @version    7.6
 * @package    core
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class AdiantiCoreApplication
{
    private static $router;
    private static $request_id;
    private static $debug;
    private static $action_verification;
    
    /**
     * Execute class/method based on request
     *
     * @param $debug Activate Exception debug
     */
    public static function run($debug = FALSE)
    {
        self::$request_id = uniqid();
        self::$debug = $debug;
        
        $ini = AdiantiApplicationConfig::get();
        $service = isset($ini['general']['request_log_service']) ? $ini['general']['request_log_service'] : '\SystemRequestLogService';
        $class   = isset($_REQUEST['class'])    ? $_REQUEST['class']   : '';
        $static  = isset($_REQUEST['static'])   ? $_REQUEST['static']  : '';
        $method  = isset($_REQUEST['method'])   ? $_REQUEST['method']  : '';
        
        $content = '';
        set_error_handler(array('AdiantiCoreApplication', 'errorHandler'));
        
        $time_start = microtime(true); 
        
        self::filterInput();
        
        $rc = new ReflectionClass($class); 
        
        if (in_array(strtolower($class), array_map('strtolower', AdiantiClassMap::getInternalClasses()) ))
        {
            ob_start();
            new TMessage( 'error', AdiantiCoreTranslator::translate('The internal class ^1 can not be executed', " <b><i><u>{$class}</u></i></b>") );
            $content = ob_get_contents();
            ob_end_clean();
        }
        else if (!$rc-> isUserDefined ())
        {
            ob_start();
            new TMessage( 'error', AdiantiCoreTranslator::translate('The internal class ^1 can not be executed', " <b><i><u>{$class}</u></i></b>") );
            $content = ob_get_contents();
            ob_end_clean();
        }
        else if (class_exists($class))
        {
            if ($static)
            {
                $rf = new ReflectionMethod($class, $method);
                if ($rf-> isStatic ())
                {
                    call_user_func(array($class, $method), $_REQUEST);
                }
                else
                {
                    call_user_func(array(new $class($_REQUEST), $method), $_REQUEST);
                }
            }
            else
            {
                try
                {
                    $page = new $class( $_REQUEST );
                    
                    ob_start();
                    $page->show( $_REQUEST );
	                $content = ob_get_contents();
	                ob_end_clean();
                }
                catch (Exception $e)
                {
                    ob_start();
                    if ($debug)
                    {
                        new TExceptionView($e);
                        $content = ob_get_contents();
                    }
                    else
                    {
                        new TMessage('error', $e->getMessage());
                        $content = ob_get_contents();
                    }
                    ob_end_clean();
                }
                catch (Error $e)
                {
                    
                    ob_start();
                    if ($debug)
                    {
                        new TExceptionView($e);
                        $content = ob_get_contents();
                    }
                    else
                    {
                        new TMessage('error', $e->getMessage());
                        $content = ob_get_contents();
                    }
                    ob_end_clean();
                }
            }
        }
        else if (!empty($class))
        {
            new TMessage('error', AdiantiCoreTranslator::translate('Class ^1 not found', " <b><i><u>{$class}</u></i></b>") . '.<br>' . AdiantiCoreTranslator::translate('Check the class name or the file name').'.');
        }
        
        if (!$static)
        {
            echo TPage::getLoadedCSS();
        }
        echo TPage::getLoadedJS();
        
        echo $content;
        
        $time_end = microtime(true);
        
        if (!empty($ini['general']['request_log']) && $ini['general']['request_log'] == '1')
        {
            if (empty($ini['general']['request_log_types']) || strpos($ini['general']['request_log_types'], 'web') !== false)
            {
                self::$request_id = $service::register( 'web', $time_end - $time_start);
            }
        }
    }
    
    /**
     * Execute internal method
     */
    public static function execute($class, $method, $request, $endpoint = null)
    {
        self::$request_id = uniqid();
        
        $ini = AdiantiApplicationConfig::get();
        $service = isset($ini['general']['request_log_service']) ? $ini['general']['request_log_service'] : '\SystemRequestLogService'; 
        
        $time_start = microtime(true);
        
        if (class_exists($class))
        {
            $rc = new ReflectionClass($class);
            
            if (in_array(strtolower($class), array_map('strtolower', AdiantiClassMap::getInternalClasses()) ))
            {
                throw new Exception(AdiantiCoreTranslator::translate('The internal class ^1 can not be executed', $class ));
            }
            else if (!$rc-> isUserDefined ())
            {
                throw new Exception(AdiantiCoreTranslator::translate('The internal class ^1 can not be executed', $class ));
            }
            
            if (method_exists($class, $method))
            {
                $rf = new ReflectionMethod($class, $method);
                if ($rf-> isStatic ())
                {
                    $response = call_user_func(array($class, $method), $request);
                }
                else
                {
                    $response = call_user_func(array(new $class($request), $method), $request);
                }
                
                $time_end = microtime(true);
                
                if (!empty($ini['general']['request_log']) && $ini['general']['request_log'] == '1')
                {
                    if (empty($endpoint) || empty($ini['general']['request_log_types']) || strpos($ini['general']['request_log_types'], $endpoint) !== false)
                    {
                        self::$request_id = $service::register( $endpoint, $time_end - $time_start );
                    }
                }
                
                return $response;
            }
            else
            {
                throw new Exception(AdiantiCoreTranslator::translate('Method ^1 not found', "$class::$method"));
            }
        }
        else
        {
            throw new Exception(AdiantiCoreTranslator::translate('Class ^1 not found', $class));
        }
    }
    
    /**
     * Filter specific framework commands
     */
    public static function filterInput()
    {
        if ($_REQUEST)
        {
            foreach ($_REQUEST as $key => $value)
            {
                if (is_scalar($value))
                {
                    if ( (substr(strtoupper($value),0,7) == '(SELECT') OR (substr(strtoupper($value),0,6) == 'NOESC:'))
                    {
                        $_REQUEST[$key] = '';
                        $_GET[$key]     = '';
                        $_POST[$key]    = '';
                    }
                }
                else if (is_array($value))
                {
                    foreach ($value as $sub_key => $sub_value)
                    {
                        if (is_scalar($sub_value))
                        {
                            if ( (substr(strtoupper($sub_value),0,7) == '(SELECT') OR (substr(strtoupper($sub_value),0,6) == 'NOESC:'))
                            {
                                $_REQUEST[$key][$sub_key] = '';
                                $_GET[$key][$sub_key]     = '';
                                $_POST[$key][$sub_key]    = '';
                            }
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Set router callback
     */
    public static function setRouter(Callable $callback)
    {
        self::$router = $callback;
    }
    
    /**
     * Get router callback
     */
    public static function getRouter()
    {
        return self::$router;
    }
    
    /**
     * Set action_verification callback
     */
    public static function setActionVerification(Callable $callback)
    {
        self::$action_verification = $callback;
    }
    
    /**
     * Get action_verification callback
     */
    public static function getActionVerification()
    {
        return self::$action_verification;
    }
    
    /**
     * Execute a specific method of a class with parameters
     *
     * @param $class class name
     * @param $method method name
     * @param $parameters array of parameters
     */
    public static function executeMethod($class, $method = NULL, $parameters = NULL)
    {
        self::gotoPage($class, $method, $parameters);
    }
    
    /**
     * Process request and insert the result it into template
     */
    public static function processRequest($template)
    {
        ob_start();
        AdiantiCoreApplication::run();
        $content = ob_get_contents();
        ob_end_clean();
        
        $template = str_replace('{content}', $content, $template);
        
        return $template;
    }
     
    /**
     * Goto a page
     *
     * @param $class class name
     * @param $method method name
     * @param $parameters array of parameters
     */
    public static function gotoPage($class, $method = NULL, $parameters = NULL, $callback = NULL)
    {
        unset($parameters['static']);
        $query = self::buildHttpQuery($class, $method, $parameters);
        
        TScript::create("__adianti_goto_page('{$query}');", true, 1);
    }
    
    /**
     * Load a page
     *
     * @param $class class name
     * @param $method method name
     * @param $parameters array of parameters
     */
    public static function loadPage($class, $method = NULL, $parameters = NULL)
    {
        $query = self::buildHttpQuery($class, $method, $parameters);
        
        TScript::create("__adianti_load_page('{$query}');", true, 1);
    }
    
    /**
     * Load a page url
     *
     * @param $class class name
     * @param $method method name
     * @param $parameters array of parameters
     */
    public static function loadPageURL($query)
    {
        TScript::create("__adianti_load_page('{$query}');", true, 1);
    }
    
    /**
     * Post data
     *
     * @param $class class name
     * @param $method method name
     * @param $parameters array of parameters
     */
    public static function postData($formName, $class, $method = NULL, $parameters = NULL)
    {
        $url = array();
        $url['class']  = $class;
        $url['method'] = $method;
        unset($parameters['class']);
        unset($parameters['method']);
        $url = array_merge($url, (array) $parameters);
        
        TScript::create("__adianti_post_data('{$formName}', '".http_build_query($url)."');");
    }
    
    /**
     * Build HTTP Query
     *
     * @param $class class name
     * @param $method method name
     * @param $parameters array of parameters
     */
    public static function buildHttpQuery($class, $method = NULL, $parameters = NULL)
    {
        $url = [];
        $url['class']  = $class;
        if ($method)
        {
            $url['method'] = $method;
        }
        
        if (!empty($parameters['class']) && $parameters['class'] !== $class)
        {
            $parameters['previous_class'] = $parameters['class'];
        }
        
        if (!empty($parameters['method']) && $parameters['method'] !== $method)
        {
            $parameters['previous_method'] = $parameters['method'];
        }
        
        unset($parameters['class']);
        unset($parameters['method']);
        $query = http_build_query($url);
        $callback = self::$router;
        $short_url = null;
        
        if ($callback)
        {
            $query  = $callback($query, TRUE);
        }
        else
        {
            $query = 'index.php?'.$query;
        }
        
        if (strpos($query, '?') !== FALSE)
        {
            return $query . ( (is_array($parameters) && count($parameters)>0) ? '&'.http_build_query($parameters) : '' );
        }
        else
        {
            return $query . ( (is_array($parameters) && count($parameters)>0) ? '?'.http_build_query($parameters) : '' );
        }
    }
    
    /**
     * Reload application
     */
    public static function reload()
    {
        TScript::create("__adianti_goto_page('index.php')");
    }
    
    /**
     * Register URL
     *
     * @param $page URL to be registered
     */
    public static function registerPage($page)
    {
        TScript::create("__adianti_register_state('{$page}', 'user');");
    }
    
    /**
     * Handle Catchable Errors
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if ( $errno === E_RECOVERABLE_ERROR )
        {
            throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
        }
        
        return false;
    }
    
    /**
     * Get request headers
     */
    public static function getHeaders()
    {
        $headers = array();
        foreach ($_SERVER as $key => $value)
        {
            if (substr($key, 0, 5) == 'HTTP_')
            {
                $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $headers[$header] = $value;
            }
        }
        
        if (function_exists('getallheaders'))
        {
            $allheaders = getallheaders();
            
            if ($allheaders)
            {
                return $allheaders;
            }
            
            return $headers;
        }
        return $headers;
    }
    
    /**
     * Returns the execution id
     */
    public static function getRequestId()
    {
        return self::$request_id;
    }
    
    /**
     * Returns the debug mode
     */
    public static function getDebugMode()
    {
        return self::$debug;
    }
}
