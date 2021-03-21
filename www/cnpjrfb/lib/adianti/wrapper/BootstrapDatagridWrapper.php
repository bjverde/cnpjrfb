<?php
namespace Adianti\Wrapper;
use Adianti\Widget\Datagrid\TDataGrid;

/**
 * Bootstrap datagrid decorator for Adianti Framework
 *
 * @version    7.3
 * @package    wrapper
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 * @wrapper    TDataGrid
 * @wrapper    TQuickGrid
 */
class BootstrapDatagridWrapper
{
    private $decorated;
    
    /**
     * Constructor method
     */
    public function __construct(TDataGrid $datagrid)
    {
        $this->decorated = $datagrid;
        $this->decorated->{'class'} = 'table table-striped table-hover';
        $this->decorated->{'type'}  = 'bootstrap';
    }
    
    /**
     * Clone datagrid
     */
    public function __clone()
    {
        $this->decorated = clone $this->decorated;
    }
    
    /**
     * Redirect calls to decorated object
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->decorated, $method),$parameters);
    }
    
    /**
     * Redirect calls to decorated object
     */
    public function __set($property, $value)
    {
        $this->decorated->$property = $value;
    }
    
    /**
     * Redirect calls to decorated object
     */
    public function __get($property)
    {
        return $this->decorated->$property;
    }
    
    /**
     * Shows the decorated datagrid
     */
    public function show()
    {
        $this->decorated->{'style'} .= ';border-collapse:collapse';
        
        $sessions = $this->decorated->getChildren();
        if ($sessions)
        {
            foreach ($sessions as $section)
            {
                unset($section->{'class'});
                
                $rows = $section->getChildren();
                if ($rows)
                {
                    foreach ($rows as $row)
                    {
                        if ($row->{'class'} == 'tdatagrid_group')
                        {
                            $row->{'class'} = 'info';
                            $row->{'style'} = $row->{'style'} . ';user-select:none';
                        }
                        else
                        {
                            unset($row->{'class'});
                            
                            if (!empty($row->{'className'}))
                            {
                                $row->{'class'} = $row->{'className'};
                            }
                        }
                    }
                }
            }
        }
        $this->decorated->show();
    }
}
