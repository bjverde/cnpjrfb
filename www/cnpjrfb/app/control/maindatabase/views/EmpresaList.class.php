<?php

class EmpresaList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;

    private $filter_criteria;


    private static $primaryKey = 'cnpj_basico';
    private static $formName = 'form_EmpresaList';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];

    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;    

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param = null)
    {
        parent::__construct();
        $this->setLimit(20);
        $this->setDatabase('maindatabase'); // define the database
        $this->setActiveRecord('empresa'); // define the Active Record
        $this->addFilterField('cnpj_basico', '=', 'cnpj_basico'); //campo, operador, campo do form
        $this->addFilterField('razao_social', 'like', 'razao_social'); //campo, operador, campo do form
        $this->addFilterField('natureza_juridica', '=', 'natureza_juridica'); //campo, operador, campo do form
        $this->addFilterField('qualificacao_responsavel', '=', 'qualificacao_responsavel'); //campo, operador, campo do form
        $this->addFilterField('capital_social', 'like', 'capital_social'); //campo, operador, campo do form
        $this->addFilterField('porte_empresa', '=', 'porte_empresa'); //campo, operador, campo do form
        $this->addFilterField('ente_federativo_responsavel', 'like', 'ente_federativo_responsavel'); //campo, operador, campo do form

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("Empresa");
        $this->limit = 20;

        $cnpj_basico = new TNumeric('cnpj_basico', '0', ',', '' );
        $razao_social = new TEntry('razao_social');
        $natureza_juridica = new TDBCombo('natureza_juridica', 'maindatabase', 'natju', 'codigo', '{descricao}','descricao asc'  );
        $qualificacao_responsavel = new TDBCombo('qualificacao_responsavel', 'maindatabase', 'quals', 'codigo', '{descricao}','descricao asc'  );
        $capital_social = new TNumeric('capital_social', '2', ',', '.' );
        $porte_empresa = new TCombo('porte_empresa');
        $ente_federativo_responsavel = new TEntry('ente_federativo_responsavel');


        $cnpj_basico->placeholder = "NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS  DO CNPJ).";

        $natureza_juridica->enableSearch();
        $qualificacao_responsavel->enableSearch();

        $cnpj_basico->setMaxLength(8);
        $razao_social->setMaxLength(1000);
        $ente_federativo_responsavel->setMaxLength(45);
        $porte_empresa->addItems(TipoPorteEmpresa::getList());

        $cnpj_basico->setSize('100%');
        $razao_social->setSize('100%');
        $porte_empresa->setSize('100%');
        $capital_social->setSize('100%');
        $natureza_juridica->setSize('100%');
        $qualificacao_responsavel->setSize('100%');
        $ente_federativo_responsavel->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("CNPJ Básico:", null, '14px', null)],[$cnpj_basico]);
        $row2 = $this->form->addFields([new TLabel("Razão social:", null, '14px', null)],[$razao_social]
                                      ,[new TLabel("Natureza jurídica:", null, '14px', null)],[$natureza_juridica]);
        $row3 = $this->form->addFields([new TLabel("Qualificação responsável:", null, '14px', null)],[$qualificacao_responsavel]
                                      ,[new TLabel("Capital social:", null, '14px', null)],[$capital_social]);
        $row4 = $this->form->addFields([new TLabel("Porte empresa:", null, '14px', null)],[$porte_empresa]
                                      ,[new TLabel("Ente federativo responsável:", null, '14px', null)],[$ente_federativo_responsavel]);

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar", new TAction([$this, 'clear']), 'fas:eraser #F44336');
        $this->btn_onclear = $btn_onclear;

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->disableHtmlConversion();

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_cnpj_basico = new TDataGridColumn('cnpj_basico', "CNPJ Básico:", 'left');
        $column_razao_social = new TDataGridColumn('razao_social', "Razão social", 'left');
        $column_natureza_juridica = new TDataGridColumn('fk_natureza_juridica->descricao', "Natureza jurídica", 'left');
        $column_qualificacao_responsavel = new TDataGridColumn('fk_qualificacao_responsavel->descricao', "Qualificação responsável", 'left');
        $column_capital_social_transformed = new TDataGridColumn('capital_social', "Capital social", 'left');
        $column_porte_empresa = new TDataGridColumn('porte_empresa', "Porte empresa", 'left');
        $column_porte_empresa->setTransformer(function($value, $object, $row) 
        {
            return TipoPorteEmpresa::getByid($value);
        });
        $column_ente_federativo_responsavel = new TDataGridColumn('ente_federativo_responsavel', "Ente federativo responsável", 'left');

        $column_capital_social_transformed->setTransformer(function($value, $object, $row) 
        {
            return Transforme::numeroBrasil($value);
        });        

        $this->datagrid->addColumn($column_cnpj_basico);
        $this->datagrid->addColumn($column_razao_social);
        $this->datagrid->addColumn($column_natureza_juridica);
        $this->datagrid->addColumn($column_qualificacao_responsavel);
        $this->datagrid->addColumn($column_capital_social_transformed);
        $this->datagrid->addColumn($column_porte_empresa);
        $this->datagrid->addColumn($column_ente_federativo_responsavel);

        $action_group = new TDataGridActionGroup("Ações", 'fas:cog');
        $action_group->addHeader('');

        $actionEmpresa = Transforme::getDataGridActionDetalharEmpresa();
        $action_group->addAction($actionEmpresa);

        $this->datagrid->addActionGroup($action_group);    

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("");
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';

        $panel->addFooter($this->pageNavigation);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Tabelas","Empresa"]));
        }
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);
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