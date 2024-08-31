<?php
/**
 * Table
 *
 * @version    7.6
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license-template
 */
class TAdiantiTable extends TAdiantiElement
{
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct('table');
    }

    /**
     * Add a new row (TTableRow object) to the table
     */
    public function addRow()
    {
        // creates a new Table Row
        $row = new TAdiantiTableRow;
        // add this row to the table element
        parent::add($row);
        return $row;
    }
}
?>
