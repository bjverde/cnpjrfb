<?php
/**
 * Excel writer
 *
 * @version    8.0
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license-template
 */
class TTableWriterXLS implements ITableWriter
{
    private $xls;
    private $styles;
    private $currentTable;
    private $lastStyle;
    private $rowcounter;
    private $colcounter;
    private $columnwidths;
    private $colorIndex;
    private $mapedColors;
    private $conversion;
    private $formatBuffer;
    private $footerCallback;
    
    /**
     * Constructor method
     */
    public function __construct($widths)
    {
        $this->styles = array();
        $this->rowcounter = -1;
        $this->colcounter = -1;
        $this->colorIndex = 8;
        $this->conversion = 6;
        $this->mapedColors = array();
        $this->formatBuffer = array();
        
        $this->xls = new Spreadsheet_Excel_Writer_Workbook;
        $this->xls->setVersion(8);
        
        $this->currentTable = $this->xls-> addWorksheet ();
        $this->columnwidths= $widths;
        $i = 0;
        foreach ($widths as $columnwidth)
        {
            $this->currentTable->setColumn($i, $i, $columnwidth / $this->conversion);
            $i++;
        }
        
        $this->rowcounter = -1;
    }
    
    /**
     * Set Header callback
     */
    public function setHeaderCallback( $callback )
    {
        call_user_func($callback, $this);
    }
    
    /**
     * Set Footer callback
     */
    public function setFooterCallback( $callback )
    {
        $this->footerCallback = $callback;
    }
    
    /**
     * Add a style in the document
     * @param @stylename style name
     * @param @fontface  font face name
     * @param @fontsize  font face size
     * @param @fontstyle font face style (bold, italic)
     * @param @fontcolor font face color
     */
    public function addStyle($stylename, $fontface, $fontsize, $fontstyle, $fontcolor, $fillcolor, $border = '')
    {
        $format_left   = $this->xls-> addFormat ();
        $format_center = $this->xls-> addFormat ();
        $format_right  = $this->xls-> addFormat ();
        
        if (strstr($fontstyle, 'B'))
        {
            $format_left-> setBold (1);
            $format_center-> setBold (1);
            $format_right-> setBold (1);
        }
        if (strstr($fontstyle, 'I'))
        {
            $format_left-> setItalic ();
            $format_center-> setItalic ();
            $format_right-> setItalic ();
        }
        if (strstr($fontstyle, 'U'))
        {
            $format_left-> setUnderline ();
            $format_center-> setUnderline ();
            $format_right-> setUnderline ();
        }
        if (!isset($this->mapedColors[$fontcolor]))
        {
            $rgb255 = self::rgb2int255($fontcolor);
            $this->xls-> setCustomColor ($this->colorIndex, $rgb255[0], $rgb255[1], $rgb255[2]);
            $this->mapedColors[$fontcolor] = $this->colorIndex;
            $this->colorIndex ++;
        }
        $format_left-> setColor ($this->mapedColors[$fontcolor]);
        $format_center-> setColor ($this->mapedColors[$fontcolor]);
        $format_right-> setColor ($this->mapedColors[$fontcolor]);
        
        if (!isset($this->mapedColors[$fillcolor]))
        {
            $rgb255 = self::rgb2int255($fillcolor);
            $this->xls-> setCustomColor ($this->colorIndex, $rgb255[0], $rgb255[1], $rgb255[2]);
            $this->mapedColors[$fillcolor] = $this->colorIndex;
            $this->colorIndex ++;
        }
        $format_left-> setFgColor ($this->mapedColors[$fillcolor]);
        $format_center-> setFgColor ($this->mapedColors[$fillcolor]);
        $format_right-> setFgColor ($this->mapedColors[$fillcolor]);
        
        $format_left-> setFontFamily ($fontface);
        $format_center-> setFontFamily ($fontface);
        $format_right-> setFontFamily ($fontface);
        
        $format_left-> setSize ($fontsize);
        $format_center-> setSize ($fontsize);
        $format_right-> setSize ($fontsize);
        
        if (strstr($border, 'L'))
        {
            $format_left-> setLeft (1);
            $format_center-> setLeft (1);
            $format_right-> setLeft (1);
        }
        if (strstr($border, 'T'))
        {
            $format_left-> setTop (1);
            $format_center-> setTop (1);
            $format_right-> setTop (1);
        }
        if (strstr($border, 'R'))
        {
            $format_left-> setRight (1);
            $format_center-> setRight (1);
            $format_right-> setRight (1);
        }
        if (strstr($border, 'B'))
        {
            $format_left-> setBottom (1);
            $format_center-> setBottom (1);
            $format_right-> setBottom (1);
        }
        
        $format_left-> setAlign ('left');
        $format_center-> setAlign ('center');
        $format_right-> setAlign ('right');
        $this->styles[$stylename]['format']['left']   = $format_left;
        $this->styles[$stylename]['format']['center'] = $format_center;
        $this->styles[$stylename]['format']['right']  = $format_right;
        
        $this->styles[$stylename]['fillcolor'] = $fillcolor;
        $this->styles[$stylename]['fontface']  = $fontface;
        $this->styles[$stylename]['fontsize']  = $fontsize;
        $this->styles[$stylename]['fontstyle'] = $fontstyle;
        $this->styles[$stylename]['border']    = $border;
    }
    
