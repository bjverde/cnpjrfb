<?php
if (PHP_SAPI !== 'cli')
{
    die ('Access denied');
}

chdir(dirname(__FILE__));
require_once 'init.php';

parse_str($argv[1], $_REQUEST);
$class   = isset($_REQUEST['class'])    ? $_REQUEST['class']   : '';
$static  = isset($_REQUEST['static'])   ? $_REQUEST['static']  : '';
$method  = isset($_REQUEST['method'])   ? $_REQUEST['method']  : '';

try
{
    AdiantiCoreApplication::execute($class, $method, $_REQUEST, 'cli');
}
catch (Exception $e)
{
    echo 'Error: ' . $e->getMessage();
}
