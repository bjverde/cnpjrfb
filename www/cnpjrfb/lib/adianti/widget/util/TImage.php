<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Base\TElement;

/**
 * Image Widget
 *
 * @version    7.4
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TImage extends TElement
{
    private $source; // image path
    
    /**
     * Class Constructor
     * @param $source Image path, of bs:bs-glyphicon, fab:font-awesome
     */
    public function __construct($source)
    {
        if (substr($source,0,3) == 'fa:')
        {
            parent::__construct('i');
            
            $fa_class = substr($source,3);
            if (strstr($source, '#') !== FALSE)
            {
                $parts = explode('#', $fa_class);
                $fa_color   = substr($parts[1],0,7);
                $fa_bgcolor = !empty($parts[2]) ? substr($parts[2],0,7) : null;
                $fa_class   = str_replace( ['#'.$fa_color, '#'.$fa_bgcolor], ['', ''], $fa_class);
            }
            
            $this->{'class'} = 'fa fa-'.$fa_class;
            if (!empty($fa_color))
            {
                $this->{'style'} .= "; color: #{$fa_color};";
            }
            
            if (!empty($fa_bgcolor))
            {
                $this->{'style'} .= "; background-color: #{$fa_bgcolor};";
            }
            parent::add('');
        }
        else if ( ( substr($source,0,4) == 'far:') || (substr($source,0,4) == 'fas:') || (substr($source,0,4) == 'fab:') || (substr($source,0,4) == 'fal:') || (substr($source,0,4) == 'fad:'))
        {
            parent::__construct('i');
            
            $fa_class = substr($source,4);
            if (strstr($source, '#') !== FALSE)
            {
                $parts = explode('#', $fa_class);
                $fa_color   = substr($parts[1],0,7);
                $fa_bgcolor = !empty($parts[2]) ? substr($parts[2],0,7) : null;
                $fa_class   = str_replace( ['#'.$fa_color, '#'.$fa_bgcolor], ['', ''], $fa_class);
            }
            
            $this->{'class'} = substr($source,0,3) . ' fa-'.$fa_class;
            
            if (!empty($fa_color))
            {
                $this->{'style'} .= "; color: #{$fa_color};";
            }
            
            if (!empty($fa_bgcolor))
            {
                $this->{'style'} .= "; background-color: #{$fa_bgcolor};";
            }
            parent::add('');
        }
        else if (substr($source,0,3) == 'mi:')
        {
            parent::__construct('i');
            
            $mi_class = substr($source,3);
            if (strstr($source, '#') !== FALSE)
            {
                $pieces = explode('#', $mi_class);
                $mi_class = $pieces[0];
                $mi_color = $pieces[1];
            }
            $this->{'class'} = 'material-icons';
            
            $pieces = explode(' ', $mi_class);
            
            if (count($pieces)>1)
            {
                $mi_class = array_shift($pieces);
                $this->{'class'} = 'material-icons ' . implode(' ', $pieces);
            }
            
            if (isset($mi_color))
            {
                $this->{'style'} = "color: #{$mi_color};";
            }
            parent::add($mi_class);
        }
        else if (substr($source,0,4) == 'http')
        {
            parent::__construct('img');
            // assign the image path
            $this->{'src'} = $source;
            $this->{'border'} = 0;
        }
        else if (file_exists($source))
        {
            parent::__construct('img');
            // assign the image path
            $this->{'src'} = $source;
            $this->{'border'} = 0;
        }
        else if (substr($source,0,12) == 'download.php')
        {
            parent::__construct('img');
            // assign the image path
            $this->{'src'} = $source;
            $this->{'border'} = 0;
        }
        else if (file_exists("app/images/{$source}"))
        {
            parent::__construct('img');
            // assign the image path
            $this->{'src'} = "app/images/{$source}";
            $this->{'border'} = 0;
        }
        else if (file_exists("lib/adianti/images/{$source}"))
        {
            parent::__construct('img');
            // assign the image path
            $this->{'src'} = "lib/adianti/images/{$source}";
            $this->{'border'} = 0;
        }
        else
        {
            parent::__construct('i');
        }
    }
}
