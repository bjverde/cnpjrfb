<?php
/*
 * CargaBd_CnpjRFB
 * Criado por Reinaldo A. Barrêto Jr
 * https://github.com/bjverde/cnpjrfb
 *
 * ----------------------------------------------------------------------------
 */
class ConfigHelper
{
    const EXTRACTED_FILES_PATH = "EXTRACTED_FILES_PATH";

    public static function loadAll()
    {        
        $filep = ROOT_FOLDER.DS.'config.php';
        if (file_exists($filep)){
            $ini = require $filep;
            return $ini;
        }else{
            throw new InvalidArgumentException('ERRO: Arquivo Config não encontrado');
        }

    }

    public static function getExtractedFilesPath()
    {
        $ini = self::loadAll();
        $path = ArrayHelper::get($ini,self::EXTRACTED_FILES_PATH);
        if( empty($path) ){
            throw new InvalidArgumentException('ERRO: Caminho do arquivos descompactados não informado');
        }
        return $path;
    }
}