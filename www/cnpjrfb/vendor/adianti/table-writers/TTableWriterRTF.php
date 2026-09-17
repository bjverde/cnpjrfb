<?php
/**
 * RTF writer
 *
 * @version    8.0
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license-template
 */
class TTableWriterRTF implements ITableWriter
{
    private $rtf;
    private $styles;
    private $table;
    private $rowcounter;
    private $colcounter;
    private $widths;
    
    /**
     * Constructor
     * @param $widths Array with column widths
     */
    public function __construct($widths, $orientation='P', $format = 'A4')
    {
        // armazena as larguras
        $this->widths = $widths;
        
        // inicializa atributos
        $this->styles = array();
        $this->rowcounter = 0;
        
        // instancia a classe PHPRtfLite
        $this->rtf = new PHPRtfLite;
        $this->rtf->setMargins(2, 2, 2, 2);
        
        $pagesize = array();
        $pagesize['A5']     = array(14.8, 21);
        $pagesize['A4']     = array(21, 29.7);
        $pagesize['A3']     = array(29.7, 42);
        $pagesize['Letter'] = array(21.59, 27.94);
        $pagesize['Legal']  = array(21.59, 35.57);
        
        if (isset($pagesize[$format]))
        {
            $size = $pagesize[$format];
            
            if ($orientation == 'P')
            {
                $this->rtf-> setPaperWidth ($size[0]);
                $this->rtf-> setPaperHeight ($size[1]);
            }
            else
            {
                $this->rtf-> setPaperWidth ($size[1]);
                $this->rtf-> setPaperHeight ($size[0]);
            }
        }
        
        foreach ($this->widths as $key => $columnwidth)
        {
            $this->widths[$key] = $columnwidth / 28;
        }
        
        $total_width = array_sum($this->widths);
        $page_width = ($orientation == 'P' ? $pagesize[strtoupper($format)][0] : $pagesize[strtoupper($format)][1]) -4;
        
        if ($total_width > $page_width)
        {
            foreach ($this->widths as $key => $width)
            {
                //echo "($width / $total_width) * $page_width <br>";
                $this->widths[$key] = ($width / $total_width) * $page_width;
            }
        }
        
        // acrescenta uma seção ao documento
        $section = $this->rtf->addSection();
        
        // acrescenta uma tabela à seção
        $this->table = $section->addTable();
        
        // acrescenta as colunas na tabela
        foreach ($this->widths as $columnwidth)
        {
            $this->table->addColumn($columnwidth);
        }
    }
    
    /**
     * Returns the native writer
     */
    public function getNativeWriter()
    {
        return $this->rtf;
    }
    
    /**
     * Set Header callback
     */
    public function setHeaderCallback( $callback )
    {
        $container = $this->rtf->addHeader();
        $table = $container->addTable();
        
        foreach ($this->widths as $columnwidth)
        {
            $table->addColumn($columnwidth);
        }
        
        $aux = $this->table;
        $this->table = $table;
        
        $this->rowcounter = 0;
        $this->colcounter = 1;
        
        call_user_func($callback, $this);
        
        $this->table = $aux;
        
        $this->rowcounter = 0;
        $this->colcounter = 1;
    }
    
    /**
     * Set Footer callback
     */
    public function setFooterCallback( $callback )
    {
        $container = $this->rtf->addFooter();
        $table = $container->addTable();
        
        foreach ($this->widths as $columnwidth)
        {
            $table->addColumn($columnwidth);
        }
        
        $aux = $this->table;
        $this->table = $table;
        
        $this->rowcounter = 0;
        $this->colcounter = 1;
        
        call_user_func($callback, $this);
        
        $this->table = $aux;
        
        $this->rowcounter = 0;
        $this->colcounter = 1;
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
        // instancia um objeto para estilo de fonte (PHPRtfLite_Font)
        $font = new PHPRtfLite_Font($fontsize, $fontface, $fontcolor);
        $font->setBold(strstr($fontstyle, 'B'));
        $font->setItalic(strstr($fontstyle, 'I'));
        $font->setUnderline(strstr($fontstyle, 'U'));
        
        //  armazena o objeto fonte e a cor de preenchimento
        $this->styles[$stylename]['font']    = $font;
        $this->styles[$stylename]['bgcolor'] = $fillcolor;
    }
    
    /**
     * Add a new row inside the table
     */
    public function addRow()
    {
        $this->rowcounter ++;
        $this->colcounter = 1;
        $this->table->addRow();
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
        
        // obtém a fonte e a cor de preenchimento
        $font      = $this->styles[$stylename]['font'];
        $fillcolor = $this->styles[$stylename]['bgcolor'];
        
        $content = AdiantiStringConversion::assureUnicode($content);
        
        // escreve o conteúdo na célula utilizando a fonte e alinhamento
        $this->table->writeToCell($this->rowcounter, $this->colcounter,
                      $content, $font, new PHPRtfLite_ParFormat($align));
                      
        // define a cor de fundo para a célula
        $this->table->setBackgroundForCellRange($fillcolor, $this->rowcounter, $this->colcounter,
                                                $this->rowcounter, $this->colcounter);

        if ($colspan>1)
        {
            // mescla as células caso necessário
            $this->table->mergeCellRange($this->rowcounter, $this->colcounter,
                                         $this->rowcounter, $this->colcounter + $colspan -1);
        }
        $this->colcounter += $colspan;
    }
    
    /**
     * Save the current file
     * @param $filename file name
     */
    public function save($filename)
    {
        // instancia um objeto para estilo de borda
        $border    = PHPRtfLite_Border::create($this->rtf, 0.7, '#000000');
        
        // liga as bordas na tabela  
        $this->table->setBorderForCellRange($border, 1, 1, $this->table->getRowsCount(),
                                            $this->table->getColumnsCount());
        
        // armazena o documento em um arquivo
        $this->rtf->save($filename);
        return TRUE;
    }
}
