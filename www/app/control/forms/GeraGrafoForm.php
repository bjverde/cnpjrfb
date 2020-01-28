<?php

class GeraGrafoForm extends TPage
{
    protected $form;      // form
    protected $datagrid;  // datagrid
    protected $loaded;
    protected $pageNavigation;  // pagination component
    
    // trait with onSave, onEdit, onDelete, onReload, onSearch...
    use Adianti\Base\AdiantiStandardFormListTrait;

    function __construct()
    {
        parent::__construct();

        $this->setDatabase('samples'); // define the database
        $this->setActiveRecord('Category'); // define the Active Record
        $this->setDefaultOrder('id', 'asc'); // define the default order
        $this->setLimit(-1); // turn off limit for datagrid

        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Gerador de Grafó');
        $this->form->generateAria(); // automatic aria-label

        $cnpj = new TEntry('cnpj');
        $cnae = new TEntry('CNAE');
        $cnae_ordem = new TEntry('cnae_ordem');

        $this->form->addFields( [new TLabel('CNPJ')],[$cnpj]);
        $this->form->addFields( [new TLabel('cnae')],[$cnae]);
        $this->form->addFields( [new TLabel('Ordem')],[$cnae_ordem]);

        $this->form->addActionLink('Limpar',  new TAction(array($this, 'onClear')), 'fa:eraser red');

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        parent::add($vbox);
    }
}
