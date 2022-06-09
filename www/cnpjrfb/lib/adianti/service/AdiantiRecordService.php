<?php
namespace Adianti\Service;

use Adianti\Database\TTransaction;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;

/**
 * Record rest service
 *
 * @version    7.4
 * @package    service
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class AdiantiRecordService
{
    /**
     * Find a Active Record and returns it
     * @return The Active Record itself as array
     * @param $param HTTP parameter
     */
    public function load($param)
    {
        $database     = static::DATABASE;
        $activeRecord = static::ACTIVE_RECORD;
        
        TTransaction::open($database);
        
        $object = new $activeRecord($param['id'], FALSE);
        
        TTransaction::close();
        $attributes = defined('static::ATTRIBUTES') ? static::ATTRIBUTES : null;
        return $object->toArray( $attributes );
    }
    
    /**
     * Delete an Active Record object from the database
     * @param [$id]     HTTP parameter
     */
    public function delete($param)
    {
        $database     = static::DATABASE;
        $activeRecord = static::ACTIVE_RECORD;
        
        TTransaction::open($database);
        
        $object = new $activeRecord($param['id']);
        $object->delete();
        
        TTransaction::close();
        return;
    }
    
    /**
     * Store the objects into the database
     * @param $param HTTP parameter
     */
    public function store($param)
    {
        $database     = static::DATABASE;
        $activeRecord = static::ACTIVE_RECORD;
        
        TTransaction::open($database);
        
        $object = new $activeRecord;
        $pk = $object->getPrimaryKey();
        $param['data'][$pk] = $param['data']['id'] ?? NULL;
        $object->fromArray( (array) $param['data']);
        $object->store();
        
        TTransaction::close();
        return $object->toArray();
    }
    
    /**
     * List the Active Records by the filter
     * @return The Active Record list as array
     * @param $param HTTP parameter
     */
    public function loadAll($param)
    {
        $database     = static::DATABASE;
        $activeRecord = static::ACTIVE_RECORD;
        
        TTransaction::open($database);
        
        $criteria = new TCriteria;
        if (isset($param['offset']))
        {
            $criteria->setProperty('offset', $param['offset']);
        }
        if (isset($param['limit']))
        {
            $criteria->setProperty('limit', $param['limit']);
        }
        if (isset($param['order']))
        {
            $criteria->setProperty('order', $param['order']);
        }
        if (isset($param['direction']))
        {
            $criteria->setProperty('direction', $param['direction']);
        }
        if (isset($param['filters']))
        {
            foreach ($param['filters'] as $filter)
            {
                $criteria->add(new TFilter($filter[0], $filter[1], $filter[2]));
            }
        }
        
        $repository = new TRepository($activeRecord);
        $objects = $repository->load($criteria, FALSE);
        $attributes = defined('static::ATTRIBUTES') ? static::ATTRIBUTES : null;
        
        $return = [];
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $return[] = $object->toArray( $attributes );
            }
        }
        TTransaction::close();
        return $return;
    }
    
    /**
     * Delete the Active Records by the filter
     * @return The result of operation
     * @param $param HTTP parameter
     */
    public function deleteAll($param)
    {
        $database     = static::DATABASE;
        $activeRecord = static::ACTIVE_RECORD;
        
        TTransaction::open($database);
        
        $criteria = new TCriteria;
        if (isset($param['filters']))
        {
            foreach ($param['filters'] as $filter)
            {
                $criteria->add(new TFilter($filter[0], $filter[1], $filter[2]));
            }
        }
        
        $repository = new TRepository($activeRecord);
        $return = $repository->delete($criteria);
        TTransaction::close();
        return $return;
    }

    /**
     * Find the count Records by the filter
     * @return The Active Record list as array
     * @param $param HTTP parameter
     */
    public function countAll($param)
    {
        $database     = static::DATABASE;
        $activeRecord = static::ACTIVE_RECORD;

        TTransaction::open($database);

        $criteria = new TCriteria;
        if (isset($param['offset']))
        {
            $criteria->setProperty('offset', $param['offset']);
        }
        if (isset($param['limit']))
        {
            $criteria->setProperty('limit', $param['limit']);
        }
        if (isset($param['order']))
        {
            $criteria->setProperty('order', $param['order']);
        }
        if (isset($param['direction']))
        {
            $criteria->setProperty('direction', $param['direction']);
        }
        if (isset($param['filters']))
        {
            foreach ($param['filters'] as $filter)
            {
                $criteria->add(new TFilter($filter[0], $filter[1], $filter[2]));
            }
        }

        $repository = new TRepository($activeRecord);
        $count = $repository->count($criteria, FALSE);

        TTransaction::close();
        return $count;
    }
    
    /**
     * Handle HTTP Request and dispatch
     * @param $param HTTP POST and php input vars
     */
    public function handle($param)
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        
        unset($param['class']);
        unset($param['method']);
        $param['data'] = $param;
        
        switch( $method )
        {
            case 'GET':
                if (!empty($param['id']))
                {
                    return self::load($param);
                }
                else
                {
                    return self::loadAll($param);
                }
                break;
            case 'POST':
                return self::store($param);
                break;
            case 'PUT':
                return self::store($param);
                break;        
            case 'DELETE':
                if (!empty($param['id']))
                {
                    return self::delete($param);
                }
                else
                {
                    return self::deleteAll($param);
                }
                break;
        }
    }
}
