<?php

class SocioForm extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('cnpj_full'); // define the database
        $this->setActiveRecord('Socio'); // define the Active Record
        $this->addFilterField('cnpj', '=', 'cnpj'); //campo, operador, campo do form
        //$this->addFilterField('tipo_socio', 'tipo_socio', 'tipo_socio'); //campo, operador, campo do form
        $this->addFilterField('nome_socio', 'like', 'nome_socio'); //campo, operador, campo do form
        $this->setDefaultOrder('cnpj_cpf_socio', 'asc'); // define the default order

        $this->form = new BootstrapFormBuilder(__CLASS__);
        $this->form->setFormTitle('Sócio');
        $this->form->generateAria(); // automatic aria-label

        $tipoSocioControler = new TipoSocio();
        $listipoSocio = $tipoSocioControler->getList();

        $cnpjLabel = 'CNPJ';
        $formDinCnpjField = new TFormDinCnpjField('cnpj',$cnpjLabel);
        $cnpj = $formDinCnpjField->getAdiantiObj();
        
        $tipo_socio = new TCombo('tipo_socio');
        $tipo_socio->addItems($listipoSocio);
        $nome_socio = new TEntry('nome_socio');

        $this->form->addFields( [new TLabel('CNPJ')],[$cnpj],[new TLabel('Tipo Sócio')],[$tipo_socio]);
        $this->form->addFields( [new TLabel('Nome')],[$nome_socio]);

        // add form actions
        $this->form->addAction('Find', new TAction([$this, 'onSearch']), 'fa:search blue');        
        $this->form->addActionLink('Clear',  new TAction([$this, 'clear']), 'fa:eraser red');

        // keep the form filled with the search data
        $this->form->setData( TSession::getValue('StandardDataGridView_filter_data') );


        // create the datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';
        //$this->datagrid->datatable = 'true'; // turn on Datatables
        
        // add the columns
        $col_cnpj        = new TDataGridColumn('cnpj', 'CNPJ Empresa', 'right');
        $col_tipo_socio  = new TDataGridColumn('tipo_socio', 'Tipo Sócio', 'left');
        $col_nome_socio  = new TDataGridColumn('nome_socio', 'Sócio', 'left');
        
        $this->datagrid->addColumn($col_cnpj);
        $this->datagrid->addColumn($col_tipo_socio);
        $this->datagrid->addColumn($col_nome_socio);
        $this->datagrid->addColumn( new TDataGridColumn('cnpj_cpf_socio','CPF Socio','left') );
        $this->datagrid->addColumn( new TDataGridColumn('cod_qualificacao','cod_qualificacao','left') );
        $this->datagrid->addColumn( new TDataGridColumn('perc_capital','perc_capital','left') );
        $this->datagrid->addColumn( new TDataGridColumn('data_entrada','data_entrada','left') );
        $this->datagrid->addColumn( new TDataGridColumn('cod_pais_ext','cod_pais_ext','left') );
        $this->datagrid->addColumn( new TDataGridColumn('nome_pais_ext','nome_pais_ext','left') );
        $this->datagrid->addColumn( new TDataGridColumn('cpf_repres','cpf_repres','left') );
        $this->datagrid->addColumn( new TDataGridColumn('nome_repres','nome_repres','left') );
        $this->datagrid->addColumn( new TDataGridColumn('cod_qualif_repres','cod_qualif_repres','left') );
        

        // creates two datagrid actions
        $action1 = new TDataGridAction([$this, 'onView'],     ['key' => '{cnpj}']  );
        $action2 = new TDataGridAction([$this, 'onDelete'],   ['key' => '{cnpj}' ] );
        $action3 = new TDataGridAction([$this, 'onViewCity'], ['key' => '{cnpj}' ] );
        
        $action1->setLabel('View name');
        $action1->setImage('fa:search #7C93CF');
        
        $action2->setLabel('Try to delete');
        $action2->setImage('far:trash-alt red');
        
        $action3->setLabel('View city');
        $action3->setImage('far:hand-pointer green');
        
        $action_group = new TDataGridActionGroup('Actions ', 'fa:th');
        
        $action_group->addHeader('Available Options');
        $action_group->addAction($action1);
        $action_group->addAction($action2);
        $action_group->addSeparator();
        $action_group->addHeader('Another Options');
        $action_group->addAction($action3);
        
        // add the actions to the datagrid
        $this->datagrid->addActionGroup($action_group);

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));

        // creates the page structure using a table
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        $vbox->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        // add the table inside the page
        parent::add($vbox);
    }

    function onView()
    {
        $this->clearFilters();
        $this->onReload();
    }

    function onDelete()
    {
        $this->clearFilters();
        $this->onReload();
    }

    function onViewCity()
    {
        $this->clearFilters();
        $this->onReload();
    }

    /**
     * Clear filters
     */
    function clear()
    {
        $this->clearFilters();
        $this->onReload();
    }
}