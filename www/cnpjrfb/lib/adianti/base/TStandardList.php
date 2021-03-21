<?php
namespace Adianti\Base;

use Adianti\Control\TPage;

/**
 * Standard page controller for listings
 *
 * @version    7.3
 * @package    base
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TStandardList extends TPage
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    
    use AdiantiStandardListTrait;
}
