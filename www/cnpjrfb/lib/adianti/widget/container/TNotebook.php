<?php
namespace Adianti\Widget\Container;

use Adianti\Control\TAction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TTable;
use Adianti\Widget\Container\TFrame;

/**
 * Notebook
 *
 * @version    7.1
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TNotebook extends TElement
{
    private $width;
    private $height;
    private $currentPage;
    private $pages;
    private $counter;
    private $id;
    private $tabAction;
    private $tabsVisibility;
    private $tabsSensibility;
    private $container;
    static private $noteCounter;
    
    /**
     * Class Constructor
     * @param $width   Notebook's width
     * @param $height  Notebook's height
     */
    public function __construct($width = null, $height = null)
    {
        parent::__construct('div');
        $this->id = 'tnotebook_' . mt_rand(1000000000, 1999999999);
        $this->counter = ++ self::$noteCounter;
        
        // define some default values
        $this->pages = [];
        $this->width = $width;
        $this->height = $height;
        $this->currentPage = 0;
        $this->tabsVisibility = TRUE;
        $this->tabsSensibility = TRUE;
    }
    
    /**
     * Define if the tabs will be visible or not
     * @param $visible If the tabs will be visible
     */
    public function setTabsVisibility($visible)
    {
        $this->tabsVisibility = $visible;
    }
    
    /**
     * Define the tabs click sensibility
     * @param $sensibility If the tabs will be sensible to click
     */
    public function setTabsSensibility($sensibility)
    {
        $this->tabsSensibility = $sensibility;
    }
    
    /**
     * Returns the element ID
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set the notebook size
     * @param $width  Notebook's width
     * @param $height Notebook's height
     */
    public function setSize($width, $height)
    {
        // define the width and height
        $this->width  = $width;
        $this->height = $height;
    }
    
    /**
     * Returns the frame size
     * @return array(width, height)
     */
    public function getSize()
    {
        return array($this->width, $this->height);
    }
    
    /**
     * Define the current page to be shown
     * @param $i An integer representing the page number (start at 0)
     */
    public function setCurrentPage($i)
    {
        // atribui a página corrente
        $this->currentPage = $i;
    }
    
    /**
     * Returns the current page
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }
    
    /**
     * Add a tab to the notebook
     * @param $title   tab's title
     * @param $object  tab's content
     */
    public function appendPage($title, $object)
    {
        $this->pages[$title] = $object;
    }

    /**
     * Return the Page count
     */
    public function getPageCount()
    {
        return count($this->pages);
    }
    
    /**
     * Define the action for the Notebook tab
     * @param $action Action taken when the user
     * clicks over Notebook tab (A TAction object)
     */
    public function setTabAction(TAction $action)
    {
        $this->tabAction = $action;
    }
    
    /**
     * Render the notebook
     */
    public function render()
    {
        // count the pages
        $pages = $this->getPageCount();
        
        $this->container = new TElement('div');
        if ($this->width)
        {
            $this->container->{'style'} = ";min-width:{$this->width}px";
        }
        $this->container->{'class'} = 'tnotebook';
        
        $ul = new TElement('ul');
        $ul->{'class'} = 'nav nav-tabs';
        $this->container->add($ul);
        
        $space = new TElement('div');
        if ($this->width)
        {
            $space->{'style'} = "min-width:{$this->width}px";
        }
        $space->{'class'} = 'spacer';
        $this->container->add($space);
        
        $i = 0;
        $id = $this->id;
        
        
        if ($this->pages)
        {
            // iterate the tabs, showing them
            foreach ($this->pages as $title => $content)
            {
                // verify if the current page is to be shown
                $classe = ($i == $this->currentPage) ? 'active' : '';
                
                // add a cell for this tab
                if ($this->tabsVisibility)
                {
                    $item = new TElement('li');
                    $link = new TElement('a');
                    $link->{'aria-controls'} = "home";
                    $link->{'role'} = "tab";
                    $link->{'data-toggle'} = "tab";
                    $link->{'href'} = "#"."panel_{$id}_{$i}";
                    $link->{'class'} = $classe . " nav-link";
                    
                    if (!$this->tabsSensibility)
                    {
                        $link->{'style'} = "pointer-events:none";
                    }
                    
                    $item->add($link);
                    $link->add("$title");
                    $item->{'class'} = $classe . " nav-item";
                    $item->{'role'} = "presentation";
                    $item->{'id'} = "tab_{$id}_{$i}";
                    
                    if ($this->tabAction)
                    {
                        $this->tabAction->setParameter('current_page', $i+1);
                        $string_action = $this->tabAction->serialize(FALSE);
                        $link-> onclick = "__adianti_ajax_exec('$string_action')";
                    }
                    
                    $ul->add($item);
                    $i ++;
                }
            }
        }
        
        // creates a <div> around the content
        $quadro = new TElement('div');
        $quadro->{'class'} = 'frame tab-content';
        
        $width = $this->width;
        $height= $this->height;// -30;
        
        if ($width)
        {
            $quadro->{'style'} .= ";min-width:{$width}px";
        }
        
        if($height)
        {
            $quadro->{'style'} .= ";min-height:{$height}px";
        }
        
        $i = 0;
        // iterate the tabs again, now to show the content
        if ($this->pages)
        {
            foreach ($this->pages as $title => $content)
            {
                $panelClass = ($i == $this->currentPage) ? 'active': '';
                
                // creates a <div> for the contents
                $panel = new TElement('div');
                $panel->{'role'}  = "tabpanel";
                $panel->{'class'} = "tab-pane " . $panelClass;
                $panel->{'id'}    = "panel_{$id}_{$i}"; // ID
                $quadro->add($panel);
                
                // check if the content is an object
                if (is_object($content))
                {
                    $panel->add($content);
                }
                
                $i ++;
            }
        }
        
        $this->container->add($quadro);
        return $this->container;
    }
    
    /**
     * Show the notebook
     */
    public function show()
    {
        if (empty($this->container))
        {
            $this->container = $this->render();
        }
        parent::add($this->container);
        parent::show();
    }
}
