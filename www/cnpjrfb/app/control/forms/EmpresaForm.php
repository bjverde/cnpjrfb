<?php

use Adianti\Registry\TSession;

class EmpresaForm extends TPage
{
    protected $form; // registration form
    protected $frm;  //Registration component FormDin 5
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    function __construct()
    {
        parent::__construct();

        $this->setDatabase('cnpj_full'); // define the database
        $this->setActiveRecord('Empresa'); // define the Active Record
        $this->addFilterField('cnpj', '=', 'cnpj'); //campo, operador, campo do form
        $this->addFilterField('razao_social', '=', 'razao_social'); //campo, operador, campo do form
        $this->addFilterField('nome_fantasia', '=', 'nome_fantasia'); //campo, operador, campo do form
        $this->addFilterField('motivo_situacao', '=', 'motivo_situacao'); //campo, operador, campo do form
        $this->addFilterField('uf', '=', 'uf'); //campo, operador, campo do form
        $this->addFilterField('municipio', '-', 'municipio'); //campo, operador, campo do form
        $this->addFilterField('situacao', '=', 'situacao'); //campo, operador, campo do form
        $this->addFilterField('matriz_filial', '=', 'matriz_filial'); //campo, operador, campo do form
        $this->setDefaultOrder('cnpj', 'asc'); // define the default order

        $this->frm = new TFormDin($this,'Empresas');
        $frm = $this->frm;
        $frm->enableCSRFProtection(); // Protection cross-site request forgery 

        $listSituacaoCadastral = SituacaoCadastralEmpresa::getList();

        $frm->addCnpjField('cnpj','CNPJ',false,null,false);
        $frm->addTextField('razao_social', 'Razão Social',null,false,null,null,false);
        $frm->addTextField('nome_fantasia', 'Nome Fantasia');
        $frm->addSelectField('situacao','Situação',false,TipoEmpresaSituacao::getList());
        $frm->addSelectField('motivo_situacao','Motivo Situação',false,$listSituacaoCadastral,false);

        $frm->addTextField('logradouro', 'Logradouro');
        $frm->addSelectField('matriz_filial','Matriz',false,TipoMatrizFilial::getList(),false);

        $frm->addTextField('uf', 'UF');
        $frm->addTextField('municipio', 'Município',null,false,null,null,false);


        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        $frm->setAction( _t('Find'), 'onSearch', null, 'fa:search', 'blue' );
        $frm->setActionLink( _t('Clear'), 'clear', null, 'fa:eraser', 'red');


        $this->form = $frm->show();
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));


        // create the datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';
        //$this->datagrid->datatable = 'true'; // turn on Datatables
        
        // add the columns
        $col_cnpj        = new TDataGridColumn('cnpj', 'CNPJ','left');
        $col_cnpj->setTransformer(function ($value) {
            return StringHelper::formatCnpjCpf($value);
        });
        $this->datagrid->addColumn( $col_cnpj );
        //$this->datagrid->addColumn( new TDataGridColumn('matriz_filial','Matriz/Filial','left') );
        $this->datagrid->addColumn( new TDataGridColumn('razao_social','Razão Social','left') );
        $this->datagrid->addColumn( new TDataGridColumn('nome_fantasia','Nome Fantasia','left') );
        $col_situacao = new TDataGridColumn('situacao','Situação','left');
        $col_situacao->setTransformer(function ($value) {
            return TipoEmpresaSituacao::getByid($value);
        });
        $this->datagrid->addColumn( $col_situacao );
        //$this->datagrid->addColumn( new TDataGridColumn('data_situacao','Dt Situação','left') );
        //$this->datagrid->addColumn( new TDataGridColumn('motivo_situacao','Motivo Situacao','left') );
        $this->datagrid->addColumn( new TDataGridColumn('uf','UF','center') );
        $this->datagrid->addColumn( new TDataGridColumn('municipio','Municipio','left') );
        /*
        $this->datagrid->addColumn( new TDataGridColumn('cod_pais','cod_pais','left') );
        $this->datagrid->addColumn( new TDataGridColumn('nome_pais','nome_pais','left') );
        $this->datagrid->addColumn( new TDataGridColumn('cod_nat_juridica','cod_nat_juridica','left') );
        $this->datagrid->addColumn( new TDataGridColumn('data_inicio_ativ','data_inicio_ativ','left') );
        $this->datagrid->addColumn( new TDataGridColumn('cnae_fiscal','cnae_fiscal','left') );
        $this->datagrid->addColumn( new TDataGridColumn('tipo_logradouro','tipo_logradouro','left') );
        $this->datagrid->addColumn( new TDataGridColumn('logradouro','logradouro','left') );
        $this->datagrid->addColumn( new TDataGridColumn('numero','numero','left') );
        $this->datagrid->addColumn( new TDataGridColumn('complemento','complemento','left') );
        $this->datagrid->addColumn( new TDataGridColumn('bairro','bairro','left') );
        $this->datagrid->addColumn( new TDataGridColumn('cep','cep','left') );
        $this->datagrid->addColumn( new TDataGridColumn('uf','uf','left') );
        $this->datagrid->addColumn( new TDataGridColumn('cod_municipio','cod_municipio','left') );
        $this->datagrid->addColumn( new TDataGridColumn('municipio','Municipio','left') );
        $this->datagrid->addColumn( new TDataGridColumn('ddd_1','ddd_1','left') );
        $this->datagrid->addColumn( new TDataGridColumn('telefone_1','telefone_1','left') );
        $this->datagrid->addColumn( new TDataGridColumn('ddd_2','ddd_2','left') );
        $this->datagrid->addColumn( new TDataGridColumn('telefone_2','telefone_2','left') );
        $this->datagrid->addColumn( new TDataGridColumn('ddd_fax','ddd_fax','left') );
        $this->datagrid->addColumn( new TDataGridColumn('num_fax','num_fax','left') );
        $this->datagrid->addColumn( new TDataGridColumn('email','email','left') );
        $this->datagrid->addColumn( new TDataGridColumn('qualif_resp','qualif_resp','left') );
        $this->datagrid->addColumn( new TDataGridColumn('capital_social','capital_social','left') );
        $this->datagrid->addColumn( new TDataGridColumn('porte','porte','left') );
        $this->datagrid->addColumn( new TDataGridColumn('opc_simples','opc_simples','left') );
        $this->datagrid->addColumn( new TDataGridColumn('data_opc_simples','data_opc_simples','left') );
        $this->datagrid->addColumn( new TDataGridColumn('data_exc_simples','data_exc_simples','left') );
        $this->datagrid->addColumn( new TDataGridColumn('opc_mei','opc_mei','left') );
        $this->datagrid->addColumn( new TDataGridColumn('sit_especial','sit_especial','left') );
        $this->datagrid->addColumn( new TDataGridColumn('data_sit_especial','data_sit_especial','left') );
        */


        //$action1 = new TDataGridAction(['EmpresaViewForm', 'onView'],  ['key' => '{cnpj}'], ['register_state' => 'false']  );
        //$this->datagrid->addAction($action1, 'Detalhar Empresa', 'fa:building #7C93CF');

        $actionEmpresaView = new TDataGridAction(['EmpresaViewForm', 'onView'],  ['key' => '{cnpj}'], ['register_state' => 'false']  );
        $actionEmpresaView->setLabel('Detalhar Empresa');
        $actionEmpresaView->setImage('fa:building #7C93CF');
        //$this->datagrid->addAction($actionEmpresaView);

        $actionGeraGrafo = new TDataGridAction(['GeraGrafoForm', 'gerarGrafo'],  ['cnpj' => '{cnpj}'], ['register_state' => 'false']  );
        $actionGeraGrafo->setLabel('Gera Grafo');
        $actionGeraGrafo->setImage('fa:magic fa-fw black');
        //$this->datagrid->addAction($actionGeraGrafo);

        $action_group = new TDataGridActionGroup('Ações ', 'fa:th');
        $action_group->addAction($actionEmpresaView);
        $action_group->addAction($actionGeraGrafo);
        //add the actions to the datagrid
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

    /**
     * Clear filters
     */
    function clear()
    {
        $this->clearFilters();
        $this->onReload();
    }
}
