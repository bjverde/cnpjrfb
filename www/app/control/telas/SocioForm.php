<?php

class SocioForm extends TPage
{
    protected $form;      // form
    protected $datagrid;  // datagrid
    protected $loaded;
    protected $pageNavigation;  // pagination component
    
    // trait with onSave, onEdit, onDelete, onReload, onSearch...
    use Adianti\Base\AdiantiStandardFormListTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('cnpj_full'); // define the database
        $this->setActiveRecord('Socio'); // define the Active Record
        $this->setDefaultOrder('cnpj_cpf_socio', 'asc'); // define the default order
        $this->setLimit(-1); // turn off limit for datagrid

        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Sócio');
        $this->form->generateAria(); // automatic aria-label

        $tipoSocioControler = new TipoSocio();
        $listipoSocio = $tipoSocioControler->getList();

        $cnpj = new TEntry('cnpj');
        $tipo_socio = new TCombo('tipo_socio');
        $tipo_socio->addItems($listipoSocio);
        $nome_socio = new TEntry('nome_socio');

        $this->form->addFields( [new TLabel('CNPJ')],[$cnpj]);
        $this->form->addFields( [new TLabel('Tipo Sócio')],[$tipo_socio]);
        $this->form->addFields( [new TLabel('Nome')],[$nome_socio]);

        $this->form->addActionLink('Limpar',  new TAction(array($this, 'onClear')), 'fa:eraser red');


        // create the datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';
        
        // add the columns
        $col_id    = new TDataGridColumn('cnpj_cpf_socio', 'CNPJ/CPF', 'right','10%');
        $col_name  = new TDataGridColumn('nome_socio', 'Nome', 'left','90%');
        
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_name);

        $col_id->setAction( new TAction([$this, 'onReload']),   ['order' => 'id']);
        $col_name->setAction( new TAction([$this, 'onReload']), ['order' => 'name']);
        
        // create the datagrid model
        $this->datagrid->createModel();


        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        $vbox->add(TPanelGroup::pack('', $this->datagrid));
        
        parent::add($vbox);
    }
}