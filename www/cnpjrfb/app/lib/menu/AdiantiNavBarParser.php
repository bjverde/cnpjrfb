<?php
class AdiantiNavBarParser
{
    const CHECK_PERMISSION = ['SystemPermission', 'checkPermission'];
    
    /**
     * Parse main menu and converts into HTML
     */
    public static function parse($file)
    {
        if (!extension_loaded('SimpleXML'))
        {
            throw new Exception(_t('Extension not found: ^1', 'SimpleXML'));
        }
        
        $xml = new SimpleXMLElement(file_get_contents($file));
        
        $output = '';
        foreach ($xml as $xmlElement)
        {
            $label  = null;
            $icon   = !empty( (string) $xmlElement-> icon ) ? (string) $xmlElement-> icon : null;
            $action_string = str_replace('#', '&', (string) $xmlElement-> action);
            
            if ((string) $xmlElement-> label)
            {
                $label = (string) $xmlElement-> label;
            }
            if ((string) $xmlElement->attributes()-> label)
            {
                $label = (string) $xmlElement->attributes()-> label;
            }
            
            if (substr($label, 0, 3) == '_t{')
            {
                $label = _t(substr($label,3,-1));
            }
            else if (substr($label, 0, 4) == '_tf{')
            {
                $ini = AdiantiApplicationConfig::get();
                $label = _tf(substr($label,4,-1), $ini['general']['source_language']);
            }
            
            if ($xmlElement->menu)
            {
                $i = ($icon) ? new TImage($icon) : null;
                $dropdown = new TDropDown($label, $i);
                $dropdown->setButtonClass('dropdown-toggle btn superlight');
                
                if (!empty((string) $xmlElement-> mobile) && (string) $xmlElement-> mobile == 'N')
                {
                    $dropdown->setButtonClass('dropdown-toggle btn superlight hide-mobile');
                }
                
                foreach ($xmlElement->menu->menuitem as $menuItem)
                {
                    $sub_li = self::createSubmenuItem($menuItem);
                    
                    if (!empty($sub_li))
                    {
                        $dropdown->addItem($sub_li);
                    }
                }
                
                if (count($dropdown->getItems()) >0)
                {
                    $output .= $dropdown;
                }
            }
            else
            {
                $link = new TElement('a');
                $link->generator = "adianti";
                $link->class     = "btn superlight ";
                $link->style     = "padding: 5px;";
                
                if (!empty((string) $xmlElement-> mobile) && (string) $xmlElement-> mobile == 'N')
                {
                    $link->class .= " hide-mobile";
                }
                
                if (!empty((string) $xmlElement-> title))
                {
                    $link->title = (string) $xmlElement-> title;
                }
                
                if ((substr($action_string,0,7) == 'http://') or (substr($action_string,0,8) == 'https://'))
                {
                    $link->{'href'} = $action_string;
                    $link->{'target'} = '_blank';
                    $link->{'generator'} = '';
                }
                else
                {
                    if ($router = AdiantiCoreApplication::getRouter())
                    {
                        $link->{'href'} = $router("class={$action_string}", true);
                    }
                    else
                    {
                        $link->{'href'} = "index.php?class={$action_string}";
                    }
                }
                
                if ($icon)
                {
                    $i = new TImage($icon);
                    $i->style = "color:gray;font-size:1.2rem;float: left;margin-top: 3px;";
                    $link->add($i);
                }
                
                if ($label)
                {
                    $link->add('&nbsp;'. $label);
                }
                
                if ( (strpos($file, 'public') !== false) || (self::checkMenuActionPermission($action_string)) || 
                     (substr($action_string,0,7) == 'http://') || (substr($action_string,0,8) == 'https://') )
                {
                    $output .= $link;
                }
            }
        }
        
        return $output;
    }
    
    /**
     *
     */
    private static function createSubmenuItem($menuItem)
    {
        $item_label  = (string) $menuItem->attributes()-> label;
        $item_action = str_replace('#', '&', (string) $menuItem-> action);
        $item_icon   = (string) $menuItem-> icon;
        
        if (!empty($item_action) && !self::checkMenuActionPermission($item_action))
        {
            return;
        }
        
        $li = new TElement('li');
        $link = new TElement('a');
        $link->{'class'} = "dropdown-item";
        
        if (!empty($item_action))
        {
            if ($router = AdiantiCoreApplication::getRouter())
            {
                $action = '__adianti_load_page("'.$router("class={$item_action}", true) .'")';
            }
            else
            {
                $action = "__adianti_load_page('index.php?class={$item_action}')";
            }
            $link->{'onclick'} = $action;
        }
        else
        {
            $link->{'onclick'} = 'window.event.stopPropagation();'; // kepp ul opened in mobile
        }
        
        $link->{'style'} = 'cursor: pointer';
        
        if ($item_icon)
        {
            $image = new TImage($item_icon . ' gray');
            //$image->{'style'} .= ';padding: 4px';
            $link->add($image);
        }
        
        if (substr($item_label, 0, 3) == '_t{')
        {
            $item_label = _t(substr($item_label,3,-1));
        }
        else if (substr($item_label, 0, 4) == '_tf{')
        {
            $ini = AdiantiApplicationConfig::get();
            $item_label = _tf(substr($item_label,4,-1), $ini['general']['source_language']);
        }
        
        $span = new TElement('span');
        $span->add($item_label);
        $link->add($span);
        $li->add($link);
        
        if (!empty((string) $menuItem-> mobile) && (string) $menuItem-> mobile == 'N')
        {
            $li->class .= " hide-mobile";
        }
        
        if ($menuItem->menu)
        {
            $ul = new TElement('ul');
            $ul->class = 'dropdown-menu dropdown-submenu';
            $link->add('<i class="fa-solid fa-angle-right" style="position: absolute;right: 0;padding: var(--bs-dropdown-item-padding-y) var(--bs-dropdown-item-padding-x);"></i>');
            
            foreach ($menuItem->menu->menuitem as $subMenuItem)
            {
                $sub_li = self::createSubmenuItem($subMenuItem);
                if (!empty($sub_li))
                {
                    $ul->add($sub_li);
                }
            }
            
            if (count($ul->getChildren()) > 0)
            {
                $li->add($ul);
            }
            else
            {
                return '';
            }
        }
        
        return $li;
    }
    
    /**
     * Check menu item permission
     */
    private static function checkMenuActionPermission($action_string)
    {
        $permission_callback = self::CHECK_PERMISSION;
        
        parse_str('class='.$action_string, $parts);
        $className = $parts['class'];
        if (call_user_func($permission_callback, $className))
        {
            return true;
        }
        return false;
    }
}
