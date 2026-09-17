<?php
class AdiantiMenuBuilder
{
    const CHECK_PERMISSION = null;
    
    /**
     * Parse main menu and converts into HTML
     */
    public static function parse($file, $theme)
    {
        if (!extension_loaded('SimpleXML')){
            throw new Exception(_t('Extension not found: ^1', 'SimpleXML'));
        }
        
        if (!file_exists($file)){
            throw new Exception(_t('File not found').': ' . $file);
        }
        
        $listTemas = array('adminbs5', 'adminbs5_v7');
        if ( in_array($theme, $listTemas) ) {
            $xml  = new SimpleXMLElement(file_get_contents($file));
            $menu = new TMenu($xml, self::CHECK_PERMISSION, 1, 'sidebar-dropdown list-unstyled collapse', 'sidebar-item', 'sidebar-link collapsed', [__class__, 'prepareItem']);
            $menu->class = 'sidebar-nav';
            $menu->id    = 'side-menu';

            ob_start();
            $menu->show();
            return ob_get_clean();
        }else{
            throw new Exception(_t('Theme not supported').': ' . $theme);
        }
    }
    
    /**
     *
     */
    public static function prepareItem($menuitem)
    {
        $ini = AdiantiApplicationConfig::get();
        if (!empty($ini['template']['navbar']['allow_page_tabs']))
        {
            $action = $menuitem->getAction();
            if (!$menuitem->getMenu() && strpos($action, 'LoginForm#method=onLogout') === false)
            {
                $open_tab = new TElement('div');
                $open_tab->title = _t('Open in new tab');
                $open_tab->onclick = "event.stopPropagation();Template.createPageTabFromMenu(this);return false;";
                $open_tab->style = 'width: 15px;height: var(--ad-font-size-menu);position: relative;float: right;';
                $open_tab->add('<i class="fa-solid fa-up-right-from-square" style="font-size:9pt"></i>');
                $menuitem->setRightWidget($open_tab);
            }
        }
    }
    
    /**
     *
     */
    public static function parseNavBar($file, $theme)
    {
        if (file_exists($file))
        {
            return AdiantiNavBarParser::parse($file);
        }
        
        return '';
    }
}
