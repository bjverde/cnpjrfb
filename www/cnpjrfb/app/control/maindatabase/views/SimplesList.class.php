<?php

class SimplesList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'maindatabase';
    private static $activeRecord = 'Simples';
    private static $primaryKey = 'cnpj_basico';
    private static $formName = 'form_SimplesList';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];
    private $limit = 20;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param = null)
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("Empresa com simples");
        $this->limit = 20;

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
        $row2 = $this->form->addFields([new TLabel("Opcao pelo simples:", null, '14px', null)],[$opcao_pelo_simples]);
        $row3 = $this->form->addFields([new TLabel("Data opcao simples:", null, '14px', null)],[$data_opcao_simples],[new TLabel("Data exclusao simples:", null, '14px', null)],[$data_exclusao_simples]);
        $row4 = $this->form->addFields([new TLabel("Opção MEI:", null, '14px', null)],[$opcao_mei]);
        $row5 = $this->form->addFields([new TLabel("Data opcao mei:", null, '14px', null)],[$data_opcao_mei],[new TLabel("Data exclusao mei:", null, '14px', null)],[$data_exclusao_mei]);

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

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
        $column_data_opcao_simples_transformed = new TDataGridColumn('data_opcao_simples', "Data opcao simples", 'left');
        $column_data_exclusao_simples_transformed = new TDataGridColumn('data_exclusao_simples', "Data exclusao simples", 'left');
        $column_opcao_mei_transformed = new TDataGridColumn('opcao_mei', "MEI", 'left');
        $column_data_opcao_mei_transformed = new TDataGridColumn('data_opcao_mei', "Data MEI", 'left');
        $column_data_exclusao_mei_transformed = new TDataGridColumn('data_exclusao_mei', "Data exclusao mei", 'left');

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
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->cnpj_basico) AND ( (is_scalar($data->cnpj_basico) AND $data->cnpj_basico !== '') OR (is_array($data->cnpj_basico) AND (!empty($data->cnpj_basico)) )) )
        {

            $filters[] = new TFilter('cnpj_basico', '=', $data->cnpj_basico);// create the filter 
        }

        if (isset($data->opcao_pelo_simples) AND ( (is_scalar($data->opcao_pelo_simples) AND $data->opcao_pelo_simples !== '') OR (is_array($data->opcao_pelo_simples) AND (!empty($data->opcao_pelo_simples)) )) )
        {

            $filters[] = new TFilter('opcao_pelo_simples', '=', $data->opcao_pelo_simples);// create the filter 
        }

        if (isset($data->data_opcao_simples) AND ( (is_scalar($data->data_opcao_simples) AND $data->data_opcao_simples !== '') OR (is_array($data->data_opcao_simples) AND (!empty($data->data_opcao_simples)) )) )
        {

            $filters[] = new TFilter('data_opcao_simples', '=', $data->data_opcao_simples);// create the filter 
        }

        if (isset($data->data_exclusao_simples) AND ( (is_scalar($data->data_exclusao_simples) AND $data->data_exclusao_simples !== '') OR (is_array($data->data_exclusao_simples) AND (!empty($data->data_exclusao_simples)) )) )
        {

            $filters[] = new TFilter('data_exclusao_simples', '=', $data->data_exclusao_simples);// create the filter 
        }

        if (isset($data->opcao_mei) AND ( (is_scalar($data->opcao_mei) AND $data->opcao_mei !== '') OR (is_array($data->opcao_mei) AND (!empty($data->opcao_mei)) )) )
        {

            $filters[] = new TFilter('opcao_mei', '=', $data->opcao_mei);// create the filter 
        }

        if (isset($data->data_opcao_mei) AND ( (is_scalar($data->data_opcao_mei) AND $data->data_opcao_mei !== '') OR (is_array($data->data_opcao_mei) AND (!empty($data->data_opcao_mei)) )) )
        {

            $filters[] = new TFilter('data_opcao_mei', '=', $data->data_opcao_mei);// create the filter 
        }

        if (isset($data->data_exclusao_mei) AND ( (is_scalar($data->data_exclusao_mei) AND $data->data_exclusao_mei !== '') OR (is_array($data->data_exclusao_mei) AND (!empty($data->data_exclusao_mei)) )) )
        {

            $filters[] = new TFilter('data_exclusao_mei', '=', $data->data_exclusao_mei);// create the filter 
        }

        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'maindatabase'
            TTransaction::open(self::$database);

            // creates a repository for Simples
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'cnpj_basico';    
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $this->limit);

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->cnpj_basico}";

                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($this->limit); // limit

            // close the transaction
            TTransaction::close();
            $this->loaded = true;

            return $objects;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    public function onShow($param = null)
    {

    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  $this->showMethods))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
}