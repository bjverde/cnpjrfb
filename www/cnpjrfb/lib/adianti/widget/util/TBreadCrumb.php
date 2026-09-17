<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Base\TElement;

/**
 * BreadCrumb
 *
 * @version    8.6
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @author     Nataniel Rabaioli
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TBreadCrumb extends TElement
{
    protected $container;
    protected $items;
    
    /**
     * Handle paths from a XML file
     * @param $xml_file path for the file
     */
    public function __construct()
    {
        parent::__construct('div');
        $this->{'id'} = 'div_breadcrumbs';
        
        $this->container = new TElement('ol');
        $this->container->{'class'} = 'tbreadcrumb';
        parent::add( $this->container );
    }
    
    /**
     * Static constructor
     */
    public static function create( $options)
    {
        $breadcrumb = new TBreadCrumb;
        foreach ($options as $option)
        {
            $breadcrumb->addItem( $option );
        }
        return $breadcrumb;
    }
    
    /**
     * Add an item
     * @param $path Path to be shown
     * @param $last If the item is the last one
     */
    public function addItem($path, $last = FALSE)
    {
        $li = new TElement('li');
        $this->container->add( $li );
        
        $span = new TElement('span');
        $span->add( $path );
        
        $this->items[$path] = $span;
        if( $last )
        {
            $li->add( $span );
        }
        else
        {
            $a = new TElement('a');
            
            $li->add( $a );
            $a->add( $span );
        }
            
    }
    
    /**
     * Mark one breadcrumb item as selected
     */
    public function select($path)
    {
        foreach ($this->items as $key => $span)
        {
            if ($key == $path)
            {
                $span->{'class'} = 'selected';
            }
            else
            {
                $span->{'class'} = '';
            }
        }
    }
}
