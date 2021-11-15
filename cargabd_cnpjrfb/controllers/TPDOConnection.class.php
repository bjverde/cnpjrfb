<?php
/*
 * CargaBd_CnpjRFB
 * Criado por Reinaldo A. Barrêto Jr
 * https://github.com/bjverde/cnpjrfb
 *
 * ----------------------------------------------------------------------------
 */

class TPDOConnection {

    private static $error = null;
    private static $instance = null;
    private static $dsn;
    private static $databaseName;
    private static $host;
    private static $port;    
 
    // construtor
    public function __construct(){
        self::setDataBaseName(ConfigHelper::getDdName());
    }
    
    public static function validarDrive()
    {
        $drive = ConfigHelper::getDBDrive();
        if (!in_array($drive,PDO::getAvailableDrivers(),TRUE)){
            throw new PDOException ("ERRO: O drive informado não está intaldo no PHP");
        }
    }
    //--------------------------------------------------------------------------------------
    public static function getPort() {
        return self::$port;
    } 
    public static function setPort($port) {
        self::$port = $port;
    }     
    //--------------------------------------------------------------------------------------
    public static function getDataBaseName()
    {
        return  self::$databaseName;
    }
    public static function setDataBaseName( $strNewValue = null )
    {
        self::$databaseName = $strNewValue;
    }    

    public static function connect() {        
        try {
            self::$instance[ self::getDatabaseName()] = new PDO( self::$dsn, self::$username, self::$password );
            self::$instance[ self::getDatabaseName()]->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            self::getExtraConfigPDO();
        } catch( PDOException $e ){
            $msg = 'Erro de conexão.<br><b>DNS:</b><br>'
                  .self::$dsn
                  .'<br><BR><b>Erro retornado:</b><br>'
                  .$e->getMessage();
            self::$error = utf8_encode( $msg );
            return false;
        }        
        return true;
    }

    public static function getDefaultPortDBMS(){        
        $port = null;
        switch( strtoupper( $DBMS ) ) {
            case DBMS_ACCESS:
            case DBMS_FIREBIRD:
            case DBMS_SQLITE:
                $port=null;
                break;
            case DBMS_MYSQL:
                $port='3306';
                break;
            case DBMS_POSTGRES:
                $port='5432';
                break;
            case DBMS_ORACLE:
                $port='1521';
                break;
            case DBMS_SQLSERVER:
                $port='1433';
                break;
            default:
                $msg = 'Name of DBMS (Data Base Management System) is wrong or not defined.';
                $msg = $msg.' Please see the list of DBMS in BASE/classes/constants.php';
                throw new InvalidArgumentException($msg);
                break;
        }
        return $port;
    }
    
}
?>
