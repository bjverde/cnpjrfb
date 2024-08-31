<?php

use Adianti\Log\AdiantiLoggerInterface;

/**
 * SystemSqlLogService
 *
 * @version    7.6
 * @package    service
 * @subpackage log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license-template
 */
class SystemSqlLogService implements AdiantiLoggerInterface
{
    /**
     * Writes an message in the global logger
     * @param  $message Message to be written
     */
    public function write($message)
    {
        $dbname = TTransaction::getDatabase();
        $uniqid = TTransaction::getUniqId();
        
        $e = new Exception;
        $cur_conn = serialize(TTransaction::getDatabaseInfo());
        $new_conn = serialize(TConnection::getDatabaseInfo('log'));
        
        $open_transaction = ($cur_conn !== $new_conn);
        
        // avoid log of log
        if ($dbname !== 'log' AND (in_array(substr($message,0,6), array('INSERT', 'UPDATE', 'DELETE') ) ) )
        {
            $info = TTransaction::getDatabaseInfo();
            $date_mask = (in_array($info['type'], ['sqlsrv', 'dblib', 'mssql'])) ? 'Ymd H:i:s' : 'Y-m-d H:i:s';
            $time = date($date_mask);
            
            if ($open_transaction)
            {
                TTransaction::open('log');
            }
            
            $object = new SystemSqlLog;
            $object->logdate = $time;
            $object->log_year = date("Y");
            $object->log_month = date("m");
            $object->log_day = date("d");
            $object->login = TSession::getValue('login');
            $object->database_name = $dbname;
            $object->sql_command = $message;
            $object->statement_type = strtoupper(substr($message,0,6));
            $object->access_ip  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
            $object->transaction_id  = $uniqid;
            $object->log_trace = $e->getTraceAsString();
            $object->session_id = session_id();
            $object->class_name = isset($_REQUEST['class']) ? $_REQUEST['class'] : '';
            $object->php_sapi   = php_sapi_name();
            $object->request_id = AdiantiCoreApplication::getRequestId();
            $object->store();
            
            if ($open_transaction)
            {
                TTransaction::close();
            }
        }
    }
}
