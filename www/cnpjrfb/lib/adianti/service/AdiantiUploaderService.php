<?php
namespace Adianti\Service;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Core\AdiantiApplicationConfig;

/**
 * File uploader listener
 *
 * @version    7.6
 * @package    service
 * @author     Nataniel Rabaioli
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class AdiantiUploaderService
{
    function show($param)
    {
        $ini  = AdiantiApplicationConfig::get();
        $seed = APPLICATION_NAME . ( !empty($ini['general']['seed']) ? $ini['general']['seed'] : 's8dkld83kf73kf094' );
        $block_extensions = ['php', 'php3', 'php4', 'phtml', 'pl', 'py', 'jsp', 'asp', 'htm', 'shtml', 'sh', 'cgi', 'htaccess'];
        
        $folder = 'tmp/';
        $response = array();
        if (isset($_FILES['fileName']))
        {
            $file = $_FILES['fileName'];
            
            if( $file['error'] === 0 && $file['size'] > 0 )
            {
                $path = $folder.$file['name'];
                
                // check blocked file extension, not using finfo because file.php.2 problem
                foreach ($block_extensions as $block_extension)
                {
                    if (strpos(strtolower($file['name']), ".{$block_extension}") !== false)
                    {
                        $response = array();
                        $response['type'] = 'error';
                        $response['msg']  = AdiantiCoreTranslator::translate('Extension not allowed');
                        echo json_encode($response);
                        return;
                    }
                }
                
                if (!empty($param['extensions']))
                {
                    $name = $param['name'];
                    $extensions = unserialize(base64_decode( $param['extensions'] ));
                    $hash = md5("{$seed}{$name}".base64_encode(serialize($extensions)));
                    
                    if ($hash !== $param['hash'])
                    {
                        $response = array();
                        $response['type'] = 'error';
                        $response['msg']  = AdiantiCoreTranslator::translate('Hash error');
                        echo json_encode($response);
                        return;
                    }
                    
                    // check allowed file extension
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    
                    if (!in_array(strtolower($ext),  $extensions))
                    {
                        $response = array();
                        $response['type'] = 'error';
                        $response['msg']  = AdiantiCoreTranslator::translate('Extension not allowed');
                        echo json_encode($response);
                        return;
                    }
                }
                
                if (is_writable($folder) )
                {
                    if( move_uploaded_file( $file['tmp_name'], $path ) )
                    {
                        $response['type'] = 'success';
                        $response['fileName'] = $file['name'];
                    }
                    else
                    {
                        $response['type'] = 'error';
                        $response['msg'] = '';
                    }
                }
                else
                {
                    $response['type'] = 'error';
                    $response['msg']  = AdiantiCoreTranslator::translate('Permission denied') . ": {$path}";
                }
                echo json_encode($response);
            }
            else
            {
                $response['type'] = 'error';
                $response['msg']  = AdiantiCoreTranslator::translate('Server has received no file') . '. ' . AdiantiCoreTranslator::translate('Check the server limits') .  '. ' . AdiantiCoreTranslator::translate('The current limit is') . ' ' . self::getMaximumFileUploadSizeFormatted();
                echo json_encode($response);
            }
        }
        else
        {
            $response['type'] = 'error';
            $response['msg']  = AdiantiCoreTranslator::translate('Server has received no file') . '. ' . AdiantiCoreTranslator::translate('Check the server limits') .  '. ' . AdiantiCoreTranslator::translate('The current limit is') . ' ' . self::getMaximumFileUploadSizeFormatted();
            echo json_encode($response);
        }
    }
    
    /**
     *
     */
    public static function getMaximumFileUploadSizeFormatted()  
    {  
        $post_max_size = self::convertSizeToBytes(ini_get('post_max_size'));
        $upld_max_size = self::convertSizeToBytes(ini_get('upload_max_filesize'));  
        
        if ($post_max_size < $upld_max_size)
        {
            return 'post_max_size: ' . ini_get('post_max_size');
        }
        
        return 'upload_max_filesize: ' .ini_get('upload_max_filesize');
    }
    
    /**
     *
     */
    public static function getMaximumFileUploadSize()  
    {  
        return min(self::convertSizeToBytes(ini_get('post_max_size')), self::convertSizeToBytes(ini_get('upload_max_filesize')));  
    }  
    
    /**
     *
     */
    public static function convertSizeToBytes($size)
    {
        $suffix = strtoupper(substr($size, -1));
        if (!in_array($suffix,array('P','T','G','M','K'))){
            return (int)$size;  
        } 
        $value = substr($size, 0, -1);
        switch ($suffix) {
            case 'P':
                $value *= 1024;
                // intended
            case 'T':
                $value *= 1024;
                // intended
            case 'G':
                $value *= 1024;
                // intended
            case 'M':
                $value *= 1024;
                // intended
            case 'K':
                $value *= 1024;
                break;
        }
        return (int)$value;
    }
}
