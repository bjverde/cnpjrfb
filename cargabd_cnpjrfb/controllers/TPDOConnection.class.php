<?php
/*
 * CargaBd_CnpjRFB
 * Criado por Reinaldo A. Barrêto Jr
 * https://github.com/bjverde/cnpjrfb
 *
 * ----------------------------------------------------------------------------
 */

class TPDOConnection {
 
    // construtor
    public function __construct(){
    }
    
    public static function validarDrive()
    {
        $drive = ConfigHelper::getDBDrive();
        if (!in_array($drive,PDO::getAvailableDrivers(),TRUE))
        {
            throw new PDOException ("ERRO: O drive informado não está intaldo no PHP");
        }
    }
    
}
?>
