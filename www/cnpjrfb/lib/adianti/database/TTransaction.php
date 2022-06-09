<?php
namespace Adianti\Database;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TConnection;
use Adianti\Log\TLogger;
use Adianti\Log\TLoggerSTD;
use Adianti\Log\TLoggerTXT;
use Adianti\Log\AdiantiLoggerInterface;

use PDO;
use Closure;
use Exception;

/**
 * Manage Database transactions
 *
 * @version    7.4
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TTransaction
{
    private static $conn;     // active connection
    private static $logger;   // Logger object
    private static $database; // database name
    private static $dbinfo;   // database info
    private static $counter;
    private static $uniqid;
    
    /**
     * Class Constructor
     * There won't be instances of this class
     */
    private function __construct(){}
    
    /**
     * Open a connection and Initiates a transaction
     * @param $database Name of the database (an INI file).
     * @param $dbinfo Optional array with database information
     */
    public static function open($database, $dbinfo = NULL)
    {
        if (!isset(self::$counter))
        {
            self::$counter = 0;
        }
        else
        {
            self::$counter ++;
        }
        
        if ($dbinfo)
        {
            self::$conn[self::$counter]   = TConnection::openArray($dbinfo);
            self::$dbinfo[self::$counter] = $dbinfo;
        }
        else
        {
            $dbinfo = TConnection::getDatabaseInfo($database);
            self::$conn[self::$counter]   = TConnection::open($database);
            self::$dbinfo[self::$counter] = $dbinfo;
        }
        
        self::$database[self::$counter] = $database;
        self::$uniqid[self::$counter] = uniqid();
        
        $driver = self::$conn[self::$counter]->getAttribute(PDO::ATTR_DRIVER_NAME);
        
        $fake = isset($dbinfo['fake']) ? $dbinfo['fake'] : FALSE;
        
        if (!$fake)
        {
            // begins transaction
            self::$conn[self::$counter]->beginTransaction();
        }
        
        if (!empty(self::$dbinfo[self::$counter]['slog']))
        {
            $logClass = self::$dbinfo[self::$counter]['slog'];
            if (class_exists($logClass))
            {
                self::setLogger(new $logClass);
            }
        }
        else
        {
            // turn OFF the log
            self::$logger[self::$counter] = NULL;
        }
        
        return self::$conn[self::$counter];
    }
    
    /**
     * Open fake transaction
     * @param $database Name of the database (an INI file).
     */
    public static function openFake($database)
    {
        $info = TConnection::getDatabaseInfo($database);
        $info['fake'] = 1;
        
        TTransaction::open(null, $info);
    }
    
    /**
     * Returns the current active connection
     * @return PDO
     */
    public static function get()
    {
        if (isset(self::$conn[self::$counter]))
        {
            return self::$conn[self::$counter];
        }
    }
    
    /**
     * Rollback all pending operations
     */
    public static function rollback()
    {
        if (isset(self::$conn[self::$counter]))
        {
            $driver = self::$conn[self::$counter]->getAttribute(PDO::ATTR_DRIVER_NAME);
            // rollback
            self::$conn[self::$counter]->rollBack();
            self::$conn[self::$counter] = NULL;
            self::$uniqid[self::$counter] = NULL;
            self::$counter --;
            
            return true;
        }
    }
    
    /**
     * Commit all the pending operations
     */
    public static function close()
    {
        if (isset(self::$conn[self::$counter]))
        {
            $driver = self::$conn[self::$counter]->getAttribute(PDO::ATTR_DRIVER_NAME);
            $info = self::getDatabaseInfo();
            $fake = isset($info['fake']) ? $info['fake'] : FALSE;
            
            if (!$fake)
            {
                // apply the pending operations
                self::$conn[self::$counter]->commit();
            }
            
            self::$conn[self::$counter] = NULL;
            self::$uniqid[self::$counter] = NULL;
            self::$counter --;
            
            return true;
        }
    }
    
    /**
     * close all transactions
     */
    public static function closeAll()
    {
        $has_connection = true;
        
        while ($has_connection)
        {
            $has_connection = self::close();
        }
    }
    
    /**
     * rollback all transactions
     */
    public static function rollbackAll()
    {
        $has_connection = true;
        
        while ($has_connection)
        {
            $has_connection = self::rollback();
        }
    }
    
    /**
     * Assign a Logger closure function
     * @param $logger A Closure
     */
    public static function setLoggerFunction(Closure $logger)
    {
        if (isset(self::$conn[self::$counter]))
        {
            self::$logger[self::$counter] = $logger;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__);
        }
    }
    
    /**
     * Assign a Logger strategy
     * @param $logger A TLogger child object
     */
    public static function setLogger(AdiantiLoggerInterface $logger)
    {
        if (isset(self::$conn[self::$counter]))
        {
            self::$logger[self::$counter] = $logger;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__);
        }
    }
    
    /**
     * Write a message in the LOG file, using the user strategy
     * @param $message Message to be logged
     */
    public static function log($message)
    {
        // check if exist a logger
        if (!empty(self::$logger[self::$counter]))
        {
            $log = self::$logger[self::$counter];
            
            // avoid recursive log
            self::$logger[self::$counter] = NULL;
            
            if ($log instanceof AdiantiLoggerInterface)
            {
                // call log method
                $log->write($message);
            }
            else if ($log instanceof Closure)
            {
                $log($message);
            }
            
            // restore logger
            self::$logger[self::$counter] = $log;
        }
    }
    
    /**
     * Return the Database Name
     */
    public static function getDatabase()
    {
        if (!empty(self::$database[self::$counter]))
        {
            return self::$database[self::$counter];
        }
    }
    
    /**
     * Returns the Database Information
     */
    public static function getDatabaseInfo()
    {
        if (!empty(self::$dbinfo[self::$counter]))
        {
            return self::$dbinfo[self::$counter];
        }
    }
    
    /**
     * Returns the Transaction uniqid
     */
    public static function getUniqId()
    {
        if (!empty(self::$uniqid[self::$counter]))
        {
            return self::$uniqid[self::$counter];
        }
    }
    
    /**
     * Enable transaction log
     */
    public static function dump( $file = null )
    {
        if ($file)
        {
            self::setLogger( new TLoggerTXT($file) );
        }
        else
        {
            self::setLogger( new TLoggerSTD );
        }
    }
}
