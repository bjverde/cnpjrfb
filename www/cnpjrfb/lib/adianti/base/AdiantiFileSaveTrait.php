<?php
namespace Adianti\Base;

use Exception;
use Adianti\Core\AdiantiCoreTranslator;

/**
 * File Save Trait
 *
 * @version    7.3
 * @package    base
 * @author     Nataniel Rabaioli
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
trait AdiantiFileSaveTrait
{
    /**
     * Save file
     * @param $object      Active Record
     * @param $data        Form data
     * @param $input_name  Input field name
     * @param $target_path Target file path
     */
    public function saveFile($object, $data, $input_name, $target_path)
    {
        $dados_file = json_decode(urldecode($data->$input_name));
        
        if (isset($dados_file->fileName))
        {
            $pk = $object->getPrimaryKey();
            
            $target_path.= '/' . $object->$pk;
            $target_path = str_replace('//', '/', $target_path);
            
            $source_file = $dados_file->fileName;
            $target_file = strpos($dados_file->fileName, $target_path) === FALSE ? $target_path . '/' . $dados_file->fileName : $dados_file->fileName;
            $target_file = str_replace('tmp/', '', $target_file);
            
            $class = get_class($object);
            $obj_store = new $class;
            $obj_store->$pk = $object->$pk;
            $obj_store->$input_name = $target_file;
            
            $delFile = null;
            
            if (!empty($dados_file->delFile))
            {
                $obj_store->$input_name = '';
                $dados_file->fileName = '';
                
                if (is_file(urldecode($dados_file->delFile)))
                {
                    $delFile = urldecode($dados_file->delFile);
                    
                    if (file_exists($delFile))
                    {
                        unlink($delFile);
                    }
                }
            }
    
            if (!empty($dados_file->newFile))
            {
                if (file_exists($source_file))
                {
                    if (!file_exists($target_path))
                    {
                        if (!mkdir($target_path, 0777, true))
                        {
                            throw new Exception(AdiantiCoreTranslator::translate('Permission denied') . ': '. $target_path);
                        }
                    }
                    
                    // if the user uploaded a source file
                    if (file_exists($target_path))
                    {
                        // move to the target directory
                        if (! rename($source_file, $target_file))
                        {
                            throw new Exception(AdiantiCoreTranslator::translate('Error while copying file to ^1', $target_file));
                        }
                        
                        $obj_store->$input_name = $target_file;
                    }
                }
            }
            elseif ($dados_file->fileName != $delFile)
            {
                $obj_store->$input_name = $dados_file->fileName;
            }
            
            $obj_store->store();
            
            if ($obj_store->$input_name)
            {
                $dados_file->fileName = $obj_store->$input_name;
                $data->$input_name = urlencode(json_encode($dados_file));
            }
            else
            {
                $data->$input_name = '';
            }
            
            return $obj_store;
        }
    }
    
    /**
     * Save files
     * @param $object      Active Record
     * @param $data        Form data
     * @param $input_name  Input field name
     * @param $target_path Target file path
     * @param $model_files Files Active Record
     * @param $file_field  File field in model_files
     * @param $foreign_key Foreign key to $object
     */
    public function saveFiles($object, $data, $input_name, $target_path, $model_files, $file_field, $foreign_key)
    {
        $pk = $object->getPrimaryKey();
        
        $delFiles      = [];
        $files_form    = [];
        $target_path  .= '/' . $object->$pk;
        $target_path   = str_replace('//', '/', $target_path);
        $final_objects = [];
        
        if (isset($data->$input_name) AND $data->$input_name)
        {
            foreach ($data->$input_name as $key => $info_file)
            {            
                $dados_file = json_decode(urldecode($info_file));
                
                if (!empty($dados_file->fileName))
                {
                    $source_file = $dados_file->fileName;
                    $target_file = $target_path . '/' . $dados_file->fileName;
                    $target_file = str_replace('tmp/', '', $target_file);
                    
                    $file_form = [];
                    $file_form['delFile']  = false;
                    $file_form['idFile']   = (isset($dados_file->idFile) AND $dados_file->idFile) ? $dados_file->idFile : null;
                    $file_form['fileName'] = $dados_file->fileName;
                    
                    if (!empty($dados_file->delFile))
                    {
                        $file_form['delFile'] = true;
                        
                        if (!empty($dados_file->idFile))
                        {
                            $file = $model_files::find($dados_file->idFile);
                            
                            if ($file)
                            {
                                if ($file->$file_field AND is_file($file->$file_field))
                                {
                                    unlink( $file->$file_field );
                                }
                                $file->delete();
                            }
                        }
                    }
                    else if (!empty($dados_file->idFile))
                    {
                        $final_objects[] = $model_files::find($dados_file->idFile);
                    }
                    
                    if (!empty($dados_file->newFile))
                    {
                        if (file_exists($source_file))
                        {
                            if (!file_exists($target_path))
                            {
                                if (!mkdir($target_path, 0777, true))
                                {    
                                    throw new Exception(AdiantiCoreTranslator::translate('Permission denied') . ': '. $target_path);
                                }
                            }
                        
                            // if the user uploaded a source file
                            if (file_exists($target_path))
                            {
                                // move to the target directory
                                if (! rename($source_file, $target_file))
                                {
                                    throw new Exception(AdiantiCoreTranslator::translate('Error while copying file to ^1', $target_file));
                                }
                                
                                $model_file = new $model_files;
                                $model_file->$file_field = $target_file;
                                $model_file->$foreign_key = $object->$pk;
                                
                                $model_file->store();
                                $final_objects[] = $model_file;
                                
                                $pk_detail = $model_file->getPrimaryKey();
                                $file_form['idFile'] = $model_file->$pk_detail;
                                $file_form['fileName'] = $target_file;
                            }
                        }
                    }
                    
                    if ($file_form and !$file_form['delFile'])
                    {
                        $files_form[] = $file_form;
                    }
                }
            }
            
            $data->$input_name = $files_form;
        }
        
        return $final_objects;
    }
    
    /**
     * Save files comma separated
     * @param $object      Active Record
     * @param $data        Form data
     * @param $input_name  Input field name
     * @param $target_path Target file path
     */
    public function saveFilesByComma($object, $data, $input_name, $target_path)
    {
        $save_files = [];
        $delFiles   = [];
        $files_form = [];
        
        $pk = $object->getPrimaryKey();
        $target_path.= '/' . $object->$pk;
        
        if (isset($data->$input_name) AND $data->$input_name)
        {
            foreach ($data->$input_name as $key => $info_file)
            {            
                $dados_file = json_decode(urldecode($info_file));
                
                $source_file = $dados_file->fileName;
                $target_file = $target_path . '/' . $dados_file->fileName;
                $target_file = str_replace('tmp/', '', $target_file);
                
                $save_file = $dados_file->fileName;
                
                $file_form = [];
                $file_form['delFile']  = false;
                $file_form['idFile']   = (isset($dados_file->idFile) AND $dados_file->idFile) ? $dados_file->idFile : null;
                $file_form['fileName'] = $dados_file->fileName;
                
                if (!empty($dados_file->delFile))
                {
                    $file_form['delFile'] = true;
                    $save_file = null;
                    
                    if (file_exists( urldecode($dados_file->delFile) ))
                    {
                        unlink( urldecode($dados_file->delFile) );
                    }
                }
                
                if (!empty($dados_file->newFile))
                {
                    if (file_exists($source_file))
                    {
                        if (!file_exists($target_path))
                        {
                            if (!mkdir($target_path, 0777, true))
                            {    
                                throw new Exception(AdiantiCoreTranslator::translate('Permission denied') . ': '. $target_path);
                            }
                        }
                    
                        // if the user uploaded a source file
                        if (file_exists($target_path))
                        {
                            // move to the target directory
                            if (! rename($source_file, $target_file))
                            {
                                throw new Exception(AdiantiCoreTranslator::translate('Error while copying file to ^1', $target_file));
                            }
                            
                            $file_form['idFile'] = $target_file;
                            $file_form['fileName'] = $target_file;
                            
                            $save_file = $target_file;
                        }
                    }
                }
                
                if ($save_file)
                {
                    $save_files[] = $save_file;
                }
                
                if ($file_form and !$file_form['delFile'])
                {
                    $files_form[] = $file_form;
                }                
            }
            
            $class = get_class($object);
            $obj_store = new $class;
            $obj_store->$pk = $object->$pk;
            $obj_store->$input_name = implode(',', $save_files);
            $obj_store->store();
            
            $data->$input_name = $files_form;
        }
    }
}
