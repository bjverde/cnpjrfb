<?php

use Picqer\Barcode\BarcodeGeneratorPNG;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/**
 * Barcode generator
 *
 * @version    8.0
 * @package    app
 * @subpackage lib
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 */
class AdiantiBarcodeDocumentGenerator extends AdiantiPDFDesigner
{
    private $barcodeMethod;    // {Ean13, Upca, Code39, i25, Codabar}
    private $leftMargin;       // Left margin
    private $topMargin;        // Top margin
    private $labelWidth;       // Label width in mm
    private $labelHeight;      // Label height in mm
    private $spaceBetween;     // Space between labels
    private $rowsPerPage;      // Label rows per page
    private $colsPerPage;      // Label cols per page
    private $fontSize;         // Text font size
    private $barcodeHeight;    // Barcode Height
    private $imageMargin;      // Image Margin
    private $labelTemplate;    // Label template
    private $objects;          // Database Objects
    private $barcodeContent;   // Barcode content
    private $standards;        // Barcode standards
    
    /**
     * Constructor method
     */
    public function __construct($orientation = 'p', $format = 'a4', $unit = 'mm')
    {
        parent::__construct($orientation, $format, $unit);
        parent::SetAutoPageBreak(false);
        
        $this->standards = ['C39', 'C39+', 'C39E', 'C39E+', 'C93', 'S25', 'S25+', 'I25', 'I25+', 'C128', 'C128A', 'C128B', 'C128C', 'EAN2', 'EAN5', 'EAN8', 'EAN13', 'UPCA', 'UPCE', 'MSI', 'MSI+', 'POSTNET', 'PLANET', 'RMS4CC', 'KIX', 'IMB', 'CODABAR', 'CODE11', 'PHARMA', 'PHARMA2T'];
        
        // set defaults
        $this->objects       = [];
        $this->barcodeMethod = 'EAN13';
        $this->leftMargin    = 12;
        $this->topMargin     = 12;
        $this->labelWidth    = 64;
        $this->labelHeight   = 54;
        $this->spaceBetween  = 4;
        $this->rowsPerPage   = 5;
        $this->colsPerPage   = 3;
        $this->fontSize      = 12;
        $this->barcodeHeight = 15;
        $this->imageMargin   = 0;
        $this->labelTemplate = '#barcode#';
    }
    
    /**
     * Set barcode properties
     */
    public function setProperties($properties)
    {
        if (isset($properties['barcodeMethod']))
        {
            if (!in_array( strtoupper($properties['barcodeMethod']), $this->standards))
            {
                throw new Exception('Method not found: '. $properties['barcodeMethod']);
            }
            $this->barcodeMethod = strtoupper($properties['barcodeMethod']);
        }
        
        $this->leftMargin    = $properties['leftMargin'];
        $this->topMargin     = $properties['topMargin'];
        $this->labelWidth    = $properties['labelWidth'];
        $this->labelHeight   = $properties['labelHeight'];
        $this->spaceBetween  = $properties['spaceBetween'];
        $this->rowsPerPage   = $properties['rowsPerPage'];
        $this->colsPerPage   = $properties['colsPerPage'];
        $this->fontSize      = $properties['fontSize'];
        $this->barcodeHeight = $properties['barcodeHeight'];
        $this->imageMargin   = $properties['imageMargin'];
    }
    
    /**
     * Set label template
     */
    public function setLabelTemplate($template)
    {
        $this->labelTemplate = str_replace('<br>', "\n", $template);
    }
    
    /**
     * Add Database object to be processed
     */
    public function addObject(TRecord $object)
    {
        $this->objects[] = $object;
    }
    
    /**
     * Set attribute name or mask that contains barcode
     */
    public function setBarcodeContent($content)
    {
        $this->barcodeContent = $content;
    }
    
    /**
     * Generate barcodes
     */
    public function generate()
    {
        parent::SetMargins($this->leftMargin, $this->topMargin, 0);
        parent::SetFont('Arial', '', $this->fontSize);
        parent::SetFillColor(0, 0, 0);
        parent::AddPage();
        
        $barcodemask = $this->barcodeContent;
        $lineBreak   = ($this->fontSize/3) +1;
        
        if (!empty($this->objects))
        {
            $col = 0;
            $row = 0;
            $y = $this->topMargin;
            $counter = 1;
            foreach ($this->objects as $key => $object)
            {
                $barcode = isset($object->$barcodemask) ? $object->$barcodemask : $object->render($barcodemask);
                
                if (!empty($barcode))
                {
                    $label = $object->render($this->labelTemplate);
                    
                    parent::SetY($y);
                    
                    // iterate rows
                    foreach (explode("\n", $label) as $label_line)
                    {
                        // horizontal positioning
                        if ($col)
                        {
                            parent::SetX(parent::GetX() + $this->spaceBetween + ($this->labelWidth * $col));
                        }
                        
                        if (trim($label_line) == '#barcode#')
                        {
                            $rand   = mt_rand(1000000000, 1999999999);
                            $output = "tmp/barcode_{$counter}_{$rand}.png";
                            $generator = new BarcodeGeneratorPNG;
                            $img = $generator->getBarcode($barcode, $this->barcodeMethod, 2, (int) ($this->barcodeHeight * 3.78));
                            file_put_contents($output, $img);
                            list($w,$h) = $this->Image($output, parent::GetX() + $this->imageMargin, parent::GetY(), $this->labelWidth -10 - $this->imageMargin, 0, '', '', true);
                            
                            unlink($output);
                            parent::Ln($h);
                            parent::SetX(parent::GetX() - $this->imageMargin);
                        }
                        else if (trim($label_line) == '#qrcode#')
                        {
                            $rand   = mt_rand(1000000000, 1999999999);
                            $output = "tmp/barcode_{$counter}_{$rand}.png";
                            
                            $renderer = new ImageRenderer(new RendererStyle((int) ($this->barcodeHeight * 3.78),2), new ImagickImageBackEnd);
                            
                            $writer = new Writer($renderer);
                            $writer->writeFile($barcode, $output);
                            
                            $imagick_image = new Imagick($output);
                            $imagick_image->setCompressionQuality(100);
                            $imagick_image->writeImage("png24:$output");
                            list($w,$h) = $this->Image($output, parent::GetX() + $this->imageMargin, parent::GetY(), 0, $this->barcodeHeight, '', '', true);
                            
                            unlink($output);
                            parent::Ln($h);
                            parent::SetX(parent::GetX() - $this->imageMargin);
                        }
                        else
                        {
                            parent::writeHTML(parent::GetX(), parent::GetY(), AdiantiStringConversion::assureISO($label_line));
                            parent::Ln( $lineBreak );
                        }
                    }
                    
                    // check row and col change
                    if (++$col == $this->colsPerPage)
                    {
                        $y = ($this->labelHeight * ++$row) + $this->topMargin;
        
                        if ($row > 0 and $row % $this->rowsPerPage == 0 and $counter< count($this->objects))
                        {
                            parent::AddPage();
                            $y = $this->topMargin;
                            $row = 0;
                        }
        
                        $col = 0;
                    }
                }
                
                $counter ++;
            }
        }
    }
    
    /**
     * Save file
     */
    public function save($filename)
    {
        parent::Output($filename);
    }
}
