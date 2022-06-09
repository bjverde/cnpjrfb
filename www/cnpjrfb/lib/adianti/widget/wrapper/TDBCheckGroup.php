<?php
namespace Adianti\Widget\Wrapper;

use Adianti\Widget\Form\TCheckGroup;
use Adianti\Database\TCriteria;

use Exception;

/**
 * Database CheckBox Widget
 *
 * @version    7.4
 * @package    widget
 * @subpackage wrapper
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDBCheckGroup extends TCheckGroup
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
     * Reload checkbox from model data
     * @param  $formname    form name
     * @param  $field       field name
     * @param  $database    database name
     * @param  $model       model class name
     * @param  $key         table field to be used as key in the checkbox
     * @param  $value       table field to be listed in the checkbox
     * @param  $ordercolumn column to order the fields (optional)
     * @param  $criteria    criteria (TCriteria object) to filter the model (optional)
     * @param  $options     array of options [layout, breakItems, useButton, value, valueSeparator, changeAction, changeFunction]
     */
    public static function reloadFromModel($formname, $field, $database, $model, $key, $value, $ordercolumn = NULL, $criteria = NULL, $options = [])
    {
        // load items
        $items = self::getItemsFromModel($database, $model, $key, $value, $ordercolumn, $criteria);

        // reload checkbox
        parent::reload($formname, $field, $items, $options);
    }
}
