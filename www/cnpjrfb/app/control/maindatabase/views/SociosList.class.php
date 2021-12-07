<?php

class SociosList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'maindatabase';
    private static $activeRecord = 'Socios';
    private static $primaryKey = 'cnpj_basico';
    private static $formName = 'form_SociosList';
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
        $this->form->setFormTitle(" Sócio da Empresa");
        $this->limit = 20;

        $cnpj_basico = new TNumeric('cnpj_basico', '0', ',', '' );
        $nome_socio_razao_social = new TEntry('nome_socio_razao_social');
        $cpf_cnpj_socio = new TEntry('cpf_cnpj_socio');
        $identificador_socio = new TCombo('identificador_socio');
        $qualificacao_socio = new TDBCombo('qualificacao_socio', 'maindatabase', 'Quals', 'codigo', '{descricao}','codigo asc'  );
        $data_entrada_sociedade = new TDate('data_entrada_sociedade');
        $pais = new TDBCombo('pais', 'maindatabase', 'Pais', 'codigo', '{descricao}','codigo asc'  );
        $faixa_etaria = new TCombo('faixa_etaria');
        $representante_legal = new TEntry('representante_legal');
        $nome_do_representante = new TEntry('nome_do_representante');
        $qualificacao_representante_legal = new TDBCombo('qualificacao_representante_legal', 'maindatabase', 'Quals', 'codigo', '{descricao}','descricao asc'  );


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

        $pais->setSize('100%');
        $cnpj_basico->setSize('100%');
        $faixa_etaria->setSize('100%');
        $cpf_cnpj_socio->setSize('100%');
        $qualificacao_socio->setSize('100%');
        $identificador_socio->setSize('100%');
        $data_entrada_sociedade->setSize(110);
        $representante_legal->setSize('100%');
        $nome_do_representante->setSize('100%');
        $nome_socio_razao_social->setSize('100%');
        $qualificacao_representante_legal->setSize('100%');

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
        $column_pais = new TDataGridColumn('pais', "Pais", 'left');
        $column_representante_legal = new TDataGridColumn('representante_legal', "Representante legal", 'left');
        $column_nome_do_representante = new TDataGridColumn('nome_do_representante', "Nome do representante", 'left');
        $column_qualificacao_representante_legal = new TDataGridColumn('qualificacao_representante_legal', "Qualificacao representante legal", 'left');
        $column_faixa_etaria = new TDataGridColumn('faixa_etaria', "Faixa etaria", 'left');

        $column_identificador_socio_transformed->setTransformer(function($value, $object, $row)
        {
            try {
                return TipoSocio::getByid($value);
            }
            catch (Exception $e) {
                return $value;
            }
        });

        $column_data_entrada_sociedade_transformed->setTransformer(function($value, $object, $row) 
        {
            if(!empty(trim($value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });        

        $this->datagrid->addColumn($column_cnpj_basico);
        $this->datagrid->addColumn($column_identificador_socio_transformed);
        $this->datagrid->addColumn($column_nome_socio_razao_social);
        $this->datagrid->addColumn($column_cpf_cnpj_socio);
        $this->datagrid->addColumn($column_qualificacao_socio);
        $this->datagrid->addColumn($column_data_entrada_sociedade_transformed);
        $this->datagrid->addColumn($column_pais);
        $this->datagrid->addColumn($column_representante_legal);
        $this->datagrid->addColumn($column_nome_do_representante);
        $this->datagrid->addColumn($column_qualificacao_representante_legal);
        $this->datagrid->addColumn($column_faixa_etaria);

        $action_group = new TDataGridActionGroup("Ações", 'fas:cog');
        $action_group->addHeader('');

        $action_onView = new TDataGridAction(array('cnpjFormView', 'onView'));
        $action_onView->setUseButton(TRUE);
        $action_onView->setButtonClass('btn btn-default');
        $action_onView->setLabel("Detalhar Empresa");
        $action_onView->setImage('fas:building #7C93CF');
        $action_onView->setField(self::$primaryKey);

        $action_onView->setParameter('cnpj', '{cnpj_basico}');
        $action_group->addAction($action_onView);

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

        if (isset($data->nome_socio_razao_social) AND ( (is_scalar($data->nome_socio_razao_social) AND $data->nome_socio_razao_social !== '') OR (is_array($data->nome_socio_razao_social) AND (!empty($data->nome_socio_razao_social)) )) )
        {

            $filters[] = new TFilter('nome_socio_razao_social', 'like', "%{$data->nome_socio_razao_social}%");// create the filter 
        }

        if (isset($data->cpf_cnpj_socio) AND ( (is_scalar($data->cpf_cnpj_socio) AND $data->cpf_cnpj_socio !== '') OR (is_array($data->cpf_cnpj_socio) AND (!empty($data->cpf_cnpj_socio)) )) )
        {

            $filters[] = new TFilter('cpf_cnpj_socio', 'like', "%{$data->cpf_cnpj_socio}%");// create the filter 
        }

        if (isset($data->identificador_socio) AND ( (is_scalar($data->identificador_socio) AND $data->identificador_socio !== '') OR (is_array($data->identificador_socio) AND (!empty($data->identificador_socio)) )) )
        {

            $filters[] = new TFilter('identificador_socio', '=', $data->identificador_socio);// create the filter 
        }

        if (isset($data->qualificacao_socio) AND ( (is_scalar($data->qualificacao_socio) AND $data->qualificacao_socio !== '') OR (is_array($data->qualificacao_socio) AND (!empty($data->qualificacao_socio)) )) )
        {

            $filters[] = new TFilter('qualificacao_socio', '=', $data->qualificacao_socio);// create the filter 
        }

        if (isset($data->data_entrada_sociedade) AND ( (is_scalar($data->data_entrada_sociedade) AND $data->data_entrada_sociedade !== '') OR (is_array($data->data_entrada_sociedade) AND (!empty($data->data_entrada_sociedade)) )) )
        {

            $filters[] = new TFilter('data_entrada_sociedade', '=', $data->data_entrada_sociedade);// create the filter 
        }

        if (isset($data->pais) AND ( (is_scalar($data->pais) AND $data->pais !== '') OR (is_array($data->pais) AND (!empty($data->pais)) )) )
        {

            $filters[] = new TFilter('pais', '=', $data->pais);// create the filter 
        }

        if (isset($data->faixa_etaria) AND ( (is_scalar($data->faixa_etaria) AND $data->faixa_etaria !== '') OR (is_array($data->faixa_etaria) AND (!empty($data->faixa_etaria)) )) )
        {

            $filters[] = new TFilter('faixa_etaria', '=', $data->faixa_etaria);// create the filter 
        }

        if (isset($data->representante_legal) AND ( (is_scalar($data->representante_legal) AND $data->representante_legal !== '') OR (is_array($data->representante_legal) AND (!empty($data->representante_legal)) )) )
        {

            $filters[] = new TFilter('representante_legal', 'like', "%{$data->representante_legal}%");// create the filter 
        }

        if (isset($data->nome_do_representante) AND ( (is_scalar($data->nome_do_representante) AND $data->nome_do_representante !== '') OR (is_array($data->nome_do_representante) AND (!empty($data->nome_do_representante)) )) )
        {

            $filters[] = new TFilter('nome_do_representante', 'like', "%{$data->nome_do_representante}%");// create the filter 
        }

        if (isset($data->qualificacao_representante_legal) AND ( (is_scalar($data->qualificacao_representante_legal) AND $data->qualificacao_representante_legal !== '') OR (is_array($data->qualificacao_representante_legal) AND (!empty($data->qualificacao_representante_legal)) )) )
        {

            $filters[] = new TFilter('qualificacao_representante_legal', '=', $data->qualificacao_representante_legal);// create the filter 
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

            // creates a repository for Socios
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

    public function onFindSocios($param = null)
    {

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