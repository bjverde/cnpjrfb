<?php
namespace Adianti\Control;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;

use Exception;
use ReflectionClass;

/**
 * Page Controller Pattern: used as container for all elements inside a page and also as a page controller
 *
 * @version    7.6
 * @package    control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
#[\AllowDynamicProperties]
class TPage extends TElement
{
    private $body;
    private $constructed;
    private static $loadedjs;
    private static $loadedcss;
    private static $registeredcss;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct('div');
        $this->constructed = TRUE;
        
        $this->{'page-name'} = $this->getClassName();
        $this->{'page_name'} = $this->getClassName();
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
     * Return the Page name
     */
    public function getClassName()
    {
        $rc = new ReflectionClass( $this );
        return $rc-> getShortName ();
    }
    
    /**
     * Change page title
     */
	public static function setPageTitle($title)
    {
    	TScript::create("document.title='{$title}';");
    }
    
    /**
     * Set target container for page content
     */
    public function setTargetContainer($container)
    {
        if ($container)
        {
            $this->setProperty('adianti_target_container', $container);
            $this->{'class'} = 'container-part';
        }
        else
        {
            unset($this->{'adianti_target_container'});
            unset($this->{'class'});
        }
    }
    
    /**
     * Return target container
     */
    public function getTargetContainer()
    {
        return $this->{'adianti_target_container'};
    }
    
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
     * Include a specific JavaScript function to this page
     * @param $js JavaScript location
     */
    public static function include_js($js)
    {
        self::$loadedjs[$js] = TRUE;
    }
    
    /**
     * Include a specific Cascading Stylesheet to this page
     * @param $css  Cascading Stylesheet 
     */
    public static function include_css($css)
    {
        self::$loadedcss[$css] = TRUE;
    }
    
    /**
     * Register a specific Cascading Stylesheet to this page
     * @param $cssname  Cascading Stylesheet Name
     * @param $csscode  Cascading Stylesheet Code
     */
    public static function register_css($cssname, $csscode)
    {
        self::$registeredcss[$cssname] = $csscode;
    }
    
    /**
     * Open a File Dialog
     * @param $file File Name
     */
    public static function openFile($file, $basename = null)
    {
        TScript::create("__adianti_download_file('{$file}', '{$basename}')");
    }
    
    /**
     * Open a page in new tab
     */
    public static function openPage($page)
    {
        TScript::create("__adianti_open_page('{$page}');");
    }
    
    /**
     * Return the loaded Cascade Stylesheet files
     * @ignore-autocomplete on
     */
    public static function getLoadedCSS()
    {
        $css = self::$loadedcss;
        $csc = self::$registeredcss;
        $css_text = '';
        
        if ($css)
        {
            foreach ($css as $cssfile => $bool)
            {
                $css_text .= "    <link rel='stylesheet' type='text/css' media='screen' href='$cssfile'/>\n";
            }
        }
        
        if ($csc)
        {
            $css_text .= "    <style type='text/css' media='screen'>\n";
            foreach ($csc as $cssname => $csscode)
            {
                $css_text .= $csscode;
            }
            $css_text .= "    </style>\n";
        }
        
        return $css_text;
    }
    
    /**
     * Return the loaded JavaScript files
     * @ignore-autocomplete on
     */
    public static function getLoadedJS()
    {
        $js = self::$loadedjs;
        $js_text = '';
        if ($js)
        {
            foreach ($js as $jsfile => $bool)
            {
                $js_text .= "    <script language='JavaScript' src='$jsfile'></script>\n";;
            }
        }
        return $js_text;
    }
    
    /**
     * Discover if the browser is mobile device
     */
    public static function isMobile()
    {
        $isMobile = FALSE;
        
        if (PHP_SAPI == 'cli')
        {
            return FALSE;
        }
        
        if (isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']))
        {
            $isMobile = TRUE;
        }
        
        $mobiBrowsers = array('android',   'audiovox', 'blackberry', 'epoc',
                              'ericsson', ' iemobile', 'ipaq',       'iphone', 'ipad', 
                              'ipod',      'j2me',     'midp',       'mmp',
                              'mobile',    'motorola', 'nitro',      'nokia',
                              'opera mini','palm',     'palmsource', 'panasonic',
                              'phone',     'pocketpc', 'samsung',    'sanyo',
                              'series60',  'sharp',    'siemens',    'smartphone',
                              'sony',      'symbian',  'toshiba',    'treo',
                              'up.browser','up.link',  'wap',        'wap',
                              'windows ce','htc');
                              
        foreach ($mobiBrowsers as $mb)
        {
            if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),$mb) !== FALSE)
            {
             	$isMobile = TRUE;
            }
        }
        
        return $isMobile;
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
     * Decide wich action to take and show the page
     */
    public function show()
    {
        // just execute run() from toplevel TPage's, not nested ones
        if (!$this->getIsWrapped())
        {
            $this->run();
        }
        parent::show();
        
        if (!$this->constructed)
        {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 constructor', __CLASS__ ) );
        }
    }
}
