<?php
/**
 * SystemChangeLogService
 *
 * @version    7.6
 * @package    service
 * @subpackage log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license-template
 */
class SystemChangeLogService
{
    /**
     * Register a change log
     */
    public static function register($activeRecord, $lastState, $currentState, $operation = null)
    {
        $table = $activeRecord->getEntity();
        $pk    = $activeRecord->getPrimaryKey();
        
        $created_col = $activeRecord->getCreatedAtColumn();
        $updated_col = $activeRecord->getUpdatedAtColumn();
        $deleted_col = $activeRecord->getDeletedAtColumn();
        
        $e        = new Exception;
        $uniqid   = TTransaction::getUniqId();
        $cur_conn = serialize(TTransaction::getDatabaseInfo());
        $new_conn = serialize(TConnection::getDatabaseInfo('log'));
        
        $open_transaction = ($cur_conn !== $new_conn);
        
        if ($open_transaction)
        {
            TTransaction::open('log');
        }
        
        foreach ($lastState as $key => $value)
        {
            if (!in_array($key, array_keys($currentState)) && !in_array($key, [$created_col, $updated_col, $deleted_col]) && ( (string) $value !== '') && ($operation == 'delete'))
            {
                // deleted
                $log = new SystemChangeLog;
                $log->tablename  = $table;
                $log->logdate    = date('Y-m-d H:i:s');
                $log->log_year   = date('Y');
                $log->log_month  = date('m');
                $log->log_day    = date('d');
                $log->login      = TSession::getValue('login');
                $log->primarykey = $pk;
                $log->pkvalue    = $activeRecord->$pk;
                $log->operation  = 'deleted';
                $log->columnname = $key;
                $log->oldvalue   = (string) $value;
                $log->newvalue   = '';
                $log->access_ip  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
                $log->transaction_id  = $uniqid;
                $log->log_trace  = $e->getTraceAsString();
                $log->session_id = session_id();
                $log->class_name = isset($_REQUEST['class']) ? $_REQUEST['class'] : '';
                $log->php_sapi   = php_sapi_name();
                $log->store();
            }
        }
        
        foreach ($currentState as $key => $value)
        {
            if (isset($lastState[$key]) && ($value != $lastState[$key]) && !in_array($key, [$created_col, $updated_col, $deleted_col]))
            {
                // changed
                $log = new SystemChangeLog;
                $log->tablename  = $table;
                $log->logdate    = date('Y-m-d H:i:s');
                $log->log_year   = date('Y');
                $log->log_month  = date('m');
                $log->log_day    = date('d');
                $log->login      = TSession::getValue('login');
                $log->primarykey = $pk;
                $log->pkvalue    = $activeRecord->$pk;
                $log->operation  = 'changed';
                $log->columnname = $key;
                $log->oldvalue   = (string) $lastState[$key];
                $log->newvalue   = (string) is_scalar($value) ? $value : ( (is_null($value) ? NULL : serialize($value)) );
                $log->access_ip  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
                $log->transaction_id  = $uniqid;
                $log->log_trace  = $e->getTraceAsString();
                $log->session_id = session_id();
                $log->class_name = isset($_REQUEST['class']) ? $_REQUEST['class'] : '';
                $log->php_sapi   = php_sapi_name();
                $log->store();
            }
            
            if (!isset($lastState[$key]) && !empty($value) && !in_array($key, [$created_col, $updated_col, $deleted_col]))
            {
                // created
                $log = new SystemChangeLog;
                $log->tablename  = $table;
                $log->logdate    = date('Y-m-d H:i:s');
                $log->log_year   = date('Y');
                $log->log_month  = date('m');
                $log->log_day    = date('d');
                $log->login      = TSession::getValue('login');
                $log->primarykey = $pk;
                $log->pkvalue    = $activeRecord->$pk;
                $log->operation  = 'created';
                $log->columnname = $key;
                $log->oldvalue   = '';
                $log->newvalue   = (string) is_scalar($value) ? $value : serialize($value);
                $log->access_ip  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
                $log->transaction_id  = $uniqid;
                $log->log_trace  = $e->getTraceAsString();
                $log->session_id = session_id();
                $log->class_name = isset($_REQUEST['class']) ? $_REQUEST['class'] : '';
                $log->php_sapi   = php_sapi_name();
                $log->store();
            }
        }
        
        if ($open_transaction)
        {
            TTransaction::close();
        }
    }
}
