<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TEntry;
use Adianti\Control\TAction;

/**
 * BarCode Widget
 *
 * @version    7.4
 * @package    widget
 * @subpackage form
 * @author     Lucas Tomasi
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TBarCodeInputReader extends TEntry implements AdiantiWidgetInterface
{
    protected $formName;
    protected $name;
    protected $id;
    protected $size;
    protected $changeFunction;
    protected $changeAction;

    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->id = 'tbarcodeinputreader_'.mt_rand(1000000000, 1999999999);
        $this->tag->{'widget'} = 'tbarcodeinputreader';
        $this->tag->{'autocomplete'} = 'off';
    }

    /**
     * Set change function
     */
    public function setChangeFunction($function)
    {
        $this->changeFunction = $function;
    }

    /**
     * Define the action to be executed when the user changes the content
     * @param $action TAction object
     */
    public function setChangeAction(TAction $action)
    {
        $this->changeAction = $action;
    }

    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        $wrapper = new TElement('div');
        $wrapper->{'class'} = 'input-group';
        $wrapper->{'style'} = 'float:inherit;width: 100%';

        $span = new TElement('span');
        $span->{'class'} = 'input-group-addon tbarcodeinputreader';

        $outer_size = 'undefined';
        if (strstr((string) $this->size, '%') !== FALSE)
        {
            $outer_size = $this->size;
            $this->size = '100%';
        }

        if ($this->changeAction)
        {
            if (!TForm::getFormByName($this->formName) instanceof TForm)
            {
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
            }

            $string_action = $this->changeAction->serialize(FALSE);
            $this->setProperty('changeaction', "__adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback')");
            $this->setProperty('onChange', $this->getProperty('changeaction'), FALSE);
        }
        
        if (isset($this->changeFunction))
        {
            $this->setProperty('changeaction', $this->changeFunction, FALSE);
            $this->setProperty('onChange', $this->changeFunction, FALSE);
        }
        
        $i = new TElement('i');
        $i->{'class'} = 'fa fa-barcode';
        $span->{'onclick'} = "tbarcodeinputreader_open_reader('{$this->id}');";
        
        $span->add($i);
        ob_start();
        parent::show();
        $child = ob_get_contents();
        ob_end_clean();
        
        $wrapper->add($child);
        $wrapper->add($span);
        $wrapper->show();

        if (!parent::getEditable())
        {
            self::disableField($this->formName, $this->name);
        }
    }
}
