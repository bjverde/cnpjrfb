<?php
namespace Adianti\Http;

use Adianti\Core\AdiantiCoreApplication;
use Adianti\Core\AdiantiCoreTranslator;
use Exception;

/**
 * Basic HTTP Client request
 *
 * @version    7.6
 * @package    http
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
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
    public static function request($url, $method = 'POST', $params = [], $authorization = null, $headers = [])
    {
        if (!in_array('curl', get_loaded_extensions()))
        {
            throw new Exception(AdiantiCoreTranslator::translate('Extension not found: ^1', 'curl'));
        }
        
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
       
        $defaults = [];
        $defaults[CURLOPT_URL] = $url;
        $defaults[CURLOPT_HEADER] = false;
        $defaults[CURLOPT_CUSTOMREQUEST] = $method;
        $defaults[CURLOPT_RETURNTRANSFER] = true;
        $defaults[CURLOPT_SSL_VERIFYHOST] = false;
        $defaults[CURLOPT_SSL_VERIFYPEER] = false;
        $defaults[CURLOPT_CONNECTTIMEOUT] = 10;
        
        if (!empty($authorization))
        {
            $headers[] = 'Authorization: '. $authorization;
        }
        
        if (!empty($headers))
        {
            $defaults[CURLOPT_HTTPHEADER] = $headers;
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
        
        if (!empty($return['status']) && $return['status'] == 'error')
        {
            throw new Exception(!empty($return['data']) ? $return['data'] : $return['message']);
        }
        
        if (!empty($return['error']))
        {
            if (is_scalar($return['error']))
            {
                throw new Exception($return['error']);
            }
            else if (!empty($return['error']['message']))
            {
                throw new Exception($return['error']['message']);
            }
        }
        
        if (!empty($return['errors']))
        {
            if (is_scalar($return['errors']))
            {
                throw new Exception($return['errors']);
            }
            else if (!empty($return['errors']['message']))
            {
                throw new Exception($return['errors']['message']);
            }
        }
        
        if (!empty($return['data']))
        {
            return $return['data'];
        }
        
        return $return;
    }
}
