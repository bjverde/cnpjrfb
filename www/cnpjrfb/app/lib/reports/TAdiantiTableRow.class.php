<?php
/**
 * Table Row
 *
 * @version    7.6
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license-template
 */
class TAdiantiTableRow extends TAdiantiElement
{
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct('tr');
    }
    
    /**
     * Add a new cell (TTableCell) to the Table Row
     * @param  $value Cell Content
     * @return The created Table Cell
     */
    public function addCell($value)
    {
        // creates a new Table Cell
        $cell = new TAdiantiTableCell($value);
        parent::add($cell);
        // returns the cell object
        return $cell;
    }
}
?>
