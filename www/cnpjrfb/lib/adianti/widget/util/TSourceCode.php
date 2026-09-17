<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Base\TElement;
use Adianti\Util\AdiantiStringConversion;

/**
 * SourceCode View
 *
 * @version    8.6
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TSourceCode
{
    private $content;
    private $row_numbers;
    private $strip_tags;
    
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
     *
     */
    public function stripTags()
    {
        $this->strip_tags = true;
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
        $raw_content = (string) $this->content;
        if (isset($this->strip_tags) && $this->strip_tags)
        {
            $raw_content = str_replace("<?php\n", '', $raw_content);
        }
        
        $copy = new TElement('div');
        $copy->{'style'} = 'border: 1px solid silver;float: right;padding: 5px;border-radius: 5px;';
        $copy->{'class'} = 'btn';
        $copy->add(new TImage('far:copy white'));
        $copy->{'onclick'} = "__adianti_copy_to_clipboard64('".base64_encode($raw_content)."')";
        
        $span = new TElement('span');
        $span->{'style'} = 'font-size:10pt';
        $span->{'class'} = 'tsourcecode';
        
        $span->add($copy);
        
        $content = highlight_string($this->content, TRUE);
        
        if (isset($this->strip_tags) && $this->strip_tags)
        {
            $content = str_replace('&lt;?php<br />', '', $content);
        }
        
        if ($this->row_numbers)
        {
            $span->add($this->insertRowNumbers($content));
        }
        else
        {
            $span->add($content);
        }
        $span->show();
    }
    
    /**
     * Returns the element content as a string
     */
    public function getContents()
    {
        ob_start();
        $this->show();
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}
