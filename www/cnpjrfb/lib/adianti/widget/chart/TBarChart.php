<?php
namespace Adianti\Widget\Chart;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;

use Exception;

/**
 * Bar chart widget
 *
 * @version    8.6
 * @package    widget
 * @subpackage chart
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TBarChart extends TChartBase
{
    private $xlabels;
    private $ylabel;
    private $datasets;
    private $horizontal;
    private $stack;
    
    /**
     * Define x axis labels
     * @param $labels array with labels
     */
    public function setXLabels($labels)
    {
        $this->xlabels = $labels;
    }
    
    /**
     * Define the y axis label
     * @param $label label
     */
    public function setYLabel($label)
    {
        $this->ylabel = $label;
    }
    
    /**
     * Define the y axis label
     * @param $label label
     */
    public function makeHorizontal()
    {
        $this->horizontal = true;
    }
    
    /**
     * Enable stack
     */
    public function makeStack()
    {
        $this->stack = true;
    }
    
    /**
     * add a dataset in the chart
     * @param $name Dataset name
     * @param $data Dataset values
     */
    public function addDataset($name, $data)
    {
        $this->datasets[$name] = $data;
    }
    
    /**
     * Show widget
     */
    public function show()
    {
        $template = file_get_contents('lib/adianti/include/components/tbarchart/tbarchart.html');
        $template = str_replace('{chart_id}', uniqid(), $template);
        $template = str_replace('{title}', (string) $this->title, $template);
        $template = str_replace('{data}', json_encode($this->datasets), $template);
        $template = str_replace('{xlabels}', json_encode($this->xlabels), $template);
        $template = str_replace('{height}', $this->height, $template);
        $template = str_replace('{indexAxis}', ((bool) $this->horizontal ? 'y' : 'x'), $template);
        $template = str_replace('{scales}', json_encode( $this->stack ? (object) [ 'x' => ['stacked'=>true], 'y' => ['stacked'=>true]] : (object) []), $template);
        $template = str_replace('{numberPrefix}', $this->numberPrefix, $template);
        $template = str_replace('{decimals}', $this->numericMask[0], $template);
        $template = str_replace('{decimalsSeparator}', $this->numericMask[1], $template);
        $template = str_replace('{thousandSeparator}', $this->numericMask[2], $template);
        
        $colors = parent::getColors();
        
        $datasets = [];
        $i = 0;
        if (!empty($this->datasets))
        {
            foreach ($this->datasets as $name => $values)
            {
                $datasets[] = ['label' => $name, 'data' => $values, 'backgroundColor' => $colors[$i] ];
                $i++;
            }
        }
        
        $template = str_replace('{datasets}', json_encode($datasets), $template);
        parent::add($template);
        
        parent::show();
    }
}