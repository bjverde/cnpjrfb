<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * TMail
 *
 * @version    7.0
 * @package    util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMail
{
    private $pm; // phpMailer instance
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        $this->pm = new PHPMailer(true);
        
        $this->pm->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
        $this->pm-> CharSet = 'utf-8';
    }
    
    /**
     * Return internal instance
     */
    public function getInternalInstance()
    {
        return $this->pm;
    }
    
    /**
     * Turn ON/OFF the debug
     */
    public function setDebug($bool)
    {
        $this->pm-> SMTPDebug    = $bool;
    }
    
    /**
     * Set from email address
     * @param  $from = from email
     * @param  $name = from name
     */
    public function setFrom($from, $name = null)
    {
        $this->pm-> From     = $from;
        
        if (!empty($name))
        {
            $this->pm-> FromName = $name;
        }
    }
    
    /**
     * Set reply-to email address
     * @param  $email = reply-to email
     * @param  $name  = reply-to name
     */
    public function setReplyTo($address, $name = '')
    {
        $this->pm-> AddReplyTo($address, $name = '');
    }
    
    /**
     * Set the message subject
     * @param  $subject of the message
     */
    public function setSubject($subject)
    {
        $this->pm-> Subject = $subject;
    }
    
    /**
     * Set the email text body
     * @param  $body = text body
     */
    public function setTextBody($body)
    {
        $this->pm-> Body = $body;
        $this->pm-> IsHTML(false);
    }
    
    /**
     * Set the email html body
     * @param  $body = html body
     */
    public function setHtmlBody($html)
    {
        $this->pm-> MsgHTML($html);
    }
    
    /**
     * Add an TO address
     * @param  $address = TO email address
     * @param  $name    = TO email name
     */
    public function addAddress($address, $name = '')
    {
        if (!$name)
        {
            // search by pattern: nome <email@mail.com>
            list($address, $name) = $this->parseMail($address);
        }
        
        $this->pm-> AddAddress($address, $name);
    }
    
    /**
     * Add an CC address
     * @param  $address = CC email address
     * @param  $name    = CC email name
     */
    public function addCC($address, $name = '')
    {
        $this->pm-> AddCC($address, $name);
    }
    
    /**
     * Add an BCC address
     * @param  $address = BCC email address
     * @param  $name    = BCC email name
     */
    public function addBCC($address, $name = '')
    {
        $this->pm-> AddBCC($address, $name);
    }
    
    /**
     * Add an attachment
     * @param  $path = path to file
     * @param  $name = name of file
     */
    public function addAttach($path, $name = '')
    {
        $this->pm-> AddAttachment($path, $name);
    }
    
    /**
     * Set to use Smtp
     */
    public function SetUseSmtp($auth = true)
    {
        $this->pm-> IsSMTP();            // set mailer to use SMTP
        $this->pm-> SMTPAuth = $auth;    // turn on SMTP authentication
    }
    
    /**
     * Set Smtp Host
     * @param  $host = smtp host
     */
    public function SetSmtpHost($host, $port = 25)
    {
        $this->pm-> Host = $host;
        $this->pm-> Port = $port;
        
        if (strstr($this->pm-> Host, 'gmail') !== FALSE)
        {
            $this->pm-> SMTPSecure = "ssl";
        }
    }
    
    /**
     * Set Smtp User
     * @param  $user = smtp user
     * @param  $pass = smtp pass
     */
    public function SetSmtpUser($user, $pass)
    {
        $this->pm-> Username = $user;
        $this->pm-> Password = $pass;
    }
    
    /**
     * Returns name and email separated
     */
    public function parseMail($fullmail)
    {
        $pos = strpos($fullmail, '<');
        if ( $pos !== FALSE )
        {
            $name  = trim(substr($fullmail, 0, $pos-1));
            $email = trim(substr($fullmail, $pos+1, -1));
            $name  = str_replace("'", "''", $name);
            
            return array($email, $name);
        }
        
        return array($fullmail, '');
    }
    
    /**
     * Send the email
     */
    public function send()
    {
        $this->pm-> Send();
        return TRUE;
    }
}
