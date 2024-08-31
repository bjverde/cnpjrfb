<?php
namespace Adianti\Base;

use Adianti\Control\TPage;

/**
 * Standard page controller for forms
 *
 * @version    7.6
 * @package    base
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TStandardForm extends TPage
{
    protected $form;
    
    use AdiantiStandardFormTrait;
}
