<?php
namespace Adianti\Core;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TPage;
use Adianti\Registry\TSession;
use Adianti\Util\AdiantiStringConversion;
use Exception;

/**
 * Template parser
 *
 * @version    8.6
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
        
        if (!empty($ini['template']['navbar']['only_top_menu']))
        {
            $content = str_replace('<!--[SIDEBAR]-->',  '<!--',  $content);
            $content = str_replace('<!--[/SIDEBAR]-->', '-->',   $content);
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
        $content   = str_replace('{application}', $ini['general']['application'], $content);
        $content   = str_replace('{template_options}',  json_encode($ini['template'] ?? []), $content);
        $content   = str_replace('{login_background}', (!empty($ini['login']['background']) ? "background: url('{$ini['login']['background']}')" : ''), $content );
        
        if (file_exists('buildid'))
        {
            $content   = str_replace('{buildid}', AdiantiStringConversion::slug(file_get_contents('buildid')), $content);
        }
        
        if (empty($ini['general']['creator_url']))
        {
            $content = str_replace('<!--[ISCREATOR]-->',  '<!--',  $content);
            $content = str_replace('<!--[/ISCREATOR]-->', '-->',   $content);
        }
        
        if (empty($ini['general']['creator_url']) || (TSession::getValue('login') !== 'admin'))
        {
            $content = str_replace('<!--[ISCREATORADMIN]-->',  '<!--',  $content);
            $content = str_replace('<!--[/ISCREATORADMIN]-->', '-->',   $content);
        }
        
        $core_options = [ 'timezone'    => $ini['general']['timezone'],
                          'language'    => \ApplicationTranslator::getLanguage(),
                          'application' => $ini['general']['application'],
                          'title'       => $ini['general']['title'],
                          'theme'       => $ini['general']['theme'],
                          'debug'       => $ini['general']['debug'] ];
        
        $content   = str_replace('{adianti_options}',  json_encode($core_options), $content);
        
        $css       = TPage::getLoadedCSS();
        $js        = TPage::getLoadedJS();
        $content   = str_replace('{HEAD}', $css.$js, $content);
        
        return $content;
    }
}
