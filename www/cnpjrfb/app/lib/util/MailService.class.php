<?php
/**
 * Mail Service
 *
 * @version    7.0
 * @package    util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class MailService
{
    /**
     * Send email
     * @param $tos array of target emails
     * @param $subject message subject
     * @param $body message body
     * @param $bodytype body type (text, html)
     * @param $attachs attachments array with files paths
     */
    public static function send($tos, $subject, $body, $bodytype = 'text', $attachs = [])
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
            $mail->setUseSmtp( (!empty($preferences['smtp_user']) && !empty($preferences['smtp_pass']) ) );
        }
        
        $mail->SetSmtpUser($preferences['smtp_user'], $preferences['smtp_pass']);
        $mail->SetSmtpHost($preferences['smtp_host'], $preferences['smtp_port']);
        
        if (!empty($attachs))
        {
            foreach ($attachs as $attach)
            {
                $mail->addAttach($attach[0], (isset($attach[1]) ? $attach[1] : null));
            }
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
