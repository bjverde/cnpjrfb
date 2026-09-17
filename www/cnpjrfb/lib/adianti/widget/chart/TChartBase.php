<?php
namespace Adianti\Widget\Chart;

use Adianti\Widget\Base\TElement;

/**
 * Chart base
 *
 * @version    8.6
 * @package    widget
 * @subpackage chart
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
abstract class TChartBase extends TElement
{
    protected $title;
    protected $height;
    protected $metadata;
    protected $palette;
    protected $numericMask;
    protected $numberPrefix;
    
    const COLORS = ['#91afec', '#fcba6d', '#78cd8b', '#f4a1c3', '#c3a643', '#c49ac6', '#e8dc66', '#f37978', '#4791cd', 
                    '#f0abab', '#bfd6ed', '#ffa640', '#ffce9e', '#53b948', '#aadca4', '#ee4b4c', '#ffb6b0', '#a982d3', 
                    '#d3c5de', '#a67a6d', '#d0b5aa', '#ec91d1', '#f9c4dd', '#999999', '#d9d9d9', '#d3cf45', '#e4e4a9', 
                    '#46c9e0', '#afe4f1', '#fba898', '#93e3a3', '#dcd9d6', '#7edabf', '#ffd44f', '#d9ddd9', '#b4dd79', 
                    '#f3bfc9', '#dbd7da', '#c199ff', '#c3d3e5', '#d6f18a', '#f2ecc1', '#dac4aa', '#ffe3cb', '#dac7c4', 
                    '#f0dff0', '#f2f240', '#f0ebdd', '#f6b884', '#bebebe'];
    
    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct('div');
        $this->height = 400;
        $this->numberPrefix = '';
        $this->numericMask = [2, ',', '.'];
        $this->metadata = [];
    }
    
    /**
     * Define the chart title
     * @param $title Chart title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    /**
     * Returns the chart title
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Define the chart height
     * @param $height Chart height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }
    
    /**
     * Set metadata
     */
    public function setMetadata($metadata, $value)
    {
        $this->metadata[$metadata] = $value;
    }
    
    /**
     * Get metadata
     */
    public function getMetadata($metadata)
    {
        return $this->metadata[$metadata] ?? null;
    }
    
    /**
     * Define the color palette name
     */
    public function setPalette($palette)
    {
        if (file_exists('app/config/palettes.php'))
        {
            $palettes = require 'app/config/palettes.php';
            if (!empty($palettes[$palette]))
            {
                $this->palette = $palette;
            }
        }
    }
    
    /**
     * Returns the array of colors from palette
     */
    public function getColors()
    {
        if (!empty($this->palette))
        {
            if (file_exists('app/config/palettes.php'))
            {
                $palettes = require 'app/config/palettes.php';
                if (!empty($palettes[$this->palette]))
                {
                    return $palettes[$this->palette];
                }
            }
        }
        
        return self::COLORS;
    }
    
    /**
     * Set default numeric mask
     */
    public function setNumericMask($decimals, $decimalsSeparator, $thousandSeparator)
    {
        $this->numericMask = [$decimals, $decimalsSeparator, $thousandSeparator];
    }
    
    /**
     * Set default number prefix
     */
    public function setNumberPrefix($prefix)
    {
        $this->numberPrefix = $prefix;
    }
}
