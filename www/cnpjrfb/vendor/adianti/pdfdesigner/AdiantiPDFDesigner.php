<?php
/**
 * FPDF Adapter that parses XML files from Adianti Framework
 *
 * @version    7.3
 * @package    pdfdesigner
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 * @alias      TPDFDesigner
 */
class AdiantiPDFDesigner extends FPDF
{
    private $current_locale;
    private $elements;
    private $anchors;
    private $orientation;
    private $format;
    private $replaces;
    private $B;
    private $I;
    private $U;
    
    /**
     * Constructor method
     * @param  $orientation Page orientation
     * @param  $format Page format
     * @author Pablo Dall'Oglio
     */
    public function __construct($orientation = 'P', $format = 'a4', $unit = 'pt')
    {
        parent::__construct($orientation, $unit, $format);
        
        $this->setLocale();
        
        parent::SetAutoPageBreak(true);
        parent::SetMargins(0, 0, 0);
        parent::SetCreator('Adianti Studio PDF Designer');
        parent::SetFillColor(255, 255, 255);
        parent::Open();
        parent::AliasNbPages();
        parent::SetX(20);
        
        $this->replaces = array();
        $this->href = '';
        $this->anchors = array();
        $this->orientation = $orientation;
        $this->format = $format;
        parent::SetFont('Arial', '', 10 * 1.3);
    }
    
    /**
     * Load designed elements from XML
     * @param $filename XML file location
     * @author Pablo Dall'Oglio
     */
    public function fromXml($filename)
    {
        if (file_exists($filename))
        {
            $xml = new SimpleXMLIterator(file_get_contents($filename));
            
            $elements = array();
            foreach ($xml as $tag => $xmlobject)
            {
                $properties = (array) $xmlobject;
                array_walk_recursive($properties, array($this, 'arrayToIso8859'));
                
                if ($tag == 'page')
                {
                    $this->format = (string) $properties['format'];
                    $this->orientation = (string) $properties['orientation'];
                }
                else
                {
                    $elements[] = $properties;
                }
            }
            $this->loadElements($elements);
        }
        else
        {
            throw new Exception(_t('File (^1) does not exist', $filename));
        }
    }
    
    /**
     * Load Elements
     * @param $elements Elements (shapes) to load
     * @author Pablo Dall'Oglio
     */
    public function loadElements($elements)
    {
        $this->elements = $elements;
        
        // map anchors
        if ($this->elements)
        {
            foreach ($this->elements as $element)
            {
                if (isset($element['class']) AND $element['class'] == 'Anchor')
                {
                    $anchor_name = $element['name'];
                    $this->anchors[ $anchor_name ] = $element;
                }
            }
        }
    }
    
