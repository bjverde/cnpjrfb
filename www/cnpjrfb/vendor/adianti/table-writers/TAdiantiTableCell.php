<?php
/**
 * Table Cell
 *
 * @version    8.0
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license-template
 */
class TAdiantiTableCell extends TAdiantiElement
{
    /**
     * Class Constructor
     * @param $value  TableCell content
     */
    public function __construct($value)
    {
        parent::__construct('td');
        parent::add($value);
    }
}
?>
