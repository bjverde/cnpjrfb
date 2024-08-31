<?php

use Adianti\Database\TTransaction;

/**
 * SystemChangeLogTrait
 *
 * @version    7.6
 * @package    model
 * @subpackage log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license-template
 */
trait SystemChangeLogTrait
{
    public function onAfterDelete( $object )
    {
        $deletedat = self::getDeletedAtColumn();
        if ($deletedat)
        {
            $lastState = (array) $object;

            $info = TTransaction::getDatabaseInfo();
            $date_mask = (in_array($info['type'], ['sqlsrv', 'dblib', 'mssql'])) ? 'Ymd H:i:s' : 'Y-m-d H:i:s';
            $object->{$deletedat} = date($date_mask);

            SystemChangeLogService::register($this, $lastState, (array) $object);
        }
        else
        {
            SystemChangeLogService::register($this, $object, [], 'delete');
        }
    }
    
    public function onBeforeStore($object)
    {
        $pk = $this->getPrimaryKey();
        $this->lastState = array();
        
        if (!empty($object->$pk))
        {
            $object = parent::load($object->$pk, TRUE);
            
            if ($object instanceof TRecord)
            {
                $this->lastState = $object->toArray();
            }
        }
    }
    
    public function onAfterStore($object)
    {
        SystemChangeLogService::register($this, $this->lastState, (array) $object);
    }
}
