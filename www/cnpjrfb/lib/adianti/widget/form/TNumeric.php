<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Form\TEntry;

/**
 * Numeric Widget
 *
 * @version    7.1
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TNumeric extends TEntry implements AdiantiWidgetInterface
{
    public function __construct($name, $decimals, $decimalsSeparator, $thousandSeparator, $replaceOnPost = true)
    {
        parent::__construct($name);
        $dec_pattern = $decimalsSeparator == '.' ? '\\.' : $decimalsSeparator;
        $tho_pattern = $thousandSeparator == '.' ? '\\.' : $thousandSeparator;
        
        $this->tag->{'pattern'}   = '^\\$?(([1-9](\\d*|\\d{0,2}('.$tho_pattern.'\\d{3})*))|0)('.$dec_pattern.'\\d{1,2})?$';
        $this->tag->{'inputmode'} = 'numeric';
        
        parent::setNumericMask($decimals, $decimalsSeparator, $thousandSeparator, $replaceOnPost);
    }
}
