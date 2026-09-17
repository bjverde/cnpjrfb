<?php
use Adianti\Util\AdiantiTemplateHandler;

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * HTML Document parser
 *
 * @version    8.0
 * @package    app
 * @subpackage lib
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 */
class AdiantiHTMLDocumentParser
{
    private $file;
    private $content;
    private $masterObject;
    private $details;
    private $replaces;
    private $totals;
    private $showEmptyDetails;
    private $enabledTranslation;
    private $DOMOptions;
    
    /**
     * Constructor
     * @param  $file HTML Filename
     */
    public function __construct($file = null)
    {
        if (file_exists($file))
        {
            $this->file     = $file;
            $this->content  = file_get_contents($file);
        }
        else
        {
            $this->content  = '';
        }
        
        $this->enabledTranslation = FALSE;
        $this->showEmptyDetails = true;
        $this->details  = [];
        $this->replaces = [];
        $this->totals   = [];
        $this->DOMOptions = null;
    }
    
    /**
     * Enable translation inside template
     */
    public function enableTranslation()
    {
        $this->enabledTranslation = TRUE;
    }
    
    /**
     * Disable empty details
     */
    public function hideEmptyDetails()
    {
        $this->showEmptyDetails = false;
    }
    
    /**
     * Create document from string
     */
    public static function newFromString($content)
    {
        $object = new self;
        $object->content = $content;
        return $object;
    }
    
    /**
     * Define the master object to be replaced
     * @param  $object Object
     */
    public function setMaster(TRecord $object)
    {
        $this->masterObject = $object;
    }
    
    /**
     * Define the detail objects
     * @param  $activeRecord Class Name
     * @param  $objects Array of Objects
     */
    public function setDetail($activeRecord, $objects)
    {
        $this->details[$activeRecord] = $objects;
    }
    
    /**
     * Define the replacements
     * @param  $
     * @param  $label  Button's label
     */
    public function replace($search, $replace)
    {
        $this->replaces[$search] = $replace;
    }
    
    /**
     * Fix HTML object sizes
     * @param  $dom Dom object
     */
    public function fixSizes($dom)
    {
        $nodes = $dom->query('[width]');
        foreach ($nodes as $node)
        {
            if ((strstr($node->attr('width'), 'px') == FALSE) and (strstr($node->attr('width'), '%') == FALSE))
            {
                $node->attr('width', $node->attr('width') . 'px');
            }
        }
        
        $nodes = $dom->query('[height]');
        foreach ($nodes as $node)
        {
            if ((strstr($node->attr('height'), 'px') == FALSE) and (strstr($node->attr('height'), '%') == FALSE))
            {
                $node->attr('height', $node->attr('height') . 'px');
            }
        }
    }
    
    /**
     * Process the replacements (master and details)
     */
    public function process()
    {
        $dom = pQuery::parseStr($this->content);
        $this->fixSizes($dom);
        $details = $dom->query('table[data-detailmodel]');

        if($details)
        {
            foreach ($details as $detail) 
            {
                $model = $detail->attr('data-detailmodel');
                if ($model)
                {
                    $body     = $detail->query('tbody');
                    $row_tpl  = $body->html();
                    
                    preg_match_all('/{\$(.*?)}/', $row_tpl, $matches1);
                    preg_match_all('/{{(.*?)}}/', $row_tpl, $matches2);
                    $matches = array_merge((array) $matches1, (array) $matches2);
                    $attributes = $matches[1];
                    
                    if (isset($this->details[$model]))
                    {
                        foreach ($attributes as $attribute)
                        {
                            $this->totals[$model][$attribute]['count'] = 0;
                            $this->totals[$model][$attribute]['sum']   = 0;
                            $this->totals[$model][$attribute]['min']   = null;
                            $this->totals[$model][$attribute]['max']   = null;
                        }
                        
                        $objects = $this->details[$model];
                        if ($objects)
                        {
                            $new_rows = '';
                            foreach ($objects as $object)
                            {
                                $new_row = $row_tpl;
                                foreach ($attributes as $attribute)
                                {
                                    $new_row = str_replace('{{'.$attribute.'}}', $object->$attribute, $new_row);
                                    $new_row = str_replace('{$'.$attribute.'}',  $object->$attribute, $new_row);
                                    
                                    $this->totals[$model][$attribute]['count'] ++;
                                    $this->totals[$model][$attribute]['min'] = (!isset($this->totals[$model][$attribute]['min']) OR $object->$attribute < $this->totals[$model][$attribute]['min']) ? $object->$attribute : $this->totals[$model][$attribute]['min'];
                                    $this->totals[$model][$attribute]['max'] = (!isset($this->totals[$model][$attribute]['max']) OR $object->$attribute > $this->totals[$model][$attribute]['max']) ? $object->$attribute : $this->totals[$model][$attribute]['max'];
                                    
                                    if (is_numeric($object->$attribute))
                                    {
                                        $this->totals[$model][$attribute]['sum'] += $object->$attribute;
                                    }
                                }
                                $new_rows .= $new_row;
                            }
                            $body->html($new_rows);
                        }
                        else
                        {
                            $body->html('');
                            
                            if (!$this->showEmptyDetails)
                            {
                                $detail->html('');
                            }
                        }
                    }
                    
                    $footer  = $detail->query('tfoot');
                    if ($footer->html())
                    {
                        if (!empty($objects))
                        {
                            $foot_tpl  = $footer->html();
                            $new_row = $foot_tpl;
                            preg_match_all('/{\$(.*?)}/', $foot_tpl, $matches1);
                            preg_match_all('/{{(.*?)}}/', $foot_tpl, $matches2);
                            $matches = array_merge((array) $matches1, (array) $matches2);
                            $attributes = $matches[1];
                            
                            foreach ($attributes as $attribute)
                            {
                                $new_row = str_replace('sum({{'.$attribute.'}})',   $this->totals[$model][$attribute]['sum'], $new_row);
                                $new_row = str_replace('sum({$'.$attribute.'})',    $this->totals[$model][$attribute]['sum'], $new_row);
                                $new_row = str_replace('count({{'.$attribute.'}})', $this->totals[$model][$attribute]['count'], $new_row);
                                $new_row = str_replace('count({$'.$attribute.'})',  $this->totals[$model][$attribute]['count'], $new_row);
                                $new_row = str_replace('min({{'.$attribute.'}})',   $this->totals[$model][$attribute]['min'], $new_row);
                                $new_row = str_replace('min({$'.$attribute.'})',    $this->totals[$model][$attribute]['min'], $new_row);
                                $new_row = str_replace('max({{'.$attribute.'}})',   $this->totals[$model][$attribute]['max'], $new_row);
                                $new_row = str_replace('max({$'.$attribute.'})',    $this->totals[$model][$attribute]['max'], $new_row);
                                $new_row = str_replace('avg({{'.$attribute.'}})',   $this->totals[$model][$attribute]['sum'] / $this->totals[$model][$attribute]['count'], $new_row);
                                $new_row = str_replace('avg({$'.$attribute.'})',    $this->totals[$model][$attribute]['sum'] / $this->totals[$model][$attribute]['count'], $new_row);
                            }
                            
                            $footer->html($new_row);
                        }
                        else
                        {
                            $footer->html('');
                        }
                    }
                }
            }
        }

        $html = $dom->html();
        
        if ($this->replaces)
        {
            foreach ($this->replaces as $search => $replace)
            {
                $html = str_replace($search, $replace, $html);
            }
        }
        
        preg_match_all('/{\$(.*?)}/', $html, $matches1);
        preg_match_all('/{{(.*?)}}/', $html, $matches2);
        $matches = array_merge((array) $matches1, (array) $matches2);
        $attributes = $matches[1];
        foreach ($attributes as $attribute)
        {
            $html = str_replace('{$'.$attribute.'}',  $this->masterObject->$attribute, $html);
            $html = str_replace('{{'.$attribute.'}}', $this->masterObject->$attribute, $html);
        }
        $html = AdiantiTemplateHandler::replaceFunctions($html);
        
        if ($this->enabledTranslation)
        {
            $html  = ApplicationTranslator::translateTemplate($html);
        }
        
        $this->content = $html;
        return $html;
    }
    
