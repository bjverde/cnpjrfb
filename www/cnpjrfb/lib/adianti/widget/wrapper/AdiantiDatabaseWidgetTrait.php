<?php
namespace Adianti\Widget\Wrapper;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TConnection;
use Adianti\Database\TTransaction;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;

use Exception;

/**
 * Database Widget trait
 *
 * @version    7.6
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
trait AdiantiDatabaseWidgetTrait
{
    /**
     * Get items (key/value) from database to populate widget
     */
    public static function getItemsFromModel($database, $model, $key, $value, $ordercolumn = NULL, TCriteria $criteria = NULL)
    {
        $items = [];
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
        
        $cur_conn = serialize(TTransaction::getDatabaseInfo());
        $new_conn = serialize(TConnection::getDatabaseInfo($database));
        
        $open_transaction = ($cur_conn !== $new_conn);
        
        if ($open_transaction)
        {
            TTransaction::openFake($database);
        }
        
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
        }
        
        if ($open_transaction)
        {
            TTransaction::close();
        }
        
        return $items;
    }
    
    /**
     * Get objects from database to populate widget
     */
    public static function getObjectsFromModel($database, $model, $key, $ordercolumn = NULL, TCriteria $criteria = NULL)
    {
        $items = [];
        $key   = trim($key);
        
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
        
        $cur_conn = serialize(TTransaction::getDatabaseInfo());
        $new_conn = serialize(TConnection::getDatabaseInfo($database));
        
        $open_transaction = ($cur_conn !== $new_conn);
        
        if ($open_transaction)
        {
            TTransaction::openFake($database);
        }
        
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
            foreach ($collection as $object)
            {
                $items[$object->$key] = $object;
            }
        }
        
        if ($open_transaction)
        {
            TTransaction::close();
        }
        
        return $items;
    }
}
