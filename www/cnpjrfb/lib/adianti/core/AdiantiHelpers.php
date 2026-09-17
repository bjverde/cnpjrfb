<?php
/**
 * Create global helpers
 *
 * @version    8.6
 * @package    core
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class AdiantiHelpers
{
    /**
     * Register helpers
     */
    public static function register()
    {
        if (!function_exists('a_dump'))
        {
            function a_dump($content, $title = null)
            {
                $block = new TElement('adump');
                
                if (!empty($title))
                {
                    $block->add( TElement::tag('h2', $title ));
                }
                $block->add( TElement::tag('span', date("Y-m-d H:i:s"), ['style' => 'margin-top: 5px'] ));
                $block->add( TElement::tag('span', print_r($content, true) ) );
                
                print $block;
            }
        }
    }
}
