<?php
namespace Adianti\Widget\Container;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TTableRow;

/**
 * Creates a table layout, with rows and columns
 *
 * @version    7.1
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TTable extends TElement
{
    private $section;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct('table');
        $this->section = null;
    }

    /**
     * Create a table
     */
    public static function create($properties)
    {
        $table = new TTable;
        foreach ($properties as $property => $value)
        {
            $table->$property = $value;
        }
        return $table;
    }
    
    /**
     * Add section
     */
    public function addSection($type)
    {
        if ($type == 'thead')
        {
            $this->section = new TElement('thead');
        }
        else if ($type == 'tbody')
        {
            $this->section = new TElement('tbody');
        }
        else if ($type == 'tfoot')
        {
            $this->section = new TElement('tfoot');
        }
        parent::add($this->section);
        
        return $this->section;
    }
    
    /**
     * Add a new row (TTableRow object) to the table
     * @return TTableRow
     */
    public function addRow()
    {
        // creates a new Table Row
        $row = new TTableRow( $this->section ? $this->section->getName() : 'tbody');
        
        // add this row to the table element
        if (isset($this->section))
        {
            $this->section->add($row);
        }
        else
        {
            parent::add($row);
        }
        return $row;
    }
    
    /**
     * Add a new row (TTableRow object) with many cells
     * @param $cells Each argument is a row cell
     * @return TTableRow
     */
    public function addRowSet()
    {
        // creates a new Table Row
        $row = $this->addRow();
        
        $args = func_get_args();
        if ($args)
        {
            foreach ($args as $arg)
            {
                if (is_array($arg))
                {
                    $inst = $row;
                    call_user_func_array(array($inst, 'addMultiCell'), $arg);
                }
                else
                {
                    $row->addCell($arg, ($this->section && $this->section->getName() == 'thead') ? 'th' : 'td');
                }
            }
        }
        return $row;
    }
    
    /**
     * Create a table from data array
     * @param $array_data Array with raw data
     * @param $table_properties Array of CSS properties for table
     * @param $header_properties Array of CSS properties for header
     * @param $body_properties Array of CSS properties for body
     */
    public static function fromData($array_data, $table_properties = null, $header_properties = null, $body_properties = null)
    {
        $table = new self;
        if ($table_properties)
        {
            foreach ($table_properties as $prop=>$value)
            {
                $table->$prop = $value;
            }
        }
        
        $header = array_keys(isset($array_data[0])?$array_data[0]:array());
        
        $thead = new TElement('thead');
        $table->add($thead);
        
        $tr = new TTableRow;
        $thead->add($tr);
        foreach ($header as $cell)
        {
            $td = $tr->addCell((string) $cell);
            if ($header_properties)
            {
                foreach ($header_properties as $prop=>$value)
                {
                    $td->$prop = $value;
                }
            }
        }
        
        $tbody = new TElement('tbody');
        $table->add($tbody);
        
        $i = 0;
        foreach ($array_data as $row)
        {
            $tr = new TTableRow;
            $tbody->add($tr);
            $tr->{'class'} = ($i %2==0) ? 'odd': 'even';
            
            foreach ($header as $key)
            {
                $cell = isset($row[$key]) ? $row[$key] : '';
                $td = $tr->addCell((string) $cell);
                if ($body_properties)
                {
                    foreach ($body_properties as $prop=>$value)
                    {
                        $td->$prop = $value;
                    }
                }
            }
            
            $i ++;
        }
        
        return $table;
    }
}
