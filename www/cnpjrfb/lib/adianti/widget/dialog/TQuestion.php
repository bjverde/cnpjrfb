<?php
namespace Adianti\Widget\Dialog;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TAction;
use Adianti\Widget\Base\TScript;

/**
 * Question Dialog
 *
 * @version    7.4
 * @package    widget
 * @subpackage dialog
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TQuestion
{
    /**
     * Class Constructor
     * @param  $message    A string containint the question
     * @param  $action_yes Action taken for YES response
     * @param  $action_no  Action taken for NO  response
     * @param  $title_msg  Dialog Title
     */
    public function __construct($message, TAction $action_yes = NULL, TAction $action_no = NULL, $title_msg = '', $label_yes = '', $label_no = '')
    {
        $title        = ( $title_msg ? $title_msg : AdiantiCoreTranslator::translate('Question') );
        $callback_yes = "function () {}";
        $callback_no  = "function () {}";
        $label_yes    = !empty($label_yes) ? $label_yes : AdiantiCoreTranslator::translate('Yes');
        $label_no     = !empty($label_no) ? $label_no : AdiantiCoreTranslator::translate('No');
        
        if ($action_yes && $action_yes->isStatic())
        {
            $action_yes->setParameter('static', '1' );
        }
        
        if ($action_no && $action_no->isStatic())
        {
            $action_no->setParameter('static', '1' );
        }
        
        $title = addslashes($title);
        $message = addslashes($message);
        
        if ($action_yes)
        {
            $callback_yes = "function () { __adianti_load_page('{$action_yes->serialize()}') }";
        }
        
        if ($action_no)
        {
            $callback_no = "function () { __adianti_load_page('{$action_no->serialize()}') }";
        }
        
        TScript::create("__adianti_question('{$title}', '{$message}', $callback_yes, $callback_no, '{$label_yes}', '{$label_no}')");
    }
}
