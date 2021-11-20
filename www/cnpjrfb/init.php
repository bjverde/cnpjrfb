<?php
if (version_compare(PHP_VERSION, '7.1.0') == -1)
{
    die ('The minimum version required for PHP is 7.1.0');
}

// define the autoloader
require_once 'lib/adianti/core/AdiantiCoreLoader.php';
spl_autoload_register(array('Adianti\Core\AdiantiCoreLoader', 'autoload'));
Adianti\Core\AdiantiCoreLoader::loadClassMap();

$loader = require 'vendor/autoload.php';
$loader->register();

// read configurations
$ini = parse_ini_file('app/config/application.ini', true);
date_default_timezone_set($ini['general']['timezone']);
AdiantiCoreTranslator::setLanguage( $ini['general']['language'] );
ApplicationTranslator::setLanguage( $ini['general']['language'] );
AdiantiApplicationConfig::load($ini);
AdiantiApplicationConfig::apply();

// define constants
define('APPLICATION_NAME', $ini['general']['application']);
define('OS', strtoupper(substr(PHP_OS, 0, 3)));
define('PATH', dirname(__FILE__));
define('LANG', $ini['general']['language']);

// custom session name
session_name('PHPSESSID_'.$ini['general']['application']);

//--- FORMDIN 5 START ---------------------------------------------------------
define('DS', DIRECTORY_SEPARATOR);
define('EOL', "\n");
define('ESP', chr(32).chr(32).chr(32).chr(32) );
define('TAB', chr(9));

define('FORMDIN_VERSION', $ini['system']['formdin_min_version']);
define('SYSTEM_VERSION', $ini['system']['version']);
define('SYSTEM_NAME', $ini['system']['system_name']);
//--- FORMDIN 5 END -----------------------------------------------------------

//--- SysGen For Adianti START ------------------------------------------------
define('ROOT_PATH', '../');
if(!defined('ROWS_PER_PAGE') ) { 
    define('ROWS_PER_PAGE', 20); 
}
if(!defined('ENCODINGS') ) { 
    define('ENCODINGS', 'UTF-8'); 
}
//--- SysGen For Adianti END --------------------------------------------------