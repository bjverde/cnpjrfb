<?php
namespace Adianti\Widget\Menu;

use Adianti\Core\AdiantiCoreApplication;
use Adianti\Widget\Menu\TMenu;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Util\TImage;

/**
 * MenuItem Widget
 *
 * @version    7.1
 * @package    widget
 * @subpackage menu
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMenuItem extends TElement
{
    private $label;
    private $action;
    private $image;
    private $menu;
    private $level;
    private $link;
    private $linkClass;
    
    /**
     * Class constructor
     * @param $label  The menu label
     * @param $action The menu action
     * @param $image  The menu image
     */
    public function __construct($label, $action, $image = NULL, $level = 0)
    {
        parent::__construct('li');
        $this->label     = $label;
        $this->action    = $action;
        $this->level     = $level;
        $this->link      = new TElement('a');
        $this->linkClass = 'dropdown-toggle';
        
        if ($image)
        {
            $this->image = $image;
        }
    }
    
    /**
     * Returns the action
     */
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * Set the action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }
    
    /**
     * Returns the label
     */
    public function getLabel()
    {
        return $this->label;
    }
    
    /**
     * Set the label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }
    
    /**
     * Returns the image
     */
    public function getImage()
    {
        return $this->image;
    }
    
    /**
     * Set the image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }
    
    /**
     * Set link class
     */
    public function setLinkClass($class)
    {
        $this->linkClass = $class;
    }
    
    /**
     * Define the submenu for the item
     * @param $menu A TMenu object
     */
    public function setMenu(TMenu $menu)
    {
        $this->{'class'} = 'dropdown-submenu';
        $this->menu = $menu;
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        if ($this->action)
        {
            //$url['class'] = $this->action;
            //$url_str = http_build_query($url);
            $action = str_replace('#', '&', $this->action);
            if ((substr($action,0,7) == 'http://') or (substr($action,0,8) == 'https://'))
            {
                $this->link->{'href'} = $action;
                $this->link->{'target'} = '_blank';
            }
            else
            {
                if ($router = AdiantiCoreApplication::getRouter())
                {
                    $this->link->{'href'} = $router("class={$action}", true);
                }
                else
                {
                    $this->link->{'href'} = "index.php?class={$action}";
                }
                $this->link->{'generator'} = 'adianti';
            }
        }
        else
        {
            $this->link->{'href'} = '#';
        }
        
        if (isset($this->image))
        {
            $image = new TImage($this->image);
            $this->link->add($image);
        }
        
        $label = new TElement('span');
        if (substr($this->label, 0, 3) == '_t{')
        {
            $label->add(_t(substr($this->label,3,-1)));
        }
        else
        {
            $label->add($this->label);
        }
        
        if (!empty($this->label))
        {
            $this->link->add($label);
            $this->add($this->link);
        }
        
        if ($this->menu instanceof TMenu)
        {
            $this->link->{'class'} = $this->linkClass;
            if (strstr($this->linkClass, 'dropdown'))
            {
                $this->link->{'data-toggle'} = "dropdown";
            }
            
            if ($this->level == 0)
            {
                $caret = new TElement('b');
                $caret->{'class'} = 'caret';
                $caret->add('');
                $this->link->add($caret);
            }
            parent::add($this->menu);
        }
        
        parent::show();
    }
}
