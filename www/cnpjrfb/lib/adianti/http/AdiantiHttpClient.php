<?php
namespace Adianti\Http;

use Adianti\Core\AdiantiCoreApplication;
use Adianti\Core\AdiantiCoreTranslator;
use Exception;

/**
 * Basic HTTP Client request
 *
 * @version    7.4
 * @package    core
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class AdiantiHttpClient
{
    /**
     * Execute a HTTP request
     *
     * @param $url URL
     * @param $method method type (GET,PUT,DELETE,POST)
     * @param $params request body
     */
    public static function request($url, $method = 'POST', $params = [], $authorization = null)
    {
        $ch = curl_init();
        
        if ($method == 'POST' || $method == 'PUT')
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_POST, true);
     
        }
        else if ( ($method == 'GET' || $method == 'DELETE') && $params)
        {
            $url .= '?'.http_build_query($params);
        }
       
        $defaults = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => 10
        );
        
        if (!empty($authorization))
        {
            $defaults[CURLOPT_HTTPHEADER] = ['Authorization: '. $authorization];
        }
        
        curl_setopt_array($ch, $defaults);
        $output = curl_exec ($ch);
        
        if ($output === false)
        {
            throw new Exception( curl_error($ch) );
        }
        
        curl_close ($ch);
        
        $return = (array) json_decode($output);
        
        if (json_last_error() !== JSON_ERROR_NONE)
        {
            throw new Exception(AdiantiCoreTranslator::translate('Return is not a valid JSON. Check the URL') . ' ' . ( AdiantiCoreApplication::getDebugMode() ? $output : '') );
        }
        
        if (!empty($return['status']) && $return['status'] == 'error') {
            throw new Exception(!empty($return['data']) ? $return['data'] : $return['message']);
        }
        
        if (!empty($return['error'])) {
            throw new Exception($return['error']['message']);
        }
        
        if (!empty($return['errors'])) {
            throw new Exception($return['errors']['message']);
        }
        
        if (!empty($return['data']))
        {
            return $return['data'];
        }
        
        return $return;
    }
}
