<?php

class SimplesList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;

    private $filter_criteria;


    private static $primaryKey = 'cnpj_basico';
    private static $formName = 'form_SimplesList';
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
        $this->setActiveRecord('simples'); // define the Active Record
        $this->addFilterField('cnpj_basico', '=', 'cnpj_basico'); //campo, operador, campo do form
        $this->addFilterField('opcao_pelo_simples', '=', 'opcao_pelo_simples'); //campo, operador, campo do form
        $this->addFilterField('data_opcao_simples', '=', 'data_opcao_simples'); //campo, operador, campo do form
        $this->addFilterField('data_opcao_simples', '=', 'data_opcao_simples'); //campo, operador, campo do form
        $this->addFilterField('data_exclusao_simples', '=', 'data_exclusao_simples'); //campo, operador, campo do form
        $this->addFilterField('opcao_mei', '=', 'opcao_mei'); //campo, operador, campo do form
        $this->addFilterField('data_opcao_mei', '=', 'data_opcao_mei'); //campo, operador, campo do form
        $this->addFilterField('data_exclusao_mei', '=', 'data_exclusao_mei'); //campo, operador, campo do form        

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("Empresa com simples");


        $cnpj_basico = new TEntry('cnpj_basico');
        $opcao_pelo_simples = new TRadioGroup('opcao_pelo_simples');
        $data_opcao_simples = new TDate('data_opcao_simples');
        $data_exclusao_simples = new TDate('data_exclusao_simples');
        $opcao_mei = new TRadioGroup('opcao_mei');
        $data_opcao_mei = new TDate('data_opcao_mei');
        $data_exclusao_mei = new TDate('data_exclusao_mei');


        $cnpj_basico->setMaxLength(8);

        $cnpj_basico->placeholder = "NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).";

        $opcao_mei->addItems(['S'=>'Sim','N'=>'Não']);
        $opcao_pelo_simples->addItems(['S'=>'Sim','N'=>'Não']);

        $opcao_mei->setLayout('horizontal');
        $opcao_pelo_simples->setLayout('horizontal');


        $data_opcao_mei->setMask('dd/mm/yyyy');
        $data_exclusao_mei->setMask('dd/mm/yyyy');
        $data_opcao_simples->setMask('dd/mm/yyyy');
        $data_exclusao_simples->setMask('dd/mm/yyyy');

        $data_opcao_mei->setDatabaseMask('yyyy-mm-dd');
        $data_exclusao_mei->setDatabaseMask('yyyy-mm-dd');
        $data_opcao_simples->setDatabaseMask('yyyy-mm-dd');
        $data_exclusao_simples->setDatabaseMask('yyyy-mm-dd');

        $opcao_mei->setSize('100%');
        $cnpj_basico->setSize('100%');
        $data_opcao_mei->setSize(110);
        $data_exclusao_mei->setSize(110);
        $data_opcao_simples->setSize(110);
        $opcao_pelo_simples->setSize('100%');
        $data_exclusao_simples->setSize(110);

        $row1 = $this->form->addFields([new TLabel("CNPJ Básico:", null, '14px', null)],[$cnpj_basico]);
        $row2 = $this->form->addFields([new TLabel("Opção pelo Simples:", null, '14px', null)],[$opcao_pelo_simples]);
        $row3 = $this->form->addFields([new TLabel("Data opção Simples:", null, '14px', null)],[$data_opcao_simples],[new TLabel("Data exclusão simples:", null, '14px', null)],[$data_exclusao_simples]);
        $row4 = $this->form->addFields([new TLabel("Opção MEI:", null, '14px', null)],[$opcao_mei]);
        $row5 = $this->form->addFields([new TLabel("Data opção MEI:", null, '14px', null)],[$data_opcao_mei],[new TLabel("Data exclusão MEI:", null, '14px', null)],[$data_exclusao_mei]);

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
        $column_opcao_pelo_simples_transformed = new TDataGridColumn('opcao_pelo_simples', "Simples", 'left');
        $column_data_opcao_simples_transformed = new TDataGridColumn('data_opcao_simples', "Data opção simples", 'left');
        $column_data_exclusao_simples_transformed = new TDataGridColumn('data_exclusao_simples', "Data exclusão simples", 'left');
        $column_opcao_mei_transformed = new TDataGridColumn('opcao_mei', "MEI", 'left');
        $column_data_opcao_mei_transformed = new TDataGridColumn('data_opcao_mei', "Data MEI", 'left');
        $column_data_exclusao_mei_transformed = new TDataGridColumn('data_exclusao_mei', "Data exclusão MEI", 'left');

        $column_opcao_pelo_simples_transformed->setTransformer(function($value, $object, $row) 
        {
            return Transforme::simNao($value);
        });

        $column_data_opcao_simples_transformed->setTransformer(function($value, $object, $row) 
        {
            return Transforme::gridDate($value, $object, $row);
        });

        $column_data_exclusao_simples_transformed->setTransformer(function($value, $object, $row) 
        {
            return Transforme::gridDate($value, $object, $row);
        });

        $column_opcao_mei_transformed->setTransformer(function($value, $object, $row) 
        {
            return Transforme::simNao($value);
        });

        $column_data_opcao_mei_transformed->setTransformer(function($value, $object, $row) 
        {
            return Transforme::gridDate($value, $object, $row);
        });

        $column_data_exclusao_mei_transformed->setTransformer(function($value, $object, $row) 
        {
            return Transforme::gridDate($value, $object, $row);
        });

        $this->datagrid->addColumn($column_cnpj_basico);
        $this->datagrid->addColumn($column_opcao_pelo_simples_transformed);
        $this->datagrid->addColumn($column_data_opcao_simples_transformed);
        $this->datagrid->addColumn($column_data_exclusao_simples_transformed);
        $this->datagrid->addColumn($column_opcao_mei_transformed);
        $this->datagrid->addColumn($column_data_opcao_mei_transformed);
        $this->datagrid->addColumn($column_data_exclusao_mei_transformed);


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
            $container->add(TBreadCrumb::create(["Tabelas","Empresa com simples"]));
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