    /**
     * Add page break
     */
    public function addPageBreak()
    {
        $this->content .= "\n" . '<div style="page-break-before: always"></div>' . "\n";
    }
    
    /**
     * Add line break
     */
    public function addLineBreak()
    {
        $this->content .= '<br>';
    }
    
    /**
     * Add static image
     */
    public function addImage($path, $width = '100%', $height = '100%')
    {
        $this->content .= "<img src='{$path}' width='{$width}' height='{$height}'>";
    }
    
    /**
     * Parse vector image
     */
    public function parseImage($path, $width = '100%', $height = '100%')
    {
        if (file_exists($path))
        {
            $path_info = pathinfo($path);
            $content   = file_get_contents($path);
            
            if ($this->replaces)
            {
                foreach ($this->replaces as $search => $replace)
                {
                    $content = str_replace($search, $replace, $content);
                }
            }
            
            // fix -> object relations preparing for render method
            if (preg_match_all('/\{(.*?)\}/', $content, $matches) )
            {
                foreach ($matches[0] as $match)
                {
                    $content = str_replace( $match, str_replace('-&gt;', '->', $match), $content);
                }
            }
            $content = $this->masterObject->render($content);
            
            $path = 'tmp/'.mt_rand(1000000000, 1999999999).'.' . $path_info['extension'];
            file_put_contents($path, $content);
            $this->content .= "<img src='{$path}' width='{$width}' height='{$height}'>";
        }
        else
        {
            throw new Exception( AdiantiCoreTranslator::translate('File not found') . ': ' . $path );
        }
    }
    
    /**
     * Add content
     */
    public function addContent($content)
    {
        $this->content .= $content;
    }
    
    /**
     * Return the current HTML
     */
    public function getContents()
    {
        return $this->content;
    }
    
    /**
     * Save the HTML content to file
     * @param  $filename Filename
     */
    public function save($filename)
    {
        $html = $this->getContents();
        file_put_contents($filename, $html);
    }
    
    /**
     * Set DOM Options
     */
    public function setDOMOptions($options)
    {
        $this->DOMOptions = $options;
    }
    
    /**
     * Save the HTML content as PDF with DOMPDF
     * @param  $filename Filename
     * @param  $format Page format
     * @param  $orientation Page orientation
     */
    public function saveAsPDF($filename, $format = 'A4', $orientation = 'portrait')
    {
        $html = $this->getContents();
        
        if (!empty($this->DOMOptions))
        {
            $options = $this->DOMOptions;
        }
        else
        {
            $options = new Options();
            $options->set('dpi', '128');
            $options->setIsRemoteEnabled(true);
        }
        $options->setChroot(getcwd());
        
        if (preg_match("/<!DOCTYPE html>/i", $html))
        {
            $options->set('enable_html5_parser', true);
        }
        
        // instantiate and use the dompdf class
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        
        if (is_array($format))
        {
            $dompdf->setPaper( [0, 0, $format[0], $format[1]], $orientation );
        }
        else
        {
            $dompdf->setPaper($format, $orientation);
        }
        
        // Render the HTML as PDF
        $dompdf->render();

        file_put_contents($filename, $dompdf->output());
    }
}
