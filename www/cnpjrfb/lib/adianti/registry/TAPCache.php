<?php
namespace Adianti\Registry;

use Adianti\Registry\AdiantiRegistryInterface;

/**
 * Adianti APC Record Cache
 *
 * @version    7.4
 * @package    registry
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TAPCache implements AdiantiRegistryInterface
{
    /**
     * Returns if the service is active
     */
    public static function enabled()
    {
        return extension_loaded('apcu');
    }
    
    /**
     * Store a variable in cache
     * @param $key    Key
     * @param $value  Value
     */
    public static function setValue($key, $value)
    {
        return apcu_store(APPLICATION_NAME . '_' . $key, serialize($value));
    }
    
    /**
     * Get a variable from cache
     * @param $key    Key
     */
    public static function getValue($key)
    {
        return unserialize(apcu_fetch(APPLICATION_NAME . '_' . $key));
    }
    
    /**
     * Delete a variable from cache
     * @param $key    Key
     */
    public static function delValue($key)
    {
        return apcu_delete(APPLICATION_NAME . '_' . $key);
    }
    
    /**
     * Clear cache
     */
    public static function clear()
    {
        return apcu_clear_cache();
    }
}
