<?php
namespace Adianti\Registry;

/**
 * Registry interface
 *
 * @version    7.6
 * @package    registry
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
interface AdiantiRegistryInterface
{
    public static function enabled();
    public static function setValue($key, $value);
    public static function getValue($key);
    public static function delValue($key);
    public static function clear();
}
