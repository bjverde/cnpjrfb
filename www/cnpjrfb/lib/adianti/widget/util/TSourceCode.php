<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Base\TElement;
use Adianti\Util\AdiantiStringConversion;

/**
 * SourceCode View
 *
 * @version    7.1
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSourceCode
{
    private $content;
    private $row_numbers;
    
    /**
     * Load a PHP file
     * @param $file Path to the PHP file
     */
    public function loadFile($file)
    {
        if (!file_exists($file))
        {
            return FALSE;
        }
        
        $this->content = AdiantiStringConversion::assureUnicode(file_get_contents($file));
        
        return TRUE;
    }
    
    /**
     * Load from string
     */
    public function loadString($content)
    {
        $this->content = AdiantiStringConversion::assureUnicode($content);
    }
    
    /**
     * Generate row numbers
     */
    public function generateRowNumbers()
    {
        $this->row_numbers = true;
    }
    
    /**
     * Insert row numbers
     */
    public function insertRowNumbers($highlighted_string)
    {
        $color = ini_get('highlight.html');
        $highlighted_string = str_replace('<code><span style="color: '.$color.'">', '<code><span style="color: #000000"><ol class="linenums"><li>', $highlighted_string);
        $highlighted_string = str_replace("</span>\n</code>", "</li></ol></span>\n</code>", $highlighted_string);
        $first = TRUE;
        $content = preg_split ('/(<(?:[^<>]+(?:"[^"]*"|\'[^\']*\')?)+>)/', trim($highlighted_string), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        
        $return = '';
        foreach ($content as $line)
        {
            if (substr(trim($line), 0, 20) == '<span style="color: ')
            {
                $color = substr(trim($line), 20, 7);
                $is_opened = TRUE;
            }
            
            if (substr(trim($line), 0, 7) == '</span>')
            {
                $is_opened = FALSE;
            }
            
            if ($line == '<br />')
            {
                if ($is_opened)
                {
                    $return .= '</span>';
                }
                $return .=  '</li>';
                
                $return .=  '<li>';
                if ($is_opened)
                {
                    $return .= '<span style="color: '.$color.'">';
                }
            }
            else
            {
                $return .= $line;
            }
        }
        
        return $return;
    }
    
    /**
     * Show the highlighted source code
     */
    public function show()
    {
        $span = new TElement('span');
        $span->{'style'} = 'font-size:10pt';
        $span->{'class'} = 'tsourcecode';
        
        if ($this->row_numbers)
        {
            $span->add($this->insertRowNumbers(highlight_string($this->content, TRUE)));
        }
        else
        {
            $span->add(highlight_string($this->content, TRUE));
        }
        $span->show();
    }
}
