<?php
namespace Adianti\Widget\Wrapper;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Form\TSortList;
use Adianti\Database\TTransaction;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;

use Exception;

/**
 * Database Sortlist Widget
 *
 * @version    7.1
 * @package    widget
 * @subpackage wrapper
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDBSortList extends TSortList
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
        
        // carrega objetos do banco de dados
        TTransaction::open($database);
        // instancia um repositório de Estado
        $repository = new TRepository($model);
        if (is_null($criteria))
        {
            $criteria = new TCriteria;
        }
        $criteria->setProperty('order', isset($ordercolumn) ? $ordercolumn : $key);
        // carrega todos objetos
        $collection = $repository->load($criteria, FALSE);
        
        // adiciona objetos na combo
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
