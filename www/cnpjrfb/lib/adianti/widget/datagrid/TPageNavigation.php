<?php
namespace Adianti\Widget\Datagrid;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Base\TElement;
use Adianti\Control\TAction;
use Adianti\Widget\Container\TTable;

use Exception;

/**
 * Page Navigation provides navigation for a datagrid
 *
 * @version    7.3
 * @package    widget
 * @subpackage datagrid
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TPageNavigation
{
    private $limit;
    private $count;
    private $order;
    private $page;
    private $first_page;
    private $action;
    private $width;
    private $direction;
    private $hidden;
    private $resume;
    
    /**
     * Constructor method
     */
    public function __construct()
    {
        $this->hidden = false;
        $this->resume = false;
    }
    
    /**
     * Hide
     */
    public function hide()
    {
        $this->hidden = true;
    }
    
    /**
     * Enable counters
     */
    public function enableCounters()
    {
        $this->resume = true;
    }
    
    /**
     * Get resume string
     */
    private function getResume()
    {
        if( !$this->getCount() )
        {
            return AdiantiCoreTranslator::translate('No records found');
        }
        else
        {
            $max = number_format( (min(( $this->getLimit() * $this->getPage() ) , $this->getCount())) , 0, '', '.');
            $min = number_format( (($this->getLimit() * ($this->getPage() - 1) ) + 1) , 0, '', '.');
            
            return AdiantiCoreTranslator::translate('^1 to ^2 from ^3 records', $min, $max, number_format($this->getCount(), 0 , '', '.'));
        }
    }
    
    /**
     * Set the Amount of displayed records
     * @param $limit An integer
     */
    public function setLimit($limit)
    {
        $this->limit  = (int) $limit;
    }
    
    /**
     * Returns the limit of records
     */
    public function getLimit()
    {
        return $this->limit;
    }
    
    /**
     * Define the PageNavigation's width
     * @param $width PageNavigation's width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }
    
    /**
     * Define the total count of records
     * @param $count An integer (the total count of records)
     */
    public function setCount($count)
    {
        $this->count = (int) $count;
    }
    
    /**
     * Return the total count of records
     */
    public function getCount()
    {
        return $this->count;
    }
    
    /**
     * Define the current page
     * @param $page An integer (the current page)
     */
    public function setPage($page)
    {
        $this->page = (int) $page;
    }
    
    /**
     * Returns the current page
     */
    public function getPage()
    {
        return $this->page;
    }
    
    /**
     * Define the first page
     * @param $page An integer (the first page)
     */
    public function setFirstPage($first_page)
    {
        $this->first_page = (int) $first_page;
    }
    
    /**
     * Define the ordering
     * @param $order A string containint the column name
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }
    
    /**
     * Define the ordering
     * @param $direction asc, desc
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }
    
    /**
     * Set the page navigation properties
     * @param $properties array of properties
     */
    public function setProperties($properties)
    {
        $order      = isset($properties['order'])  ? addslashes($properties['order'])  : '';
        $page       = isset($properties['page'])   ? $properties['page']   : 1;
        $direction  = (isset($properties['direction']) AND in_array($properties['direction'], array('asc', 'desc')))  ? $properties['direction']   : NULL;
        $first_page = isset($properties['first_page']) ? $properties['first_page']: 1;
        
        $this->setOrder($order);
        $this->setPage($page);
        $this->setDirection($direction);
        $this->setFirstPage($first_page);
    }
    
    /**
     * Define the PageNavigation action
     * @param $action TAction object (fired when the user navigates)
     */
    public function setAction($action)
    {
        $this->action = $action;
    }
    
    /**
     * Show the PageNavigation widget
     */
    public function show()
    {
        if ($this->hidden)
        {
            return;
        }
        
        if (!$this->action instanceof TAction)
        {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before add this component', __CLASS__ . '::' . 'setAction()'));
        }
        
        if ($this->resume)
        {
            $total = new TElement('div');
            $total->{'class'} = 'tpagenavigation_resume';
            $total->add($this->getResume());
            $total->show();
        }
        
        $first_page = isset($this->first_page) ? $this->first_page : 1;
        $direction  = 'asc';
        $page_size  = isset($this->limit) ? $this->limit : 10;
        $max = 10;
        $registros = $this->count;
        
        if (!$registros)
        {
            $registros = 0;
        }
        
        if ($page_size > 0)
        {
            $pages = (int) ($registros / $page_size) - $first_page +1;
        }
        else
        {
            $pages = 1;
        }
        
        $resto = 0;
        if ($page_size>0)
        {
            $resto = $registros % $page_size;
        }
        
        $pages += $resto > 0 ? 1 : 0;
        $last_page = min($pages, $max);
        
        $nav = new TElement('nav');
        $nav->{'class'} = 'tpagenavigation';
        $nav->{'align'} = 'center';
        
        $ul = new TElement('ul');
        $ul->{'class'} = 'pagination';
        $ul->{'style'} = 'display:inline-flex;';
        $nav->add($ul);
        
        if ($first_page > 1)
        {
            // first
            $item = new TElement('li');
            $link = new TElement('a');
            $span = new TElement('span');
            $link->{'aria-label'} = 'Previous';
            $ul->add($item);
            $item->add($link);
            $link->add($span);
            $this->action->setParameter('offset', 0);
            $this->action->setParameter('limit',  $page_size);
            $this->action->setParameter('direction', $this->direction);
            $this->action->setParameter('page',   1);
            $this->action->setParameter('first_page', 1);
            $this->action->setParameter('order', $this->order);

            $link->{'class'}     = "page-link";
            $link->{'href'}      = $this->action->serialize();
            $link->{'generator'} = 'adianti';
            $span->add(TElement::tag('span', '', ['class'=>'fa fa-angle-double-left']));
            
            // previous
            $item = new TElement('li');
            $link = new TElement('a');
            $span = new TElement('span');
            $link->{'aria-label'} = 'Previous';
            $ul->add($item);
            $item->add($link);
            $link->add($span);
            $this->action->setParameter('offset', ($first_page - $max -1) * $page_size);
            $this->action->setParameter('limit',  $page_size);
            $this->action->setParameter('direction', $this->direction);
            $this->action->setParameter('page',   $first_page - $max);
            $this->action->setParameter('first_page', $first_page - $max);
            $this->action->setParameter('order', $this->order);

            $link->{'class'}     = "page-link";
            $link->{'href'}      = $this->action->serialize();
            $link->{'generator'} = 'adianti';
            $span->add(TElement::tag('span', '', ['class'=>'fa fa-angle-left'])); //$span->add('&laquo;');
        }
        
        // active pages
        for ($n = $first_page; $n <= $last_page + $first_page -1; $n++)
        {
            $offset = ($n -1) * $page_size;
            $item = new TElement('li');
            $link = new TElement('a');
            $span = new TElement('span');
            $ul->add($item);
            $item->add($link);
            $link->add($span);
            $span->add($n);
            
            $this->action->setParameter('offset', $offset);
            $this->action->setParameter('limit',  $page_size);
            $this->action->setParameter('direction', $this->direction);
            $this->action->setParameter('page',   $n);
            $this->action->setParameter('first_page', $first_page);
            $this->action->setParameter('order', $this->order);
            
            $link->{'href'}      = $this->action->serialize();
            $link->{'generator'} = 'adianti';
            $link->{'class'}     = 'page-link';

            if($this->page == $n)
            {
                $item->{'class'} = 'active page-item';
            }
        }
        
        // inactive pages/placeholders
        for ($z=$n; $z<=10; $z++)
        {
            $item = new TElement('li');
            $link = new TElement('a');
            $span = new TElement('span');
            $item->{'class'} = 'off page-item';
            $link->{'class'} = 'page-link';
            $ul->add($item);
            $item->add($link);
            $link->add($span);
            $span->add($z);
        }
        
        if ($pages > $max)
        {
            // next
            $first_page = $n;
            $item = new TElement('li');
            $link = new TElement('a');
            $span = new TElement('span');
            $link->{'aria-label'} = "Next";
            $ul->add($item);
            $item->add($link);
            $link->add($span);
            $this->action->setParameter('offset',  ($n -1) * $page_size);
            $this->action->setParameter('limit',   $page_size);
            $this->action->setParameter('direction', $this->direction);
            $this->action->setParameter('page',    $n);
            $this->action->setParameter('first_page', $n);
            $this->action->setParameter('order', $this->order);
            $link->{'class'}     = "page-link";
            $link->{'href'}      = $this->action->serialize();
            $link->{'generator'} = 'adianti';
            $span->add(TElement::tag('span', '', ['class'=>'fa fa-angle-right'])); //$span->add('&raquo;');
            
            // last
            $item = new TElement('li');
            $link = new TElement('a');
            $span = new TElement('span');
            $link->{'aria-label'} = "Next";
            $ul->add($item);
            $item->add($link);
            $link->add($span);
            $this->action->setParameter('offset',  ceil($registros / $page_size)* $page_size - $page_size);
            $this->action->setParameter('limit',   $page_size);
            $this->action->setParameter('direction', $this->direction);
            $this->action->setParameter('page',    ceil($registros / $page_size));
            $this->action->setParameter('first_page', (int) ($registros / ($page_size *10)) *10 +1);
            $this->action->setParameter('order', $this->order);
            $link->{'class'}     = "page-link";
            $link->{'href'}      = $this->action->serialize();
            $link->{'generator'} = 'adianti';
            $span->add(TElement::tag('span', '', ['class'=>'fa fa-angle-double-right']));
        }
        
        $nav->show();
    }
}
