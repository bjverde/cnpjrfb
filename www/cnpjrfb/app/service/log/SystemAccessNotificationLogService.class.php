<?php

/**
 * SystemAccessNotificationLogService
 *
 * @version    7.6
 * @package    service
 * @subpackage log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license-template
 */
class SystemAccessNotificationLogService
{
    /**
     * Register login notification
     */
    public static function registerLogin()
    {
        $ini = AdiantiApplicationConfig::get();

        if (empty($ini['general']['notification_login']))
        {
            return;
        }

        $cur_conn = serialize(TTransaction::getDatabaseInfo());
        $new_conn = serialize(TConnection::getDatabaseInfo('log'));
        
        $open_transaction = ($cur_conn !== $new_conn);
        
        if ($open_transaction)
        {
            TTransaction::open('log');
        }
        
        $object = new SystemAccessNotificationLog;
        $object->email = TSession::getValue('usermail');
        $object->login = TSession::getValue('login');
        $object->login_time = date("Y-m-d H:i:s");
        $object->ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        $object->store();
        
        if ($open_transaction)
        {
            TTransaction::close();
        }
    }

    /**
     * Send email login notification
     * php cmd.php "class=SystemAccessNotificationLogService&method=sendNotificationLogin&static=1"
     */
    public static function sendNotificationLogin()
    {
        try
        {
            $ini = AdiantiApplicationConfig::get();

            if (empty($ini['general']['notification_login']))
            {
                return;
            }

            TTransaction::open('log');
            $objects = SystemAccessNotificationLog::getObjects();
            TTransaction::close();

            if (empty($objects))
            {
                return;
            }

            foreach($objects as $notification)
            {
                try
                {
                    TTransaction::open('log');

                    (new TEmailValidator())->validate("E-mail {$notification->email} FROM {$notification->login}", $notification->email);

                    $html = new THtmlRenderer('app/resources/system_access_notification.html');
                    $html->enableTranslation();

                    $title = $ini['general']['title']??'System';
                    
                    $subject = _t('Login to your account');
                    $content = _t('You have just successfully logged in to ^1. If you do not recognize this login, contact technical support', $title);
                    
                    $html->enableSection(
                        'main',
                        [
                            'login' => $notification->login,
                            'login_time' => $notification->login_time,
                            'ip_address' => $notification->ip_address,
                            'subject' => $subject,
                            'content' => $content,
                        ]
                    );

                    MailService::send($notification->email, $subject, $html->getContents(), 'html');

                    $notification->delete();
                    TTransaction::close();
                }
                catch (Exception $e)
                {
                    TTransaction::rollback();
                    echo $e->getMessage();
                }
            }
        }
        catch(Exception $e)
        {
            TTransaction::rollback();
        }
    }
}
