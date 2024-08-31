<?php
namespace Adianti\Control;

use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Container\TJQueryDialog;
use Adianti\Widget\Base\TScript;

use ReflectionClass;
use Exception;

/**
 * Window Container (JQueryDialog wrapper)
 *
 * @version    7.6
 * @package    control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TWindow extends TPage
{
    private $wrapper;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->wrapper = new TJQueryDialog;
        $this->wrapper->setUseOKButton(FALSE);
        $this->wrapper->setTitle('');
        $this->wrapper->setSize(1000, 500);
        $this->wrapper->setModal(TRUE);
        $this->wrapper->{'widget'} = 'T'.'Window';
        $this->wrapper->{'name'} = $this->getClassName();
        
        $this->{'id'} = 'window_' . mt_rand(1000000000, 1999999999);
        $this->{'window_name'} = $this->wrapper->{'name'};
        $this->{'role'} = 'window-wrapper';
        parent::add($this->wrapper);
    }
    
    /**
     *
     */
    public function setTargetContainer($container)
    {
        throw new Exception( AdiantiCoreTranslator::translate('Use of target containers along with windows is not allowed') );
    }
    
    
    /**
     * Returns ID
     */
    public function getId()
    {
        return $this->wrapper->getId();
    }
    
    /**
     * Create a window
     */
    public static function create($title, $width, $height, $params = null)
    {
        $inst = new static($params);
        $inst->setIsWrapped(TRUE);
        $inst->setTitle($title);
        $inst->setSize($width, $height);
        unset($inst->wrapper->{'widget'});
        return $inst;
    }
    
    /**
     * Remove padding
     */
    public function removePadding()
    {
        $this->setProperty('class', 'window_modal');
    }
    
    /**
     * Remove titlebar
     */
    public function removeTitleBar()
    {
        $this->setDialogClass('no-title');
    }
    
    /**
     * Set Dialog class
     * @param $class Class name
     */
    public function setDialogClass($class)
    {
        $this->wrapper->setDialogClass($class);
    }
    
    /**
     * Define the stack order (zIndex)
     * @param $order Stack order
     */
    public function setStackOrder($order)
    {
        $this->wrapper->setStackOrder($order);
    }
    
    /**
     * Define the window's title
     * @param  $title Window's title
     */
    public function setTitle($title)
    {
        $this->wrapper->setTitle($title);
    }
    
    /**
     * Turn on/off modal
     * @param $modal Boolean
     */
    public function setModal($modal)
    {
        $this->wrapper->setModal($modal);
    }
    
    /**
     * Disable Escape
     */
    public function disableEscape()
    {
        $this->wrapper->disableEscape();
    }
    
    /**
     * Disable scrolling
     */
    public function disableScrolling()
    {
        $this->wrapper->disableScrolling();
    }
    
    /**
     * Define the window's size
     * @param  $width  Window's width
     * @param  $height Window's height
     */
    public function setSize($width, $height)
    {
        $this->wrapper->setSize($width, $height);
    }
    
    /**
     * Define the window's min width between percent and absolute
     * @param  $percent width
     * @param  $absolute width
     */
    public function setMinWidth($percent, $absolute)
    {
        $this->wrapper->setMinWidth($percent, $absolute);
    }
    
    /**
     * Define the top corner positions
     * @param $x left coordinate
     * @param $y top  coordinate
     */
    public function setPosition($x, $y)
    {
        $this->wrapper->setPosition($x, $y);
    }
    
    /**
     * Define the Property value
     * @param $property Property name
     * @param $value Property value
     */
    public function setProperty($property, $value)
    {
        $this->wrapper->$property = $value;
    }
    
    /**
     * Add some content to the window
     * @param $content Any object that implements the show() method
     */
    public function add($content)
    {
        $this->wrapper->add($content);
    }
    
    /**
     * set close action
     * @param $action close action
     */
    public function setCloseAction(TAction $action)
    {
        $this->wrapper->setCloseAction($action);
    }
    
    /**
     * Block UI
     */
    public static function blockUI($timeout = null)
    {
        TScript::create('tjquerydialog_block_ui()', true, $timeout);
    }
    
    /**
     * Unblock UI
     */
    public static function unBlockUI($timeout = null)
    {
        TScript::create('tjquerydialog_unblock_ui()', true, $timeout);
    }
    
    /**
     * Close TJQueryDialog's
     */
    public static function closeWindow($id = null)
    {
        if (!empty($id))
        {
            TJQueryDialog::closeById($id);
        }
        else
        {
            TJQueryDialog::closeLatest();
        }
    }
    
    /**
     * Close all windows
     */
    public static function closeAll()
    {
        TJQueryDialog::closeAll();
    }
    
    /**
     * Close window by name of controller
     */
    public static function closeWindowByName($name)
    {
        TScript::create( ' $(\'[window_name="'.$name.'"]\').remove(); ' );
    }
}
