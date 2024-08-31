<?php
namespace Adianti\Widget\Wrapper;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Form\TCheckList;
use Adianti\Database\TTransaction;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;

use Exception;

/**
 * Database Checklist
 *
 * @version    7.6
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TDBCheckList extends TCheckList
{
    protected $items; // array containing the combobox options
    protected $keyColumn;
    protected $valueColumn;
    
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
        
        // define the ID column por set/get values from component
        parent::setIdColumn($key);
        
        // value column
        $this->valueColumn = parent::addColumn($value,  '',    'left',  '100%');
        
        // get objects
        $collection = ( $this->getObjectsFromModel($database, $model, $key, $ordercolumn, $criteria) );
        
        if (strpos($value, '{') !== FALSE)
        {
            // iterate objects to render the value when needed
            TTransaction::open($database);
            if ($collection)
            {
                foreach ($collection as $key => $object)
                {
                    if (!isset($object->$value))
                    {
                        $collection[$key]->$value = $object->render($value);
                    }
                }
            }
            TTransaction::close();
        }
        
        parent::addItems($collection);
    }
    
    /**
     * show
     */
    public function show()
    {
        $head = parent::getHead();
        if ($head)
        {
            $head->{'style'} = 'display:none';
        }

        parent::show();
    }
}
