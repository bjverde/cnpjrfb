<?php
/**
 * SystemAccessNotificationLog
 *
 * @version    7.6
 * @package    model
 * @subpackage log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license-template
 */
class SystemAccessNotificationLog extends TRecord
{
    const TABLENAME = 'system_access_notification_log';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('login');
        parent::addAttribute('login_time');
        parent::addAttribute('ip_address');
        parent::addAttribute('email');
    }
}
