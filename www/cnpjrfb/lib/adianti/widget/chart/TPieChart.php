<?php
namespace Adianti\Widget\Chart;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;

use Exception;

/**
 * Pie chart widget
 *
 * @version    8.6
 * @package    widget
 * @subpackage chart
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TPieChart extends TChartBase
{
    private $xlabels;
    private $ylabel;
    private $slices;
    private $is_donut;
    
    /**
     * add a dataset in the chart
     * @param $name Dataset name
     * @param $data Dataset values
     */
    public function addSlice($name, $value)
    {
        $this->slices[$name] = $value;
    }
    
    /**
     * Enable donut
     */
    public function enableDonut()
    {
        $this->is_donut = true;
    }
    
    /**
     * Show widget
     */
    public function show()
    {
        if (empty($this->slices))
        {
            $this->slices = [];
        }
        
        $colors = parent::getColors();
        
        $template = file_get_contents('lib/adianti/include/components/tpiechart/tpiechart.html');
        $template = str_replace('{chart_id}', uniqid(), $template);
        $template = str_replace('{title}', (string) $this->title, $template);
        $template = str_replace('{data}', json_encode(array_values($this->slices)), $template);
        $template = str_replace('{xlabels}', json_encode(array_keys($this->slices)), $template);
        $template = str_replace('{height}', $this->height, $template);
        $template = str_replace('{type}', $this->is_donut ? 'doughnut' : 'pie', $template);
        $template = str_replace('{colors}', json_encode($colors), $template);
        $template = str_replace('{numberPrefix}', $this->numberPrefix, $template);
        $template = str_replace('{decimals}', $this->numericMask[0], $template);
        $template = str_replace('{decimalsSeparator}', $this->numericMask[1], $template);
        $template = str_replace('{thousandSeparator}', $this->numericMask[2], $template);
        
        parent::add($template);
        
        parent::show();
    }
}