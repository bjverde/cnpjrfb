<?php

class GeraGrafoForm extends TPage
{
    private $form;
    
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Gerador de Grafó');
        $this->form->generateAria(); // automatic aria-label

        $cnpj = new TEntry('cnpj');
        $cnae = new TEntry('CNAE');
        $cnae_ordem = new TEntry('cnae_ordem');

        $this->form->addFields( [new TLabel('CNPJ')],[$cnpj]);
        $this->form->addFields( [new TLabel('cnae')],[$cnae]);
        $this->form->addFields( [new TLabel('Ordem')],[$cnae_ordem]);

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        parent::add($vbox);
    }
}
