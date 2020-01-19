<?php
/**
 * Mail Service
 *
 * @version    7.0
 * @package    util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class MailService
{
    /**
     * Send email
     * @param $tos array of target emails
     * @param $subject message subject
     * @param $body message body
     * @param $bodytype body type (text, html)
     */
    public static function send($tos, $subject, $body, $bodytype = 'text')
    {
        TTransaction::open('permission');
        $preferences = SystemPreference::getAllPreferences();
        TTransaction::close();
        
        $mail = new TMail;
        $mail->setFrom( trim($preferences['mail_from']), APPLICATION_NAME );
        
        if (is_string($tos))
        {
            $tos = str_replace(',', ';', $tos);
            $tos = explode(';', $tos);
        }
        
        if (is_array($tos))
        {
            foreach ($tos as $to)
            {
                $mail->addAddress( $to );
            }
        }
        else
        {
            $mail->addAddress( $tos );
        }
        $mail->setSubject( $subject );
        
        if ($preferences['smtp_auth'])
        {
            $mail->SetUseSmtp();
            $mail->SetSmtpHost($preferences['smtp_host'], $preferences['smtp_port']);
            $mail->SetSmtpUser($preferences['smtp_user'], $preferences['smtp_pass']);
        }
        
        if ($bodytype == 'text')
        {
            $mail->setTextBody($body);
        }
        else
        {
            $mail->setHtmlBody($body);
        }
        
        $mail->send();
    }
}
