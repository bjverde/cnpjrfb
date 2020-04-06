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
        $this->addFilterField('cnpj', 'like', 'cnpj'); //campo, operador, campo do form
        $this->addFilterField('tipo_socio', '=', 'tipo_socio'); //campo, operador, campo do form
        $this->addFilterField('nome_socio', 'like', 'nome_socio'); //campo, operador, campo do form
        $this->addFilterField('cnpj_cpf_socio', 'like', 'cnpj_cpf_socio'); //campo, operador, campo do form
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
        $cnpj_cpf_socio = new TEntry('cnpj_cpf_socio');
        $cnpj_cpf_socio->setMask('***.999.999-**',true);

        $this->form->addFields( [new TLabel('CNPJ')],[$cnpj],[new TLabel('Tipo Sócio')],[$tipo_socio]);
        $this->form->addFields( [new TLabel('Nome')],[$nome_socio]);
        $this->form->addFields( [new TLabel('CPF')],[$cnpj_cpf_socio]);

        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));
        
        // add form actions
        $this->form->addAction('Find', new TAction([$this, 'onSearch']), 'fa:search blue');        
        $this->form->addActionLink('Clear',  new TAction([$this, 'clear']), 'fa:eraser red');

        // create the datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';
        //$this->datagrid->datatable = 'true'; // turn on Datatables
        
        // add the columns
        $col_cnpj        = new TDataGridColumn('cnpj', 'CNPJ Empresa', 'right');
        $col_cnpj->setTransformer(function ($value) {
            return StringHelper::formatCnpjCpf($value);
        });
        $col_tipo_socio  = new TDataGridColumn('tipo_socio', 'Tipo Sócio', 'left');
        $col_tipo_socio->setTransformer(function ($value) {
            return TipoSocio::getByid($value);
        });
        $col_nome_socio  = new TDataGridColumn('nome_socio', 'Sócio', 'left');
        
        $this->datagrid->addColumn($col_cnpj);
        $this->datagrid->addColumn($col_nome_socio);
        $this->datagrid->addColumn( new TDataGridColumn('cnpj_cpf_socio','CPF Socio','left') );
        $this->datagrid->addColumn($col_tipo_socio);
        $col_cod_qualificacao  = new TDataGridColumn('cod_qualificacao','Qualificação','left');
        $col_cod_qualificacao->setTransformer(function ($value) {
            return TipoSocioQualificacao::getByid($value);
        });        
        $this->datagrid->addColumn( $col_cod_qualificacao );
        $this->datagrid->addColumn( new TDataGridColumn('perc_capital','% Capital','left') );
        $this->datagrid->addColumn( new TDataGridColumn('data_entrada','Dt Entrada','left') );
        //$this->datagrid->addColumn( new TDataGridColumn('cod_pais_ext','cod_pais_ext','left') );
        //$this->datagrid->addColumn( new TDataGridColumn('nome_pais_ext','nome_pais_ext','left') );
        //$this->datagrid->addColumn( new TDataGridColumn('cpf_repres','cpf_repres','left') );
        //$this->datagrid->addColumn( new TDataGridColumn('nome_repres','nome_repres','left') );
        //$this->datagrid->addColumn( new TDataGridColumn('cod_qualif_repres','cod_qualif_repres','left') );
        

        // creates two datagrid actions
        $actionEmpresaView = new TDataGridAction(['EmpresaViewForm', 'onView'],  ['key' => '{cnpj}'], ['register_state' => 'false']  );
        $actionEmpresaView->setLabel('Detalhar Empresa');
        $actionEmpresaView->setImage('fa:building #7C93CF');

        $actionSocio = new TDataGridAction(['SocioViewForm', 'onView'],  ['cnpj_cpf_socio' => '{cnpj_cpf_socio}','nome_socio' => '{nome_socio}'], ['register_state' => 'false']  );
        $actionSocio->setLabel('Detalhar essa sociedade');
        $actionSocio->setImage('fa:user green');

        $action3 = new TDataGridAction([$this, 'onFindSocios'],   ['cnpj' => '{cnpj}' ] );
        $action3->setLabel('Buscar outros Socios');
        $action3->setImage('fa:users red');

        $actionGeraGrafo = new TDataGridAction(['GeraGrafoForm', 'gerarGrafo'],  ['cnpj' => '{cnpj}','cnpj_cpf_socio' => '{cnpj_cpf_socio}','nome_socio' => '{nome_socio}'], ['register_state' => 'false']  );
        $actionGeraGrafo->setLabel('Gera Grafo');
        $actionGeraGrafo->setImage('fa:magic fa-fw');

        
        $action_group = new TDataGridActionGroup('Ações ', 'fa:th');
        $action_group->addAction($actionSocio);
        $action_group->addAction($actionEmpresaView);
        //$action_group->addAction($action3);
        $action_group->addAction($actionGeraGrafo);
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

    function onViewEmpresa($param)
    {
        //$this->clearFilters();
        //$this->onReload();
        var_dump($param);
        $data = $this->form->getData();
        var_dump($data);
    }

    function onFindSocios($param)
    {
        $this->clearFilters();
        $data = new stdClass();
        $data->cnpj = $param['cnpj'];
        $this->form->setData($data);
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