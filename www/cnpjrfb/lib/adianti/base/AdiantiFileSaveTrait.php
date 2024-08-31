<?php
namespace Adianti\Base;

use Exception;
use PDO;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TTransaction;

/**
 * File Save Trait
 *
 * @version    7.6
 * @package    base
 * @author     Nataniel Rabaioli
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
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
            $object->$input_name = $target_file;
            
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

            $object->$input_name = $obj_store->$input_name;
            $data->$input_name = $files_form;
        }
    }

    /**
     * Save binary file
     *
     * Database and column types supporteds:
     *      ORACLE   => BLOB
     *      MYSQL    => LONGBLOB
     *      MSSQL    => VARBINARY(MAX)
     *      POSTGRES => BYTEA
     *
     * @param $object          Active Record
     * @param $data            Form data
     * @param $attr_file       Input field name
     * @param $attr_file_name  Active field name for name file
     */
    public function saveBinaryFile($object, $data, $attr_file, $attr_file_name)
    {
        $dados_file = json_decode(urldecode($data->$attr_file));

        if (isset($dados_file->fileName))
        {
            $pk = $object->getPrimaryKey();

            $source_file = $dados_file->fileName;
            $target_file = str_replace('tmp/', '', $dados_file->fileName);

            $table_name = $object->getEntity();
            $pk_value   = $object->{$pk};

            $target_file = str_replace("{$table_name}/{$pk_value}/", '', $target_file);

            $class = get_class($object);
            $obj_store = new $class;
            $obj_store->$pk = $object->$pk;
            $object->$attr_file = $target_file;
            $obj_store->$attr_file_name = $target_file;

            $delFile = null;

            if (! empty($dados_file->delFile))
            {
                $dados_file->fileName = '';
                $obj_store->$attr_file = NULL;

                if (is_file(urldecode($dados_file->delFile)))
                {
                    $delFile = urldecode($dados_file->delFile);

                    if (file_exists($delFile))
                    {
                        unlink($delFile);
                    }
                }

                $obj_store->$attr_file_name = NULL;
            }

            if (!empty($dados_file->newFile))
            {
                if (file_exists($source_file))
                {
                    $obj_store->$attr_file = $dados_file->newFile;
                    $obj_store->$attr_file_name = $target_file;
                }
            }
            elseif ($dados_file->fileName != $delFile)
            {
                $obj_store->$attr_file = $dados_file->fileName;
            }

            $conn = TTransaction::get();
            $drive = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);

            $id   = $obj_store->{$pk};
            $file = $obj_store->{$attr_file} ? base64_encode(file_get_contents($obj_store->{$attr_file})) : NULL;

            if (in_array($drive,['sqlsrv','dblib']))
            {
                $stmt = $conn->prepare("UPDATE  {$object->getEntity()} SET {$attr_file} = (CONVERT(varbinary(max), ?)) WHERE {$pk} = ?");

                $stmt->bindParam(1, $file, PDO::PARAM_LOB);
                $stmt->bindParam(2, $id);
                $stmt->execute();
            }
            else if($drive === 'oci')
            {
                $attr = $obj_store->{$attr_file} ? 'empty_blob()' : 'NULL';

                $stmt = $conn->prepare("UPDATE  {$object->getEntity()} SET {$attr_file} = {$attr} WHERE {$pk} = ? returning {$attr_file} into ?");

                $stmt->bindParam(1, $id);
                $stmt->bindParam(2, $blob, PDO::PARAM_LOB);
                $blob = null;
                $stmt->execute();

                if($file)
                {
                    fwrite($blob, $file);
                    fclose($blob);
                }
            }
            else
            {
                $stmt = $conn->prepare("UPDATE  {$object->getEntity()} SET {$attr_file} = ? WHERE {$pk} = ?");

                $stmt->bindParam(1, $file, PDO::PARAM_LOB);
                $stmt->bindParam(2, $id);
                $stmt->execute();
            }

            unset($obj_store->{$attr_file});
            $obj_store->store();

            if ($source_file)
            {
                $dados_file->fileName = $source_file;
                $data->$attr_file = urlencode(json_encode($dados_file));
            }
            else
            {
                $data->$attr_file = '';
            }

            return $obj_store;
        }
    }

    /**
     * Save binary file
     *
     * Database and column types supporteds:
     *      ORACLE   => BLOB
     *      MYSQL    => LONGBLOB
     *      MSSQL    => VARBINARY(MAX)
     *      POSTGRES => BYTEA
     *
     * @param $object          Active Record
     * @param $data            Form data
     * @param $attr_file_data  Input field name
     * @param $model_files     Files Active Record
     * @param $attr_file_name  Active field name for name file
     * @param $file_field      File field in model_files
     * @param $foreign_key     Foreign key to $object
     */
    public function saveBinaryFiles($object, $data, $attr_file_data, $model_files, $attr_file_name, $file_field, $foreign_key)
    {
        $pk = $object->getPrimaryKey();

        $delFiles      = [];
        $files_form    = [];
        $final_objects = [];

        if (isset($data->$attr_file_data) AND $data->$attr_file_data)
        {
            foreach ($data->$attr_file_data as $key => $info_file)
            {
                $dados_file = json_decode(urldecode($info_file));

                if (!empty($dados_file->fileName))
                {
                    $source_file = $dados_file->fileName;
                    $target_file = str_replace('tmp/', '', $dados_file->fileName);

                    $table_name = $object->getEntity();
                    $pk_name    = $object->getPrimaryKey();
                    $pk_value   = $object->{$pk_name};

                    $target_file = str_replace("{$table_name}/{$pk_value}/", '', $target_file);

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
                                $file->delete();

                                $delFile = urldecode($dados_file->delFile);

                                if (file_exists($delFile))
                                {
                                    unlink($delFile);
                                }
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
                            $model_file = new $model_files;
                            $model_file->$attr_file_name = $target_file;
                            $model_file->$foreign_key = $object->$pk;
                            $model_file->store();

                            $conn = TTransaction::get();
                            $drive = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);

                            $pk_model = $model_file->getPrimaryKey();
                            $file = $source_file ? base64_encode(file_get_contents($source_file)) : NULL;
                            $id = $model_file->{$pk_model};

                            if (in_array($drive,['sqlsrv','dblib']))
                            {
                                $stmt = $conn->prepare("UPDATE  {$model_file->getEntity()} SET {$file_field} = (CONVERT(varbinary(max), ?)) WHERE {$pk} = ?");

                                $stmt->bindParam(1, $file, PDO::PARAM_LOB);
                                $stmt->bindParam(2, $id);
                                $stmt->execute();
                            }
                            else if($drive === 'oci')
                            {
                                $attr = $source_file ? 'empty_blob()' : 'NULL';
                                $stmt = $conn->prepare("UPDATE  {$model_file->getEntity()} SET {$file_field} = {$attr} WHERE {$pk} = ? returning {$file_field} into ?");
                                $blob = null;
                                $stmt->bindParam(1, $id);
                                $stmt->bindParam(2, $blob, PDO::PARAM_LOB);
                                $blob = null;
                                $stmt->execute();

                                if($file)
                                {
                                    fwrite($blob, $file);
                                    fclose($blob);
                                }
                            }
                            else
                            {
                                $stmt = $conn->prepare("UPDATE  {$model_file->getEntity()} SET {$file_field} = ? WHERE {$pk} = ?");

                                $stmt->bindParam(1, $file, PDO::PARAM_LOB);
                                $stmt->bindParam(2, $id);
                                $stmt->execute();
                            }

                            $final_objects[] = $model_file;

                            $pk_detail = $model_file->getPrimaryKey();
                            $file_form['idFile'] = $model_file->$pk_detail;
                            $file_form['fileName'] = $target_file;
                        }
                    }

                    if ($file_form and !$file_form['delFile'])
                    {
                        $files_form[] = $file_form;
                    }
                }
            }

            $data->$attr_file_data = $files_form;
        }

        return $final_objects;
    }

    /**
     * Save binary file on tmp and return path
     * @param $object          Active Record
     * @param $attr_file       Input field name
     * @param $attr_file_name  Active field name for name file
     */
    public function loadBinaryFile($object, $attr_file, $attr_file_name)
    {
        $conn = TTransaction::get();

        $table_name = $object->getEntity();
        $pk_name    = $object->getPrimaryKey();
        $pk_value   = $object->{$pk_name};

        $stmt = $conn->prepare("SELECT {$pk_name}, {$attr_file}, {$attr_file_name} FROM {$table_name} WHERE {$pk_name} = ?");

        $stmt->bindParam(1, $pk_value);
        $stmt->bindColumn($attr_file, $lob, PDO::PARAM_LOB);
        $stmt->execute();
        $result = $stmt->fetch();

        if(! $lob)
        {
            return null;
        }

        if(! is_string($lob))
        {
            $lob = stream_get_contents($lob);
        }

        $lob = base64_decode($lob);

        $folder_name = "tmp/{$table_name}/{$result[0]}";

        if(! file_exists($folder_name))
        {
            mkdir($folder_name, 0777, true);
        }

        $file_name = $folder_name . '/' .  $result[2];

        file_put_contents($file_name, $lob);

        $object->{$attr_file} = $file_name;

        return $file_name;
    }

    /**
     * Save binary files on tmp and return paths
     * @param $object          Active Record
     * @param $model_files     Files Active Record
     * @param $attr_file_name  Active field name for name file
     * @param $file_field      File field in model_files
     * @param $foreign_key     Foreign key to $object
     */
    public function loadBinaryFiles($object, $model_files, $attr_file_name, $file_field, $foreign_key)
    {
        $conn = TTransaction::get();

        $model_file = new $model_files;

        $table_name_file = $model_file->getEntity();
        $pk_name_file    = $model_file->getPrimaryKey();

        $pk_name  = $object->getPrimaryKey();
        $pk_value = $object->{$pk_name};

        $stmt = $conn->prepare("SELECT {$pk_name_file}, {$file_field}, {$attr_file_name} FROM {$table_name_file} WHERE {$foreign_key} = ?");

        $stmt->bindParam(1, $pk_value);
        $stmt->bindColumn($file_field, $lobs, PDO::PARAM_LOB);
        $stmt->execute();

        $file_names = [];

        while($result = $stmt->fetch())
        {
            $lob = $result[1];

            if(! is_string($lob))
            {
                $lob = stream_get_contents($lob);
            }

            $lob = base64_decode($lob);

            $folder_name = "tmp/{$table_name_file}/{$result[0]}";

            if(! file_exists($folder_name))
            {
                mkdir($folder_name, 0777, true);
            }

            $file_name = $folder_name . '/' .  $result[2] ;

            file_put_contents($file_name, $lob);

            $file_names[$result[0]] = $file_name;
        }

        $object->{$attr_file_name} = $file_names;

        return $file_names;
    }
}