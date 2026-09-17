<?php
require_once 'init.php';

$ini = AdiantiApplicationConfig::get();
$theme  = $ini['general']['theme'];
new TSession;

if (isset($_REQUEST['template']) AND $_REQUEST['template'] == 'iframe') {
	$content = file_get_contents("app/templates/{$theme}/iframe.html");
} else {
	$content  = file_get_contents("app/templates/{$theme}/layout.html");
}

$menu_string = AdiantiMenuBuilder::parse('menu.xml', $theme);
$content     = ApplicationTranslator::translateTemplate($content);

//--- START: TEMA ADMINBS5_V7  ---------------------------------------------------------
$system_version = $ini['system']['system_version'];
$title          = $ini['general']['title'];
$head_title     = $title.' - v'.$system_version;

$content = str_replace('{head_title}', $head_title, $content);
$content = str_replace('{title}', $title, $content);
$content = str_replace('{system_version}', $system_version, $content);
//--- END: TEMA ADMINBS5_V7 ------------------------------------------------------------

$content     = str_replace('{LIBRARIES}', file_get_contents("app/templates/{$theme}/libraries.html"), $content);
$content     = str_replace('{class}', isset($_REQUEST['class']) ? $_REQUEST['class'] : '', $content);
$content     = str_replace('{template}', $theme, $content);
$content     = str_replace('{MENU}', $menu_string, $content);
$content     = str_replace('{MENUTOP}', AdiantiMenuBuilder::parseNavBar('menu-top-public.xml', $theme), $content);
$content     = str_replace('{MENUBOTTOM}', AdiantiMenuBuilder::parseNavBar('menu-bottom-public.xml', $theme), $content);
$content     = str_replace('{lang}', $ini['general']['language'], $content);
//$content     = str_replace('{title}', $ini['general']['title'] ?? '', $content);
$content     = str_replace('{template_options}',  json_encode($ini['template'] ?? []), $content);
$content     = str_replace('{adianti_options}',  json_encode($ini['general']), $content);

$css         = TPage::getLoadedCSS();
$js          = TPage::getLoadedJS();
$content     = str_replace('{HEAD}', $css.$js, $content);

echo $content;

if (isset($_REQUEST['class'])){
    $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : NULL;
    AdiantiCoreApplication::loadPage($_REQUEST['class'], $method, $_REQUEST);
} else {
    AdiantiCoreApplication::loadPage('AjudaView', null, null);
}
