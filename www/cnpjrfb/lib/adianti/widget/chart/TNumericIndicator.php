<?php
namespace Adianti\Widget\Chart;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;

use Exception;

/**
 * Numeric indicator widget
 *
 * @version    8.6
 * @package    widget
 * @subpackage chart
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TNumericIndicator extends TChartBase
{
    private $value;
    private $icon;
    private $color;
    
    /**
     * Set indicator value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    /**
     * Set indicator icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }
    
    /**
     * Set indicator color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }
    
    /**
     * Show indicator
     */
    public function show()
    {
        $value = (float) $this->value;
        $value = number_format($value, $this->numericMask[0], $this->numericMask[1], $this->numericMask[2]);
        
        if (!empty($this->numberPrefix))
        {
            $value = $this->numberPrefix . ' ' . $value;
        }
        
        $template = file_get_contents('lib/adianti/include/components/tnumericindicator/tnumericindicator.html');
        $template = str_replace('{chart_id}', uniqid(), $template);
        $template = str_replace('{title}', (string) $this->title, $template);
        $template = str_replace('{value}', $value, $template);
        $template = str_replace('{icon}', $this->icon, $template);
        $template = str_replace('{color}', $this->color, $template);
        
        parent::add($template);
        
        parent::show();
    }
}