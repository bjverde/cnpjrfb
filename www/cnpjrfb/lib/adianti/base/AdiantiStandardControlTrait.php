<?php
namespace Adianti\Base;

use Adianti\Core\AdiantiCoreTranslator;
use Exception;
use ReflectionClass;

/**
 * Standard Control Trait
 *
 * @version    7.6
 * @package    base
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
trait AdiantiStandardControlTrait
{
    protected $database; // Database name
    protected $activeRecord;    // Active Record class name
    
    /**
     * method setDatabase()
     * Define the database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }
    
    /**
     * method setActiveRecord()
     * Define wich Active Record class will be used
     */
    public function setActiveRecord($activeRecord)
    {
        if (class_exists($activeRecord))
        {
            if (is_subclass_of($activeRecord, 'TRecord'))
            {
                $this->activeRecord = $activeRecord;
            }
            else
            {
                throw new Exception(AdiantiCoreTranslator::translate('The class ^1 was not accepted as argument. The class informed as parameter must be subclass of ^2.', $activeRecord, 'TRecord'));
            }
        }
        else
        {
            throw new Exception(AdiantiCoreTranslator::translate('The class ^1 was not found. Check the class name or the file name. They must match', $activeRecord));
        }
    }
}
