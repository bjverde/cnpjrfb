<?php
/**
 * SystemRequestLog
 *
 * @version    7.6
 * @package    model
 * @subpackage log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license-template
 */
class SystemRequestLog extends TRecord
{
    const TABLENAME = 'system_request_log';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('endpoint');
        parent::addAttribute('logdate');
        parent::addAttribute('log_year');
        parent::addAttribute('log_month');
        parent::addAttribute('log_day');
        parent::addAttribute('session_id');
        parent::addAttribute('login');
        parent::addAttribute('access_ip');
        parent::addAttribute('class_name');
        parent::addAttribute('class_method');
        parent::addAttribute('http_host');
        parent::addAttribute('server_port');
        parent::addAttribute('request_uri');
        parent::addAttribute('request_method');
        parent::addAttribute('query_string');
        parent::addAttribute('request_headers');
        parent::addAttribute('request_body');
        parent::addAttribute('request_duration');
    }
    
    /**
     *
     */
    public function get_class_method_formatted()
    {
        if (!empty($this->class_method))
        {
            return '::' . $this->class_method . '()';
        }
    }


}
