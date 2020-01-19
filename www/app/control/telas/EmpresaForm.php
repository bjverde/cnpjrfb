<?php

class EmpresaForm extends TPage
{
    private $html;
    
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Empresas');
        $this->form->generateAria(); // automatic aria-label

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        parent::add($vbox);
    }
}
