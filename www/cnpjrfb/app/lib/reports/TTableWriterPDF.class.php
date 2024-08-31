<?php
/**
 * PDF Writer
 *
 * @version    7.6
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license-template
 */
class TTableWriterPDF implements ITableWriter
{
    private $styles;
    private $pdf;
    private $widths;
    private $colcounter;
    
    /**
     * Constructor
     * @param $widths Array with column widths
     */
    public function __construct($widths, $orientation='P', $format = 'A4')
    {
        $this->widths = $widths;
        $this->styles = array();
        
        $sizes = ['A3' => [841.89,1190.55],
                  'A4' => [595.28,841.89],
                  'A5' => [420.94,595.28],
                  'LETTER' => [612,792],
                  'LEGAL' => [612,1008]];
        
        $total_width = array_sum($this->widths);
        $page_width = ($orientation == 'P' ? $sizes[strtoupper($format)][0] : $sizes[strtoupper($format)][1]) -50;
        
        if ($total_width > $page_width)
        {
            foreach ($this->widths as $key => $width)
            {
                $this->widths[$key] = ($width / $total_width) * $page_width;
            }
        }
        
        // define o locale
        setlocale(LC_ALL, 'POSIX');
        // cria o objeto FPDF
        $this->pdf = new FPDF($orientation, 'pt', $format);
        $this->pdf->Open();
        $this->pdf->AddPage();
    }
    
    /**
     * Set Header callback
     */
    public function setHeaderCallback( $callback )
    {
        // call the first time
        call_user_func($callback, $this);
        $this->pdf->setHeaderCallback($callback, $this);
    }
    
    /**
     * Set Footer callback
     */
    public function setFooterCallback( $callback )
    {
        $this->pdf->setFooterCallback($callback, $this);
    }
    
    /**
     * Returns the native writer
     */
    public function getNativeWriter()
    {
        return $this->pdf;
    }
    
    /**
     * Add a new style
     * @param @stylename style name
     * @param @fontface  font face
     * @param @fontsize  font size
     * @param @fontstyle font style (B=bold, I=italic)
     * @param @fontcolor font color
     * @param @fillcolor fill color
     */
    public function addStyle($stylename, $fontface, $fontsize, $fontstyle, $fontcolor, $fillcolor, $border = null)
    {
        $border = is_null($border) ? 1 : $border;
        $this->styles[$stylename] = array($fontface, $fontsize, $fontstyle, $fontcolor, $fillcolor, $border);
    }
    
    /**
     * Apply a given style
     * @param $stylename style name
     */
    public function applyStyle($stylename)
    {
        // verifica se o estilo existe
        if (isset($this->styles[$stylename]))
        {
            $style = $this->styles[$stylename];
            // obtém os atributos do estilo
            $fontface    = $style[0];
            $fontsize    = $style[1];
            $fontstyle   = $style[2];
            $fontcolor   = $style[3];
            $fillcolor   = $style[4];
            
            // aplica os atributos do estilo
            $this->pdf->SetFont($fontface, $fontstyle); // fonte
            $this->pdf->SetFontSize($fontsize); // estilo
            $colorarray = self::rgb2int255($fontcolor);
            // cor do texto
            $this->pdf->SetTextColor($colorarray[0], $colorarray[1], $colorarray[2]);
            $colorarray = self::rgb2int255($fillcolor);
            // cor de preenchimento
            $this->pdf->SetFillColor($colorarray[0], $colorarray[1], $colorarray[2]);
        }
    }
    
    /**
     * Convert one RGB color into array of decimals
     * @param $rgb String with a RGB color
     */
    private function rgb2int255($rgb)
    {
        $red   = hexdec(substr($rgb,1,2));
        $green = hexdec(substr($rgb,3,2));
        $blue  = hexdec(substr($rgb,5,2));
        
        return array($red, $green, $blue);
    }
    
    /**
     * Add a new row inside the table
     */
    public function addRow()
    {
        $this->pdf->Ln(); // quebra de linha
        $this->colcounter = 0;
    }
    
    /**
     * Add a new cell inside the current row
     * @param $content   cell content
     * @param $align     cell align
     * @param $stylename style to be used
     * @param $colspan   colspan (merge) 
     */
    public function addCell($content, $align, $stylename, $colspan = 1)
    {
        if (is_null($stylename) OR !isset($this->styles[$stylename]) )
        {
            throw new Exception(TAdiantiCoreTranslator::translate('Style ^1 not found in ^2', $stylename, __METHOD__ ) );
        }
        
        $this->applyStyle($stylename); // aplica o estilo
        $fontsize = $this->styles[$stylename][1]; // obtém a fonte
        
        $content = AdiantiStringConversion::assureISO($content);
        
        $width = 0;
        // calcula a largura da célula (incluindo as mescladas)
        for ($n=$this->colcounter; $n<$this->colcounter+$colspan; $n++)
        {
            $width += $this->widths[$n];
        }
        // exibe a célula com o conteúdo passado
        $this->pdf->Cell( $width, $fontsize * 1.5, $content, $this->styles[$stylename][5], 0, strtoupper(substr($align,0,1)), true);
        $this->colcounter += $colspan;
    }
    
    /**
     * Save the current file
     * @param $filename file name
     */
    public function save($filename)
    {
        $this->pdf->Output($filename);
        return TRUE;
    }
}
?>