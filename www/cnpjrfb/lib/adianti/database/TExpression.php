<?php
namespace Adianti\Database;

/**
 * Base class for TCriteria and TFilter (composite pattern implementation)
 *
 * @version    7.4
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
abstract class TExpression
{
    // logic operators
    const AND_OPERATOR = 'AND ';
    const OR_OPERATOR  = 'OR ';
    
    // force method rewrite in child classes
    abstract public function dump();
}
