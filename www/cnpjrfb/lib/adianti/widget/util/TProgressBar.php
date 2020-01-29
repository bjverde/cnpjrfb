<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Base\TElement;

/**
 * TProgressBar
 *
 * @version    7.1
 * @package    widget
 * @subpackage util
 * @author     Ademilson Nunes
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TProgressBar extends TElement
{
    private $value;
    private $mask;
    private $className;
    
    public function __construct() 
    {
        parent::__construct('div');
        $this->{'class'} = 'progress';
        $this->{'id'} = 'tprogressbar_'.mt_rand(1000000000, 1999999999);
        $this->{'style'} = 'margin-bottom:0; text-shadow: none;';
        $this->mask = '{value}%';
        $this->className = 'info';
    }
    
    /**
     * set mask for progress bar value Ex: "{value}%"
     */
    public function setMask($mask)
    {
        $span = new TElement("span");
        $span->add($mask);
        $this->mask = $span;
    }
    
    /**
     * set style class
     */
    public function setClass($class)
    {
        $this->className = $class;
    }
    
    /**
     * Set the value of progress bar
     */ 
    public function setValue($value)
    {
       $this->value = $value;
    }
            
    /**
     * Shows the widget at the screen
     */       
    public function show()
    {                   
        $progressBar = new TElement('div');
        $progressBar->{'class'} = "progress-bar progress-bar-{$this->className}";
        $progressBar->{'role'} = 'progressbar';
        $progressBar->{'arial-valuenow'} = $this->value;
        $progressBar->{'arial-valuemin'} = '0';
        $progressBar->{'arial-valuemax'} = '100';
        $progressBar->{'style'} = 'width: ' . $this->value . '%;';
         
        $value = str_replace('{value}', $this->value, $this->mask);
         
        $progressBar->add($value);
        parent::add($progressBar);
       
        parent::show();
    }
}