    /**
     * Put the cursor at the anchor XY position
     * @param $anchor_name Anchor name
     * @returns TRUE if the anchor exists
     * @author  Pablo Dall'Oglio
     */
    public function gotoAnchorXY($anchor_name)
    {
        if (isset($this->anchors[ $anchor_name ]))
        {
            $anchor_x = $this->anchors[ $anchor_name ][ 'x' ];
            $anchor_y = $this->anchors[ $anchor_name ][ 'y' ];
            
            $this->SetY( $anchor_y );
            $this->SetX( $anchor_x );
            
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * Put the cursor at the anchor X position
     * @param $anchor_name Anchor name
     * @returns TRUE if the anchor exists
     * @author  Pablo Dall'Oglio
     */
    public function gotoAnchorX($anchor_name)
    {
        if (isset($this->anchors[ $anchor_name ]))
        {
            $anchor_x = $this->anchors[ $anchor_name ][ 'x' ];
            $this->SetX( $anchor_x );
            
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * Put the cursor at the anchor Y position
     * @param $anchor_name Anchor name
     * @returns TRUE if the anchor exists
     * @author  Pablo Dall'Oglio
     */
    public function gotoAnchorY($anchor_name)
    {
        if (isset($this->anchors[ $anchor_name ]))
        {
            $anchor_y = $this->anchors[ $anchor_name ][ 'y' ];
            $this->SetY( $anchor_y );
            
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * Write at the anchor position
     * @param $anchor_name Anchor name
     * @param $text Text to write
     * @returns TRUE if the anchor exists
     * @author  Pablo Dall'Oglio
     */
    public function writeAtAnchor($anchor_name, $text)
    {
        if ($this->gotoAnchorXY($anchor_name))
        {
            parent::Write($this->FontSizePt, $text);
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * Replace a piece of {text}
     * @param $mark piece to be replaced
     * @param $text new content
     * @author  Pablo Dall'Oglio
     */
    public function replace($mark, $text)
    {
        $this->replaces[$mark] = $text;
    }
    
    /**
     * Generate one PDF page with the parsed elements
     * @author  Pablo Dall'Oglio
     */
    public function generate()
    {
        $this->AddPage( $this->orientation, $this->format );
        $style = '';

        foreach ($this->elements as $element)
        {
            if (isset($element['class']))
            {
                switch ($element['class'])
                {
                    case 'Rectangle':
                        if ($element['shadowoffset'] > 0)
                        {
                            $this->setFillColorRGB($element['shadowcolor']);
                            $this->Rect($element['x'] + $element['shadowoffset'], $element['y'] + $element['shadowoffset'], $element['width'], $element['height'], 'F');
                        }
                        parent::SetLineWidth($element['linewidth']);
                        $this->setDrawColorRGB( $element['linecolor'] );
                        $this->setFillColorRGB( $element['fillcolor'] );
                        $mode = $element['linewidth'] > 0 ? 'FD' : 'F';
                        parent::Rect($element['x'], $element['y'], $element['width'], $element['height'], $mode);
                        break;
                        
                    case 'Ellipse':
                        $x = $element['x'] + ($element['width']/2);
                        $y = $element['y'] + ($element['height']/2);
                        
                        if ($element['shadowoffset'] > 0)
                        {
                            $fillc = $this->rgb2int255($element['shadowcolor']);
                            parent::SetFillColor($fillc[0], $fillc[1], $fillc[2]);
                            $this->ellipse($x + $element['shadowoffset'], $y + $element['shadowoffset'], $element['width']/2, $element['height']/2, 'F');
                        }
                        $mode = $element['linewidth'] > 0 ? 'FD' : 'F';
                        parent::SetLineWidth($element['linewidth']);
                        $this->setDrawColorRGB( $element['linecolor'] );
                        $this->setFillColorRGB( $element['fillcolor'] );
                        $this->ellipse($x, $y , $element['width']/2, $element['height']/2, $mode);
                        
                        break;
                        
                    case 'Text':
                        $height_factor['Courier'] = 0.335;
                        $height_factor['Arial'] = 0.39;
                        $height_factor['Times'] = 0.42;
                        $text = str_replace( array_keys($this->replaces), array_values($this->replaces), $element['text'] );
                        
                        $x = $element['x'] - 2;
                        $y = $element['y'] + ($element['size'] * $height_factor[ $element['font'] ]) - (30 * (1/$element['size']));
                        if ($element['shadowoffset'] > 0)
                        {
                            $this->setFontColorRGB($element['shadowcolor']);
                            parent::SetFont($element['font'], $style, $element['size']);
                            $this->writeHTML($x + $element['shadowoffset'], $y + $element['shadowoffset'], $text);
                        }
                        parent::SetFont($element['font'], $style, $element['size'] );
                        $this->setFontColorRGB($element['color']);
                        $this->writeHTML($x, $y, $text);
                        break;
                        
                    case 'Line':
                        parent::SetLineWidth($element['linewidth']);
                        $this->setDrawColorRGB( $element['linecolor'] );
                        parent::Line($element['x'], $element['y'], $element['x2'], $element['y2']);
                        break;
                        
                    case 'Image':
                        if (file_exists($element['file']))
                        {
                            parent::Image($element['file'], $element['x'], $element['y'], $element['width'], $element['height']);
                        }
                        break;
                }
            }
        }
    }
    
    /**
     * Draws an ellipse
     * @param  $x X
     * @param  $y Y
     * @param  $rx X Ray
     * @param  $ry Y Ray
     * @param  $style Line Style
     * @author Olivier Plathey
     */
    public function ellipse( $x, $y, $rx, $ry, $style = 'D' )
    {
        if ($style=='F')
            $op='f';
        else if ($style=='FD' or $style=='DF')
            $op='B';
        else
            $op='S';

        $lx=4/3*(M_SQRT2-1)*$rx;
        $ly=4/3*(M_SQRT2-1)*$ry;
        $k=$this->k;
        $h=$this->h;

        $this->_out(sprintf('%.2f %.2f m %.2f %.2f %.2f %.2f %.2f %.2f c',
            ($x+$rx)*$k,($h-$y)*$k,
            ($x+$rx)*$k,($h-($y-$ly))*$k,
            ($x+$lx)*$k,($h-($y-$ry))*$k,
            $x*$k,($h-($y-$ry))*$k));
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c',
            ($x-$lx)*$k,($h-($y-$ry))*$k,
            ($x-$rx)*$k,($h-($y-$ly))*$k,
            ($x-$rx)*$k,($h-$y)*$k));
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c',
            ($x-$rx)*$k,($h-($y+$ly))*$k,
            ($x-$lx)*$k,($h-($y+$ry))*$k,
            $x*$k,($h-($y+$ry))*$k));
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c %s',
            ($x+$lx)*$k,($h-($y+$ry))*$k,
            ($x+$rx)*$k,($h-($y+$ly))*$k,
            ($x+$rx)*$k,($h-$y)*$k,
            $op));
    }

    /**
     * Write HTML
     * @param  $x X
     * @param  $y Y
     * @param  $html HTML
     * @author Azeem Abbas (contributor of fpdf.org)
     */
    public function writeHTML( $x, $y, $html )
    {
        $this->SetY($y);
        $this->SetX($x);
        //HTML parser
        $html = str_replace("\n",'<br>',$html);
        $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        foreach($a as $i=>$e)
        {
            if($i%2==0)
            {
                // Text
                if ($this->href)
                {
                    $this->putLink($this->href,$e);
                }
                else
                {
                    $this->Write(5,$e);
                }
            }
            else
            {
                // Tag
                if (substr($e,0,1) == '/')
                {
                    $this->closeTag(strtoupper(substr($e,1)));
                }
                else
                {
                    //Extract attributes
                    $a2   = explode(' ',$e);
                    $tag  = strtoupper(array_shift($a2));
                    $attr = array();
                    foreach ($a2 as $v)
                    {
                        if (preg_match('/^([^=]*)=["\']?([^"\']*)["\']?$/', $v, $a3))
                        {
                            $attr[strtoupper($a3[1])]=$a3[2];
                        }
                    }
                    $this->openTag( $tag, $attr, $x );
                }
            }
        }
    }
    
    /**
     * Open Html TAG
     * @param  $tag Tag
     * @param  $attr Tag attributes
     * @param  $x X position
     * @author Azeem Abbas (contributor of fpdf.org)
     */
    public function openTag($tag,$attr, $x)
    {
        // Opening tag
        if ($tag=='B' or $tag=='I' or $tag=='U')
        {
            $this->setStyle($tag,true);
        }
        if ($tag=='A')
        {
            $this->href=$attr['href'];
        }
        if ($tag=='BR')
        {
            parent::Ln($this->FontSizePt * 1.1);
            parent::SetX($x);
        }
    }
    
    /**
     * Close Html TAG
     * @param  $tag Tag
     * @author Azeem Abbas (contributor of fpdf.org)
     */
    public function closeTag($tag)
    {
        //Closing tag
        if ($tag=='B' or $tag=='I' or $tag=='U')
        {
            $this->setStyle($tag, FALSE);
        }
        if ($tag=='A')
        {
            $this->href='';
        }
    }
    
    /**
     * Set Style
     * @param  $tag Tag
     * @param  $enable Enable
     * @author Azeem Abbas (contributor of fpdf.org)
     */
    public function setStyle($tag,$enable)
    {
        //Modify style and select corresponding font
        $this->$tag+=($enable ? 1 : -1);
        $style='';
        foreach (array('B','I','U') as $s)
        {
            if (isset($this->$s))
            {
                if ($this->$s>0)
                {
                    $style.=$s;
                }
            }
        }
        $this->SetFont('',$style);
    }
    
    /**
     * Put link
     * @param $URL
     * @param $txt
     * @author Azeem Abbas (contributor of fpdf.org)
     */
    public function putLink($URL,$txt)
    {
        parent::SetTextColor(0,0,255);
        $this->setStyle('U',true);
        parent::Write(5,$txt,$URL);
        $this->setStyle('U',false);
        parent::SetTextColor(0);
    }

    /**
     * Change PDF locale
     * @author  Pablo Dall'Oglio
     */
    public function setLocale()
    {
        $this->current_locale = setlocale(LC_ALL, 0);
        
        if (OS == 'WIN')
        {
            setlocale(LC_ALL, 'english');
        }
        else
        {
            setlocale(LC_ALL, 'POSIX');
        }
    }

    /**
     * Back to the old locale
     * @author  Pablo Dall'Oglio
     */
    public function unsetLocale()
    {
        setlocale(LC_ALL, $this->current_locale);
    }

    /**
     * Changes the color
     * @param $color Color in RGB
     * @author  Pablo Dall'Oglio
     */
    public function setFontColorRGB($color)
    {
        $colorR = hexdec(substr($color,1,2));
        $colorG = hexdec(substr($color,3,2));
        $colorB = hexdec(substr($color,5,2));
        
        parent::SetTextColor($colorR, $colorG, $colorB);
    }
    
    /**
     * Changes the fill color
     * @param $color Color in RGB
     * @author  Pablo Dall'Oglio
     */
    public function setFillColorRGB($color)
    {
        $fillc = $this->rgb2int255($color);
        parent::SetFillColor($fillc[0], $fillc[1], $fillc[2]);
    }
    
    /**
     * Changes the draw color
     * @param $color Color in RGB
     * @author  Pablo Dall'Oglio
     */
    public function setDrawColorRGB($color)
    {
        $drawc = $this->rgb2int255($color);
        parent::SetDrawColor($drawc[0], $drawc[1], $drawc[2]);
    }
    
    /**
     * Converts RGB into array(0..255)
     * @param $rgb String RGB color
     * @author  Pablo Dall'Oglio
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
     * Converts RGB into array(0..1)
     * @param $rgb String RGB color
     * @author  Pablo Dall'Oglio
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
     * Converts from UTF8 to ISO
     * @author  Pablo Dall'Oglio
     */
    private function arrayToIso8859(&$value, $key)
    {
        if (is_scalar($value))
        {
            $value = utf8_decode($value);
        }
    }
    
    /**
     * Saves the PDF
     * @param $output Output path
     * @author Pablo Dall'Oglio
     */
    public function save($output)
    {
        parent::Output($output);
        $this->unsetLocale();
    }
}
