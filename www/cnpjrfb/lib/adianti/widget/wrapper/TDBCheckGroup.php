<?php
namespace Adianti\Widget\Wrapper;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Form\TCheckGroup;
use Adianti\Database\TTransaction;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;

use Exception;

/**
 * Database CheckBox Widget
 *
 * @version    7.1
 * @package    widget
 * @subpackage wrapper
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDBCheckGroup extends TCheckGroup
{
    protected $items; // array containing the combobox options
    
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
        
        $key   = trim($key);
        $value = trim($value);
        
        if (empty($database))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'database', __CLASS__));
        }
        
        if (empty($model))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'model', __CLASS__));
        }
        
        if (empty($key))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'key', __CLASS__));
        }
        
        if (empty($value))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'value', __CLASS__));
        }
        
        TTransaction::open($database);
        
        // creates repository
        $repository = new TRepository($model);
        if (is_null($criteria))
        {
            $criteria = new TCriteria;
        }
        $criteria->setProperty('order', isset($ordercolumn) ? $ordercolumn : $key);
        
        // load all objects
        $collection = $repository->load($criteria, FALSE);
        
        // add objects to the options
        if ($collection)
        {
            $items = array();
            foreach ($collection as $object)
            {
                if (isset($object->$value))
                {
                    $items[$object->$key] = $object->$value;
                }
                else
                {
                    $items[$object->$key] = $object->render($value);
                }
            }
            
            if (strpos($value, '{') !== FALSE AND is_null($ordercolumn))
            {
                asort($items);
            }
            parent::addItems($items);
        }
        TTransaction::close();
    }
}
