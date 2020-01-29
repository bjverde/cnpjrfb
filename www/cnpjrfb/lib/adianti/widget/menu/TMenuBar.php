<?php
namespace Adianti\Widget\Menu;

use Adianti\Widget\Menu\TMenu;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Util\AdiantiStringConversion;

use SimpleXMLElement;

/**
 * Menubar Widget
 *
 * @version    7.1
 * @package    widget
 * @subpackage menu
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMenuBar extends TElement
{
    public function __construct()
    {
        parent::__construct('div');
        $this->{'style'} = 'margin: 0;';
        $this->{'class'} = 'navbar';
    }
    
    /**
     * Build a MenuBar from a XML file
     * @param $xml_file path for the file
     * @param $permission_callback check permission callback
     */
    public static function newFromXML($xml_file, $permission_callback = NULL, $bar_class = 'nav navbar-nav', $menu_class = 'dropdown-menu', $item_class = '')
    {
        if (file_exists($xml_file))
        {
            $menu_string = AdiantiStringConversion::assureUnicode(file_get_contents($xml_file));
            $xml = new SimpleXMLElement($menu_string);
            
            $menubar = new TMenuBar;
            $ul = new TElement('ul');
            $ul->{'class'} = $bar_class;
            $menubar->add($ul);
            foreach ($xml as $xmlElement)
            {
                $atts   = $xmlElement->attributes();
                $label  = (string) $atts['label'];
                $action = (string) $xmlElement-> action;
                $icon   = (string) $xmlElement-> icon;
                
                $item = new TMenuItem($label, $action, $icon);
                $menu = new TMenu($xmlElement-> menu-> menuitem, $permission_callback, 1, $menu_class, $item_class);

                // check children count (permissions)
                if (count($menu->getMenuItems()) >0)
                {
                    $item->setMenu($menu);
                    $item->{'class'} = 'active';
                    $ul->add($item);
                }
                else if ($action)
                {
                    $ul->add($item);
                }
            }
            
            return $menubar;
        }
    }
    
    /**
     * Show
     */
    public function show()
    {
        TScript::create( 'tmenubar_start();' );
        parent::show();
    }
}
