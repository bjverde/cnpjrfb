<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Container\TTable;
use Adianti\Widget\Container\TScroll;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Core\AdiantiCoreTranslator;

use Exception;

/**
 * Exception visualizer
 *
 * @version    7.1
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TExceptionView
{
    /**
     * Constructor method
     */
    function __construct(Exception $e)
    {
        $error_array = $e->getTrace();
        $table = new TTable;
        $row=$table->addRow();
        $row->addCell('<b>' . $e->getMessage(). '</b><br>');
        $row=$table->addRow();
        $row->addCell('&nbsp;');
        
        foreach ($error_array as $error)
        {
            $file = isset($error['file']) ? $error['file'] : '';
            $line = isset($error['line']) ? $error['line'] : '';
            $file = str_replace(PATH, '', $file);
            
            $row=$table->addRow();
            $row->addCell('File: '.$file. ' : '. $line);
            $row=$table->addRow();
            $args = array();
            if ($error['args'])
            {
                foreach ($error['args'] as $arg)
                {
                    if (is_object($arg))
                    {
                        $args[] = get_class($arg). ' object';
                    }
                    else if (is_array($arg))
                    {
                        $array_param = array();
                        foreach ($arg as $value)
                        {
                            if (is_object($value))
                            {
                                $array_param[] = get_class($value);
                            }
                            else if (is_array($value))
                            {
                                $array_param[] = 'array';
                            }
                            else
                            {
                                $array_param[] = $value;
                            }
                        }
                        $args[] = implode(',', $array_param);
                    }
                    else
                    {
                        $args[] = (string) $arg;
                    }
                }
            }
            $class = isset($error['class']) ? $error['class'] : '';
            $type  = isset($error['type'])  ? $error['type']  : '';
            
            $row->addCell('&nbsp;&nbsp;<i>'.'<font color=green>'.$class.'</font>'.
                                            '<font color=olive>'.$type.'</font>'.
                                            '<font color=darkblue>'.$error['function'].'</font>'.
                                            '('.'<font color=maroon>'.implode(',', $args).'</font>'.')</i>');
        }
        
        ob_start();
        $table->show();
        $content = ob_get_clean();
        
        new TMessage('error', $content, NULL, AdiantiCoreTranslator::translate('Exception'));
    }
}
