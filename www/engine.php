<?php
require_once 'init.php';

class TApplication extends AdiantiCoreApplication
{
    public static function run($debug = null)
    {
        new TSession;
        
        if ($_REQUEST)
        {
            $ini    = AdiantiApplicationConfig::get();
            $debug  = is_null($debug)? $ini['general']['debug'] : $debug;
            
            parent::run($debug);
        }
    }
}

TApplication::run();
