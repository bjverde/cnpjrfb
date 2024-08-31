<?php
namespace Adianti\Widget\Wrapper;

use Adianti\Widget\Wrapper\TQuickForm;
use Adianti\Widget\Container\TTable;
use Adianti\Widget\Container\TNotebook;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Base\TElement;
use Adianti\Control\TAction;

/**
 * Create quick forms with a notebook wrapper
 *
 * @version    7.6
 * @package    widget
 * @subpackage wrapper
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TQuickNotebookForm extends TQuickForm
{
    protected $notebook;
    protected $table;
    protected $vertical_box;
    
    /**
     * Class Constructor
     * @param $name Form Name
     */
    public function __construct($name = 'my_form')
    {
        parent::__construct($name);
        
        $this->vertical_box = new TVBox;
        $this->vertical_box->{'style'} = 'width: 100%';
        $this->notebook = new TNotebook;
        $this->hasAction = FALSE;
        
        $this->fieldsByRow = 1;
    }
    
    /**
     * Set the notebook wrapper
     * @param $notebook Notebook wrapper
     */
    public function setNotebookWrapper($notebook)
    {
        $this->notebook = $notebook;
    }
    
    /**
     * Add a form title
     * @param $title     Form title
     */
    public function setFormTitle($title)
    {
        parent::setFormTitle($title);
        $this->vertical_box->add($this->table);
    }
    
    /**
     * Append a notebook page
     * @param $title     Page title
     * @param $cotnainer Page container
     */
    public function appendPage($title, $container = NULL)
    {
        if (empty($container))
        {
            $container = new TTable;
            $container->{'width'} = '100%';
        }
        
        if ($this->notebook->getPageCount() == 0)
        {
            $this->vertical_box->add($this->notebook);
        }
        
        $this->table = $container;
        $this->notebook->appendPage($title, $this->table);
        $this->fieldPositions = 0;
    }
    
    /**
     * Add a form action
     * @param $label  Action Label
     * @param $action TAction Object
     * @param $icon   Action Icon
     */
    public function addQuickAction($label, TAction $action, $icon = 'fa:save')
    {
        $this->table = new TTable;
        $this->table->{'width'} = '100%';
        $this->vertical_box->add($this->table);
        
        parent::addQuickAction($label, $action, $icon);
    }
    
    /**
     * Show the component
     */
    public function show()
    {
        $this->notebook->{'style'} = 'margin:10px';
        
        // add the table to the form
        parent::pack($this->vertical_box);
        parent::show();
    }
}
