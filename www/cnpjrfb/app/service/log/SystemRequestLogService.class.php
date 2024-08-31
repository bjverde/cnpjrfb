<?php
/**
 * SystemRequestLogService
 *
 * @version    7.6
 * @package    service
 * @subpackage log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license-template
 */
class SystemRequestLogService
{
    /**
     * Register login
     */
    public static function register( $endpoint = null, $duration = null)
    {
        $input    = json_decode(file_get_contents("php://input"));
        $request  = array_merge($_REQUEST, (array) $input);
        $cur_conn = serialize(TTransaction::getDatabaseInfo());
        $new_conn = serialize(TConnection::getDatabaseInfo('log'));
        
        $open_transaction = ($cur_conn !== $new_conn);
        
        if ($open_transaction)
        {
            TTransaction::open('log');
        }
        
        $object = new SystemRequestLog;
        $object->endpoint = $endpoint;
        $object->logdate = date("Y-m-d H:i:s");
        $object->log_year = date("Y");
        $object->log_month = date("m");
        $object->log_day = date("d");
        $object->session_id = session_id();
        $object->login = TSession::getValue('login');
        $object->access_ip = (PHP_SAPI == 'cli') ? '127.0.0.1' : ( isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null );
        $object->class_name = $request['class'];
        $object->class_method = $request['method'] ?? '';
        $object->http_host = (PHP_SAPI == 'cli') ? 'cli' : $_SERVER['HTTP_HOST'];
        $object->server_port = (PHP_SAPI == 'cli') ? '' : $_SERVER['SERVER_PORT'];
        $object->request_uri = (PHP_SAPI == 'cli') ? 'cmd.php' : $_SERVER['REQUEST_URI'];
        $object->request_method = (PHP_SAPI == 'cli') ? 'CLI' : $_SERVER['REQUEST_METHOD'];
        $object->request_duration = (int) ( $duration * 1000 ); // miliseconds
        $object->query_string = (PHP_SAPI == 'cli') ? http_build_query($request) : $_SERVER['QUERY_STRING'];
        
        if (PHP_SAPI !== 'cli')
        {
            $object->request_headers = json_encode(getallheaders());
        }
        
        $object->request_body = json_encode($request);
        $object->store();
        
        if ($open_transaction)
        {
            TTransaction::close();
        }
        
        return $object->id;
    }
}