    /**
     * Add a row in the table
     * @param $bgcolor row background color
     * @param $stylename row style
     */
    public function addRow($stylename = NULL)
    {
        $this->rowcounter ++;
        $this->colcounter = -1;
        
        if (isset($stylename))
        {
            $this->lastStyle = $stylename;
        }
    }
    
    /**
     * Add a cell in the row of the table
     * @param $content   cell content
     * @param $align     cell align
     * @param $stylename cell style name
     * @param $width     cell width
     * @param $colspan   cell colspan
     */
    public function addCell($content, $align, $stylename = NULL, $colspan = 1)
    {
        $this->colcounter ++;
        
        if (isset($this->styles[$stylename]['format'][$align]))
        {
            $format = $this->styles[$stylename]['format'][$align];
            $this->lastStyle = $stylename;
        }
        else
        {
            $format = $this->styles[$this->lastStyle]['format'][$align];
        }
        
        $content = AdiantiStringConversion::assureISO($content);
        
        $this->currentTable-> write($this->rowcounter, $this->colcounter, $content, $format);
        
        if ($colspan>1)
        {
            // usado somente para preencher outras células e gerar borda ao redor de todo o merge
            for ($n=1; $n< $colspan; $n++)
            {
                $this->currentTable-> write ($this->rowcounter, $this->colcounter+$n, '', $format);
            }
            $this->currentTable-> mergeCells ($this->rowcounter, $this->colcounter, $this->rowcounter, $this->colcounter + $colspan -1);
            $this->colcounter += ($colspan -1);
        }
    }
    
    /**
     * Converts a RGB color into an array 0-1
     * @param $rgb RGB color string
     */
    private function rgb2int($rgb)
    {
        $hex_red   = substr($rgb,1,2);
        $hex_green = substr($rgb,3,2);
        $hex_blue  = substr($rgb,5,2);
        
        $dec_red = hexdec($hex_red);
        $dec_green = hexdec($hex_green);
        $dec_blue = hexdec($hex_blue);
    
        $int_red = $dec_red/255;
        $int_green = $dec_green/255;
        $int_blue = $dec_blue/255;
    
        return array($int_red, $int_green, $int_blue);
    }
    
    /**
     * Converts a RGB color into an array 0-255
     * @param $rgb RGB color string
     */
    private function rgb2int255($rgb)
    {
        $ints = self::rgb2int($rgb);
        $ints[0] = $ints[0] * 255;
        $ints[1] = $ints[1] * 255;
        $ints[2] = $ints[2] * 255;
        return $ints;
    }
    
    /**
     * Save the table content
     * @param $filename path of the output file
     */
    public function save($filename)
    {
        if (is_callable($this->footerCallback))
        {
            call_user_func($this->footerCallback, $this);
        }
        
        if ( (file_exists($filename) AND !is_writable($filename)) OR (!is_writable(dirname($filename))) )
        {
            throw new Exception(_t('Permission denied') . ': '. $filename);
        }
        $this->xls-> close ($filename);
        unset($this->xls);
        return TRUE;
    }
}
