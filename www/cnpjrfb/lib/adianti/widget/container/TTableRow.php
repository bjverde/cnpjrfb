<?php
namespace Adianti\Widget\Container;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\THBox;
use Adianti\Widget\Container\TTableCell;
use Exception;

/**
 * TableRow: Represents a row inside a table
 *
 * @version    7.3
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TTableRow extends TElement
{
    private $section;
    
    /**
     * Class Constructor
     */
    public function __construct($section = 'tbody')
    {
        parent::__construct('tr');
        $this->section = $section;
    }
    
    /**
     * Add a new cell (TTableCell) to the Table Row
     * @param  $value Cell Content
     * @return TTableCell
     */
    public function addCell($value)
    {
        if (is_null($value))
        {
            throw new Exception(AdiantiCoreTranslator::translate('Method ^1 does not accept null values', __METHOD__));
        }
        else
        {
            // creates a new Table Cell
            $cell = new TTableCell($value, $this->section == 'thead' ? 'th' : 'td');
            
            parent::add($cell);
            // returns the cell object
            return $cell;
        }
    }
    
    /**
     * Add a multi-cell content to a table cell
     * @param $cells Each argument is a row cell
     */
    public function addMultiCell()
    {
        $wrapper = new THBox;
        
        $args = func_get_args();
        if ($args)
        {
            foreach ($args as $arg)
            {
                $wrapper->add($arg);
            }
        }
        
        return $this->addCell($wrapper);
    }
    
    /**
     * Clear any child elements
     */
    public function clearChildren()
    {
        $this->children = array();
    }
}
