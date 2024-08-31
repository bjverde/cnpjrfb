<?php
namespace Adianti\Widget\Base;

use Adianti\Widget\Base\TElement;

/**
 * Base class for scripts
 *
 * @version    7.6
 * @package    widget
 * @subpackage base
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TScript
{
    /**
     * Create a script
     * @param $code source code
     */
    public static function create( $code, $show = TRUE, $timeout = null )
    {
        if ($timeout)
        {
            $code = "setTimeout( function() { $code }, $timeout )";
        }
        
        $script = new TElement('script');
        $script->{'type'} = 'text/javascript';
        $script->setUseSingleQuotes(TRUE);
        $script->setUseLineBreaks(FALSE);
        $script->add( str_replace( ["\n", "\r"], [' ', ' '], $code) );
        if ($show)
        {
            $script->show();
        }
        return $script;
    }
    
    /**
     * Import script
     * @param $script Script file name
     */
    public static function importFromFile( $script, $show = TRUE, $timeout = null )
    {
        TScript::create('$.getScript("'.$script.'");', $show, $timeout);
    }
}
