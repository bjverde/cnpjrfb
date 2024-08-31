<?php
namespace Adianti\Widget\Container;

use Adianti\Control\TAction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Core\AdiantiCoreTranslator;
use Exception;

/**
 * JQuery dialog container
 *
 * @version    7.6
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TJQueryDialog extends TElement
{
    private $actions;
    private $width;
    private $height;
    private $top;
    private $left;
    private $modal;
    private $draggable;
    private $resizable;
    private $useOKButton;
    private $stackOrder;
    private $closeAction;
    private $closeEscape;
    private $dialogClass;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct()
    {
        parent::__construct('div');
        $this->useOKButton = TRUE;
        $this->top = NULL;
        $this->left = NULL;
        $this->modal = 'true';
        $this->draggable = 'true';
        $this->resizable = 'true';
        $this->stackOrder = 2000;
        $this->closeEscape = true;
        $this->dialogClass = '';
        
        $this->{'id'} = 'jquery_dialog_'.mt_rand(1000000000, 1999999999);
        $this->{'style'} = "overflow:auto";
    }
    
    /**
     * Disable close on escape
     */
    public function disableEscape()
    {
        $this->closeEscape = false;
    }
    
    /**
     * Disable scrolling
     */
    public function disableScrolling()
    {
        $this->{'style'} = "overflow: hidden";
    }
    
    /**
     * Set Dialog class
     * @param $class Class name
     */
    public function setDialogClass($class)
    {
        $this->dialogClass = $class;
    }
    
    /**
     * Set close action
     */
    public function setCloseAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->closeAction = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
    }
    
    /**
     * Define if will use OK Button
     * @param $bool boolean
     */
    public function setUseOKButton($bool)
    {
        $this->useOKButton = $bool;
    }
    
    /**
     * Define the dialog title
     * @param $title title
     */
    public function setTitle($title)
    {
        $this->{'title'} = $title;
    }
    
    /**
     * Turn on/off modal
     * @param $modal Boolean
     */
    public function setModal($bool)
    {
        $this->modal = $bool ? 'true' : 'false';
    }
    
    /**
     * Turn on/off resizeable
     * @param $bool Boolean
     */
    public function setResizable($bool)
    {
        $this->resizable = $bool ? 'true' : 'false';
    }
    
    /**
     * Turn on/off draggable
     * @param $bool Boolean
     */
    public function setDraggable($bool)
    {
        $this->draggable = $bool ? 'true' : 'false';
    }
    
    /**
     * Returns the element ID
     */
    public function getId()
    {
        return $this->{'id'};
    }
    
    /**
     * Define the dialog size
     * @param $width width
     * @param $height height
     */
    public function setSize($width, $height)
    {
        $this->width  = $width  < 1 ? "\$(window).width() * $width" : $width;
        
        if (is_null($height))
        {
            $this->height = "'auto'";
        }
        else
        {
            $this->height = $height < 1 ? "\$(window).height() * $height" : $height;
        }
    }
    
    /**
     * Define the window's min width between percent and absolute
     * @param  $percent width
     * @param  $absolute width
     */
    public function setMinWidth($percent, $absolute)
    {
        $this->width  = "Math.min(\$(window).width() * $percent, $absolute)";
    }
    
    /**
     * Define the dialog position
     * @param $left left
     * @param $top top
     */
    public function setPosition($left, $top)
    {
        $this->left = $left;
        $this->top  = $top;
    }
    
    /**
     * Add a JS button to the dialog
     * @param $label button label
     * @param $action JS action
     */
    public function addAction($label, $action)
    {
        $this->actions[] = array($label, $action);
    }
    
    /**
     * Define the stack order (zIndex)
     * @param $order Stack order
     */
    public function setStackOrder($order)
    {
        $this->stackOrder = $order;
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        $action_code = '';
        if ($this->actions)
        {
            foreach ($this->actions as $action_array)
            {
                $label  = $action_array[0];
                $action = $action_array[1];
                $action_code .= "\"{$label}\": function() {  $action },";
            }
        }
        
        $ok_button = '';
        if ($this->useOKButton)
        {
            $ok_button = '  OK: function() { $( this ).remove(); }';
        }
        
        $left = $this->left ? $this->left : 0;
        $top  = $this->top  ? $this->top  : 0;
        
        $pos_string = '';
        $id = $this->{'id'};
        
        $close_action = 'undefined'; // cannot be function, because it is tested inside tjquerydialog.js
        
        if (isset($this->closeAction))
        {
            $string_action = $this->closeAction->serialize(FALSE);
            $close_action = "function() { __adianti_ajax_exec('{$string_action}') }";
        }
        
        $close_on_escape = $this->closeEscape ? 'true' : 'false';
        parent::add(TScript::create("tjquerydialog_start( '#{$id}', {$this->modal}, {$this->draggable}, {$this->resizable}, {$this->width}, {$this->height}, {$top}, {$left}, {$this->stackOrder}, { {$action_code} {$ok_button} }, $close_action, $close_on_escape, '{$this->dialogClass}' ); ", FALSE));
        parent::show();
    }
    
    /**
     * Closes the dialog
     */
    public function close()
    {
        parent::add(TScript::create('$( "#' . $this->{'id'} . '" ).remove();', false));
    }
    
    /**
     * Close window by id
     */
    public static function closeById($id)
    {
        TScript::create('$( "#' . $id . '" ).remove();');
    }
    
    /**
     * Close all TJQueryDialog
     */
    public static function closeAll()
    {
        if (!isset($_REQUEST['ajax_lookup']) OR $_REQUEST['ajax_lookup'] !== '1')
        {
            // it has to be inline (not external function call)
            TScript::create( ' $(\'[widget="TWindow"]\').remove(); ' );
        }
    }
    
    /**
     * Close all TJQueryDialog
     */
    public static function closeLatest()
    {
        if (!isset($_REQUEST['ajax_lookup']) OR $_REQUEST['ajax_lookup'] !== '1')
        {
            // it has to be inline (not external function call)
            TScript::create( ' $(\'[role=window-wrapper]\').last().remove(); ' );
        }
    }
}
