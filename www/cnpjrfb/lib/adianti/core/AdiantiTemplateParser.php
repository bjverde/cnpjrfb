<?php
namespace Adianti\Core;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TPage;
use Adianti\Registry\TSession;
use Exception;

/**
 * Template parser
 *
 * @version    7.6
 * @package    core
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class AdiantiTemplateParser
{
    /**
     * Parse template and replace basic system variables
     * @param $content raw template
     */
    public static function parse($content)
    {
        $ini       = AdiantiApplicationConfig::get();
        $theme     = $ini['general']['theme'];
        $libraries = file_get_contents("app/templates/{$theme}/libraries.html");
        $class     = isset($_REQUEST['class']) ? $_REQUEST['class'] : '';
        
        if ( !(TSession::getValue('login') == 'admin'))
        {
            $content = str_replace('<!--[IFADMIN]-->',  '<!--',  $content);
            $content = str_replace('<!--[/IFADMIN]-->', '-->',   $content);
        }
        
        if (!isset($ini['permission']['user_register']) OR $ini['permission']['user_register'] !== '1')
        {
            $content = str_replace(['<!--[CREATE-ACCOUNT]-->', '<!--[CREATE-ACCOUNT]-->'], ['<!--', '-->'], $content);
        }
        
        if (!isset($ini['permission']['reset_password']) OR $ini['permission']['reset_password'] !== '1')
        {
            $content = str_replace(['<!--[RESET-PASSWORD]-->', '<!--[RESET-PASSWORD]-->'], ['<!--', '-->'], $content);
        }
        
        $use_tabs = $ini['general']['use_tabs'] ?? 0;
        $store_tabs = $ini['general']['store_tabs'] ?? 0;
        $use_mdi_windows = $ini['general']['use_mdi_windows'] ?? 0;
        $store_mdi_windows = $ini['general']['store_mdi_windows'] ?? 0;

        if ($use_mdi_windows) {
            $use_tabs = 1;
        }

        if ($store_mdi_windows) {
            $store_tabs = 1;
        }

        $content   = str_replace('{LIBRARIES}', $libraries, $content);
        $content   = str_replace('{class}',     $class, $content);
        $content   = str_replace('{template}',  $theme, $content);
        $content   = str_replace('{lang}',      AdiantiCoreTranslator::getLanguage(), $content);
        $content   = str_replace('{debug}',     isset($ini['general']['debug']) ? $ini['general']['debug'] : '1', $content);
        $content   = str_replace('{login}',     (string) TSession::getValue('login'), $content);
        $content   = str_replace('{title}',     isset($ini['general']['title']) ? $ini['general']['title'] : '', $content);
        $content   = str_replace('{username}',  (string) TSession::getValue('username'), $content);
        $content   = str_replace('{usermail}',  (string) TSession::getValue('usermail'), $content);
        $content   = str_replace('{frontpage}', (string) TSession::getValue('frontpage'), $content);
        $content   = str_replace('{userunitid}', (string) TSession::getValue('userunitid'), $content);
        $content   = str_replace('{userunitname}', (string) TSession::getValue('userunitname'), $content);
        $content   = str_replace('{query_string}', $_SERVER["QUERY_STRING"] ?? '', $content);
        $content   = str_replace('{use_tabs}', $use_tabs, $content);
        $content   = str_replace('{store_tabs}', $store_tabs, $content);
        $content   = str_replace('{use_mdi_windows}', $use_mdi_windows, $content);
        $content   = str_replace('{application}', $ini['general']['application'], $content);
        
        $css       = TPage::getLoadedCSS();
        $js        = TPage::getLoadedJS();
        $content   = str_replace('{HEAD}', $css.$js, $content);
        
        return $content;
    }
}
