<?php
/**
 * System generated by SysGen (System Generator with Formdin Framework) 
 * Download SysGen: https://github.com/bjverde/sysgen
 * Download Formdin Framework: https://github.com/bjverde/formDin
 * 
 * SysGen  Version: 1.11.0
 * FormDin Version: 4.11.0
 * 
 * System concursomembrointranet created in: 2020-10-28 20:59:17
 */

if ( !function_exists( 'cargabd_cnpjrfb_dao_autoload') ) {
    function concursomembrointranet_dao_autoload( $class_name )
    {
        $path = __DIR__.DS.$class_name.'.class.php';
        if (file_exists($path)){
            require_once $path;
        } else {
            return false;
        }
    }
spl_autoload_register('cargabd_cnpjrfb_dao_autoload');
}