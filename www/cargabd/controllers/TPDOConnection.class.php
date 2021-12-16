<?php
/*
 * CargaBd_CnpjRFB
 * Criado por Reinaldo A. Barrêto Jr
 * https://github.com/bjverde/cnpjrfb
 *
 * ----------------------------------------------------------------------------
 */

class TPDOConnection {

    const DBMS_SQLITE   = "sqlite";
    const DBMS_MYSQL    = "mysql";
    const DBMS_POSTGRES = "pgsql";
    const DBMS_SQLSERVER= "sqlsrv";

    private static $error = null;
    private static $instance = null;
    private static $databaseName;
    private static $dbms;
    private static $host;
    private static $port;    
 
    // construtor
    public function __construct(){
        self::setDBMS(ConfigHelper::getDBDrive());
        self::setDataBaseName(ConfigHelper::getDdName());
        self::setPort(ConfigHelper::getDdPort());
    }    
    public static function validarDrive()
    {
        $drive = ConfigHelper::getDBDrive();
        if (!in_array($drive,PDO::getAvailableDrivers(),TRUE)){
            throw new PDOException ("ERRO: O drive informado não está intaldo no PHP");
        }
    }
    public static function validarDBMS()
    {
        $listDbms = array(self::DBMS_MYSQL,self::DBMS_POSTGRES, self::DBMS_SQLITE, self::DBMS_SQLSERVER);
        $drive = ConfigHelper::getDBDrive();
        if (!in_array($drive,$listDbms,TRUE)){
            throw new PDOException ("ERRO: o sistema não funciona com drive: ".$drive);
        }
    }    
    //--------------------------------------------------------------------------------------
    public static function getDBMS() {
        return  self::$dbms;
    }
    public static function setDBMS( $dbms = null ) {
        self::$dbms = $dbms;
    }
    public static function getPort() {
        $port = empty(self::$port)?self::getDefaultPortDBMS():self::$port;
        return $port;
    } 
    public static function setPort($port) {
        self::$port = $port;
    }
    public static function getDataBaseName(){
        return  self::$databaseName;
    }
    public static function setDataBaseName( $strNewValue = null ){
        self::$databaseName = $strNewValue;
    }
    //--------------------------------------------------------------------------------------
    public static function connect() {        
        try {
            $dsn = self::defineDsnPDO();
            $username = ConfigHelper::getDdUser();
            $password = ConfigHelper::getDdPassword();
            $pdo = new PDO( $dsn, $username, $password );
            $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            self::setInstance($pdo);
        } catch( PDOException $e ){
            error_log($e->getMessage());
        }        
        return true;
    }
    /**
     * Define DSN (Data source name) for connection.
     * return implicit, in attributes of class: Host, Database Name and port.
     * return explicit: array with errors of config.
     *
     * @param array $configErrors
     * @param boolean $useConfigfile
     * @return string
     */
    private static function defineDsnPDO() {
        $host = ConfigHelper::getDdHost();
        $database = self::getDataBaseName();
        $port = self::getPort();
        
        switch( self::getDBMS() ) {
            case self::DBMS_MYSQL :
                $dsn = 'mysql:host='.$host.';dbname='.$database.';port='.$port;
            break;
            //-----------------------------------------------------------------------
            case self::DBMS_POSTGRES :
                $dsn = 'pgsql:host='.$host.';dbname='.$database.';port='.$port;
            break;
            //-----------------------------------------------------------------------
            case self::DBMS_SQLITE:
                $dsn = 'sqlite:'.$database;
            break;
            //-----------------------------------------------------------------------
            case self::DBMS_SQLSERVER:
                /**
                 * Dica de Reinaldo A. Barrêto Junior para utilizar o sql server no linux
                 *
                 * No PHP 5.4 ou superior o drive mudou de MSSQL para SQLSRV
                 * */
                if (PHP_OS == "Linux") {
                    if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
                        $driver = 'sqlsrv';
                        $dsn = $driver.':Server='.$host.','.$port.';Database='.$database;
                    } else {
                        $driver = 'dblib';
                        //self::$dsn = $driver.':version=7.2;charset=UTF-8;host=' . HOST . ';dbname=' . DATABASE . ';port=' . PORT;
                        $dsn = $driver.':version=7.2;host='.$host.';dbname='.$database.';port='.$port;
                    }
                } else {
                    $driver = 'sqlsrv';
                    //self::$dsn = $driver.':Server='.$host.','.$port.';Database='.$database;
					$dsn = $driver.':Server='.$host.';Database='.$database;
                }
            break;
        }
        return $dsn;
    }
    public static function getDefaultPortDBMS(){        
        $port = null;
        switch( self::getDBMS() ) {
            case self::DBMS_SQLITE:
                $port=null;
            break;
            case self::DBMS_MYSQL:
                $port='3306';
            break;
            case self::DBMS_POSTGRES:
                $port='5432';
            break;
            case self::DBMS_SQLSERVER:
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
    //-------------------------------------------------------------------------------------------
    public static function setInstance($pdo)    
    {
        self::$instance[ self::getDatabaseName() ] = $pdo;
    }    
    public static function getInstance()
    {
        return self::$instance[ self::getDatabaseName()];
    }    
    //------------------------------------------------------------------------------------------
    public static function executeSql( $sql, $arrParams = null, $fetchMode = PDO::FETCH_ASSOC, $boolUtfDecode = null ) {
        try {
            // converter o parametro passado para array
            if ( is_string( $arrParams ) || is_numeric( $arrParams ) ) {
                $arrParams = array( $arrParams );
            }
            $result = null;        
            
            // verificar se a quantidade de parametros é igual a quantidade de variaveis
            if ( strpos( $sql, '?' ) > 0 && is_array( $arrParams ) && count( $arrParams ) > 0 ) {
                $qtd1 = substr_count( $sql, '?' );
                $qtd2 = count( $arrParams );
                
                if ( $qtd1 != $qtd2 ) {
                    throw new InvalidArgumentException ('Quantidade de parametros diferente da quantidade utilizada na instrução sql.');
                }
            } else {
                $arrParams = array();
            }
            
            $stmt = self::getInstance()->prepare( $sql );            
            if ( !$stmt ) {
                throw new InvalidArgumentException ('Erro no comando sql');
            }
            
            $result = $stmt->execute( $arrParams );            
            if ( $result ) {
                if( !is_int($fetchMode) ){
                    $fetchMode = PDO::FETCH_ASSOC;
                }
                
                // em caso select ou insert com returning, processa o resultado
                if ( preg_match( '/^select/i', $sql ) > 0 || preg_match( '/returning/i', $sql ) > 0 || preg_match( '/^with/i', $sql ) > 0 ) {
                    $res = $stmt->fetchAll( $fetchMode );                
                    if ( is_array( $res ) || is_object( $res ) ){
                        return $res;
                    }else {
                        return null;
                    }                            
                }
            }
            return $result;
        }
        catch (Exception $e) {
            $msg = '-----------------'
                   .PHP_EOL.' mensagem: '.$e->getMessage()
                   .PHP_EOL.' SQL: '.$sql
                   .PHP_EOL.' valores: '.print_r($arrParams, TRUE)
                   ;
            error_log($msg);
        }        
    }
}
?>
