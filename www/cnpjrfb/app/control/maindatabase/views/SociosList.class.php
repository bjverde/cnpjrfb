<?php

class SociosList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;

    private $filter_criteria;

    private static $primaryKey = 'cnpj_basico';
    private static $formName = 'form_SociosList';
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
        $this->setActiveRecord('socios'); // define the Active Record
        $this->addFilterField('cnpj_basico', '=', 'cnpj_basico'); //campo, operador, campo do form
        $this->addFilterField('nome_socio_razao_social', 'like', 'nome_socio_razao_social'); //campo, operador, campo do form
        $this->addFilterField('cpf_cnpj_socio', 'like', 'cpf_cnpj_socio'); //campo, operador, campo do form
        $this->addFilterField('identificador_socio', '=', 'identificador_socio'); //campo, operador, campo do form
        $this->addFilterField('qualificacao_socio', '=', 'qualificacao_socio'); //campo, operador, campo do form
        $this->addFilterField('data_entrada_sociedade', '=', 'data_entrada_sociedade'); //campo, operador, campo do form
        $this->addFilterField('pais', '=', 'pais'); //campo, operador, campo do form
        $this->addFilterField('faixa_etaria', '=', 'faixa_etaria'); //campo, operador, campo do form
        $this->addFilterField('representante_legal', '=', 'representante_legal'); //campo, operador, campo do form
        $this->addFilterField('nome_do_representante', 'like', 'nome_do_representante'); //campo, operador, campo do form
        $this->addFilterField('qualificacao_representante_legal', '=', 'qualificacao_representante_legal'); //campo, operador, campo do form
        //$this->setDefaultOrder('cnpj_basico', 'asc'); // define the default order        

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("Sócio da Empresa");
        $this->limit = 20;

        $cnpj_basico = new TNumeric('cnpj_basico', '0', ',', '' );
        $nome_socio_razao_social = new TEntry('nome_socio_razao_social');
        $cpf_cnpj_socio = new TEntry('cpf_cnpj_socio');
        $identificador_socio = new TCombo('identificador_socio');
        $qualificacao_socio = new TDBCombo('qualificacao_socio', 'maindatabase', 'quals', 'codigo', '{descricao}','codigo asc'  );
        $data_entrada_sociedade = new TDate('data_entrada_sociedade');
        $pais = new TDBCombo('pais', 'maindatabase', 'pais', 'codigo', '{descricao}','codigo asc'  );
        $faixa_etaria = new TCombo('faixa_etaria');
        $representante_legal = new TEntry('representante_legal');
        $nome_do_representante = new TEntry('nome_do_representante');
        $qualificacao_representante_legal = new TDBCombo('qualificacao_representante_legal', 'maindatabase', 'quals', 'codigo', '{descricao}','descricao asc'  );


        $identificador_socio->addItems(TipoSocio::getList());
        $faixa_etaria->addItems(TipoFaixaEtaria::getList());
        $data_entrada_sociedade->setMask('dd/mm/yyyy');
        $data_entrada_sociedade->setDatabaseMask('yyyy-mm-dd');

        $cnpj_basico->placeholder = "NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS  DO CNPJ).";

        $pais->enableSearch();
        $qualificacao_socio->enableSearch();
        $qualificacao_representante_legal->enableSearch();

        $cnpj_basico->setMaxLength(8);
        $cpf_cnpj_socio->setMaxLength(45);
        $representante_legal->setMaxLength(45);
        $nome_do_representante->setMaxLength(500);
        $nome_socio_razao_social->setMaxLength(1000);

        $row1 = $this->form->addFields([new TLabel("CNPJ Básico:", null, '14px', null)],[$cnpj_basico]);
        $row2 = $this->form->addContent([new TFormSeparator("Sócio", '#333', '18', '#eee')]);
        $row3 = $this->form->addFields([new TLabel("Nome / Razão social:", null, '14px', null)],[$nome_socio_razao_social]);
        $row4 = $this->form->addFields([new TLabel("CPF/CNPJ", null, '14px', null)],[$cpf_cnpj_socio],[new TLabel("Tipo sócio:", null, '14px', null)],[$identificador_socio]);
        $row5 = $this->form->addFields([new TLabel("Qualificação:", null, '14px', null)],[$qualificacao_socio]);
        $row6 = $this->form->addFields([new TLabel("Data entrada sociedade:", null, '14px', null)],[$data_entrada_sociedade],[new TLabel("Pais:", null, '14px', null)],[$pais]);
        $row7 = $this->form->addFields([new TLabel("Faixa etaria:", null, '14px', null)],[$faixa_etaria]);
        $row8 = $this->form->addContent([new TFormSeparator("Representante Legal", '#333', '18', '#eee')]);
        $row9 = $this->form->addFields([new TLabel("CPF / CNPJ", null, '14px', null)],[$representante_legal]);
        $row10 = $this->form->addFields([new TLabel("Nome", null, '14px', null)],[$nome_do_representante]);
        $row11 = $this->form->addFields([new TLabel("Qualificacao", null, '14px', null)],[$qualificacao_representante_legal]);

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

        $column_cnpj_basico = new TDataGridColumn('cnpj_basico', "CNPJ Básico", 'left');
        $column_identificador_socio_transformed = new TDataGridColumn('identificador_socio', "Tip Sócio", 'left');
        $column_nome_socio_razao_social = new TDataGridColumn('nome_socio_razao_social', "Nome socio razao social", 'left');
        $column_cpf_cnpj_socio = new TDataGridColumn('cpf_cnpj_socio', "CPF/CNPJ", 'left');
        $column_qualificacao_socio = new TDataGridColumn('qualificacao_socio', "Qualificacao socio", 'left');
        $column_data_entrada_sociedade_transformed = new TDataGridColumn('data_entrada_sociedade', "Data entrada sociedade", 'left');

        $column_identificador_socio_transformed->setTransformer(function($value, $object, $row)
        {
            return TipoSocio::getByid($value);
        });

        $column_data_entrada_sociedade_transformed->setTransformer(function($value, $object, $row) 
        {
            return Transforme::gridDate($value, $object, $row);
        });        

        $this->datagrid->addColumn($column_cnpj_basico);
        $this->datagrid->addColumn($column_identificador_socio_transformed);
        $this->datagrid->addColumn($column_nome_socio_razao_social);
        $this->datagrid->addColumn($column_cpf_cnpj_socio);
        $this->datagrid->addColumn($column_qualificacao_socio);
        $this->datagrid->addColumn($column_data_entrada_sociedade_transformed);

        $action_group = new TDataGridActionGroup("Ações", 'fas:cog');
        $action_group->addHeader('');

        $actionSocioView = Transforme::getDataGridActionDetalharSocio();
        $action_group->addAction($actionSocioView);

        $actionEmpresa = Transforme::getDataGridActionDetalharEmpresa();
        $action_group->addAction($actionEmpresa);

        $action_onFindSocios = new TDataGridAction(array('SociosList', 'onFindSocios'));
        $action_onFindSocios->setUseButton(TRUE);
        $action_onFindSocios->setButtonClass('btn btn-default');
        $action_onFindSocios->setLabel("Buscar outros Socios");
        $action_onFindSocios->setImage('fas:users #F44336');
        $action_onFindSocios->setField(self::$primaryKey);

        $action_onFindSocios->setParameter('cnpj', '{cnpj_basico}');
        $action_group->addAction($action_onFindSocios);

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
            $container->add(TBreadCrumb::create(["Tabelas","Socios Empresas"]));
        }
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);

    }

    public function onClear($param = null) 
    {
        try 
        {
            TSession::setValue(__CLASS__.'_filter_data', NULL);
            TSession::setValue(__CLASS__.'_filters', NULL);
            $this->form->clear(true);
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public  function onFindSocios($param = null) 
    {
        try 
        {
            //$this->clearFilters();
            $data = new stdClass();
            $data->cnpj_basico = $param['cnpj'];
            $this->form->setData($data);
            $this->onSearch($param);
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
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