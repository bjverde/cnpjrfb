<?php
require_once 'init.php';

FormDinHelper::setFormDinMinimumVersion($ini['system']['formdin_min_version']);

$theme  = $ini['general']['theme'];
new TSession;

$content     = file_get_contents("app/templates/{$theme}/layout.html");
$menu_string = AdiantiMenuBuilder::parse('menu.xml', $theme);
$content     = str_replace('{MENU}', $menu_string, $content);
$content     = ApplicationTranslator::translateTemplate($content);
$content     = str_replace('{LIBRARIES}', file_get_contents("app/templates/{$theme}/libraries.html"), $content);
$content     = str_replace('{class}', isset($_REQUEST['class']) ? $_REQUEST['class'] : '', $content);
$content     = str_replace('{template}', $theme, $content);
$content     = str_replace('{MENU}', $menu_string, $content);
$css         = TPage::getLoadedCSS();
$js          = TPage::getLoadedJS();
$content     = str_replace('{HEAD}', $css.$js, $content);

//--- FORMDIN 5 START ---------------------------------------------------------
$content     = str_replace('{head_title}'     , $ini['general']['application'], $content);
$content     = str_replace('{formdin_version}', FormDinHelper::version(), $content);
$content     = str_replace('{system_version}' , $ini['system']['version'], $content);
$content     = str_replace('{system_name}'    , $ini['system']['system_name'], $content);
$content     = str_replace('{system_name_sub}', $ini['system']['version'], $content);
$content     = str_replace('{logo-mini}', $ini['system']['logo-mini'], $content);
$content     = str_replace('{logo-lg}', $ini['system']['logo-lg'], $content);
$content     = str_replace('{logo-link-class}', $ini['system']['logo-link-class'], $content);
$content     = str_replace('{login-link}', $ini['system']['login-link'], $content);
$content     = str_replace('{login}', $ini['system']['login'], $content);
//--- FORMDIN 5 END -----------------------------------------------------------

echo $content;

if (isset($_REQUEST['class'])){
    $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : NULL;
    AdiantiCoreApplication::loadPage($_REQUEST['class'], $method, $_REQUEST);
} else {
    AdiantiCoreApplication::loadPage('AjudaView', null, null);
}