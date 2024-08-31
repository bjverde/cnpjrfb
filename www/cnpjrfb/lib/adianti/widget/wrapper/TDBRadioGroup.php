<?php
namespace Adianti\Widget\Wrapper;

use Adianti\Widget\Form\TRadioGroup;
use Adianti\Database\TCriteria;

use Exception;

/**
 * Database Radio Widget
 *
 * @version    7.6
 * @package    widget
 * @subpackage wrapper
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TDBRadioGroup extends TRadioGroup
{
    protected $items; // array containing the combobox options
    
    use AdiantiDatabaseWidgetTrait;
    
    /**
     * Class Constructor
     * @param  $name     widget's name
     * @param  $database database name
     * @param  $model    model class name
     * @param  $key      table field to be used as key in the combo
     * @param  $value    table field to be listed in the combo
     * @param  $ordercolumn column to order the fields (optional)
     * @param  $criteria criteria (TCriteria object) to filter the model (optional)
     */
    public function __construct($name, $database, $model, $key, $value, $ordercolumn = NULL, TCriteria $criteria = NULL)
    {
        // executes the parent class constructor
        parent::__construct($name);
        
        // load items
        parent::addItems( self::getItemsFromModel($database, $model, $key, $value, $ordercolumn, $criteria) );
    }

    /**
     * Reload radio from model data
     * @param  $formname    form name
     * @param  $field       field name
     * @param  $database    database name
     * @param  $model       model class name
     * @param  $key         table field to be used as key in the radio
     * @param  $value       table field to be listed in the radio
     * @param  $ordercolumn column to order the fields (optional)
     * @param  $criteria    criteria (TCriteria object) to filter the model (optional)
     * @param  $options     array of options [layout, breakItems, useButton, value, changeAction, changeFunction]
     */
    public static function reloadFromModel($formname, $field, $database, $model, $key, $value, $ordercolumn = NULL, $criteria = NULL, $options = [])
    {
        // load items
        $items = self::getItemsFromModel($database, $model, $key, $value, $ordercolumn, $criteria);

        // reload radio
        parent::reload($formname, $field, $items, $options);
    }
}
