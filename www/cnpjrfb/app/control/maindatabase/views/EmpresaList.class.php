<?php

class EmpresaList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'maindatabase';
    private static $activeRecord = 'Empresa';
    private static $primaryKey = 'cnpj_basico';
    private static $formName = 'form_EmpresaList';
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
        $this->form->setFormTitle("Empresa");
        $this->limit = 20;

        $cnpj_basico = new TNumeric('cnpj_basico', '0', ',', '' );
        $razao_social = new TEntry('razao_social');
        $natureza_juridica = new TDBCombo('natureza_juridica', 'maindatabase', 'Natju', 'codigo', '{descricao}','descricao asc'  );
        $qualificacao_responsavel = new TDBCombo('qualificacao_responsavel', 'maindatabase', 'Quals', 'codigo', '{descricao}','descricao asc'  );
        $capital_social = new TNumeric('capital_social', '2', ',', '.' );
        $porte_empresa = new TCombo('porte_empresa');
        $ente_federativo_responsavel = new TEntry('ente_federativo_responsavel');


        $cnpj_basico->placeholder = "NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS  DO CNPJ).";

        $natureza_juridica->enableSearch();
        $qualificacao_responsavel->enableSearch();

        $cnpj_basico->setMaxLength(8);
        $razao_social->setMaxLength(1000);
        $ente_federativo_responsavel->setMaxLength(45);

        $cnpj_basico->setSize('100%');
        $razao_social->setSize('100%');
        $porte_empresa->setSize('100%');
        $capital_social->setSize('100%');
        $natureza_juridica->setSize('100%');
        $qualificacao_responsavel->setSize('100%');
        $ente_federativo_responsavel->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("CNPJ Básico:", null, '14px', null)],[$cnpj_basico]);
        $row2 = $this->form->addFields([new TLabel("Razao social:", null, '14px', null)],[$razao_social],[new TLabel("Natureza juridica:", null, '14px', null)],[$natureza_juridica]);
        $row3 = $this->form->addFields([new TLabel("Qualificacao responsavel:", null, '14px', null)],[$qualificacao_responsavel],[new TLabel("Capital social:", null, '14px', null)],[$capital_social]);
        $row4 = $this->form->addFields([new TLabel("Porte empresa:", null, '14px', null)],[$porte_empresa],[new TLabel("Ente federativo responsavel:", null, '14px', null)],[$ente_federativo_responsavel]);

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

        $column_cnpj_basico = new TDataGridColumn('cnpj_basico', "CNPJ Básico:", 'left');
        $column_razao_social = new TDataGridColumn('razao_social', "Razao social", 'left');
        $column_natureza_juridica = new TDataGridColumn('fk_natureza_juridica->descricao', "Natureza juridica", 'left');
        $column_qualificacao_responsavel = new TDataGridColumn('fk_qualificacao_responsavel->descricao', "Qualificacao responsavel", 'left');
        $column_capital_social_transformed = new TDataGridColumn('capital_social', "Capital social", 'left');
        $column_porte_empresa = new TDataGridColumn('porte_empresa', "Porte empresa", 'left');
        $column_ente_federativo_responsavel = new TDataGridColumn('ente_federativo_responsavel', "Ente federativo responsavel", 'left');

        $column_capital_social_transformed->setTransformer(function($value, $object, $row) 
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });        

        $this->datagrid->addColumn($column_cnpj_basico);
        $this->datagrid->addColumn($column_razao_social);
        $this->datagrid->addColumn($column_natureza_juridica);
        $this->datagrid->addColumn($column_qualificacao_responsavel);
        $this->datagrid->addColumn($column_capital_social_transformed);
        $this->datagrid->addColumn($column_porte_empresa);
        $this->datagrid->addColumn($column_ente_federativo_responsavel);


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

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';
        $headerActions->style = 'background-color:#fff; justify-content: space-between;';

        $head_left_actions = new TElement('div');
        $head_left_actions->class = ' datagrid-header-actions-left-actions ';

        $head_right_actions = new TElement('div');
        $head_right_actions->class = ' datagrid-header-actions-left-actions ';

        $headerActions->add($head_left_actions);
        $headerActions->add($head_right_actions);

        $panel->getBody()->insert(0, $headerActions);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['EmpresaList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:table #00b894' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['EmpresaList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['EmpresaList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_right_actions->add($dropdown_button_exportar);

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

    public function onExportCsv($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.csv';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects)
                {
                    $handler = fopen($output, 'w');
                    TTransaction::open(self::$database);

                    foreach ($objects as $object)
                    {
                        $row = [];
                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();

                            if (isset($object->$column_name))
                            {
                                $row[] = is_scalar($object->$column_name) ? $object->$column_name : '';
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $column_name = (strpos($column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                $row[] = $object->render($column_name);
                            }
                        }

                        fputcsv($handler, $row);
                    }

                    fclose($handler);
                    TTransaction::close();
                }
                else
                {
                    throw new Exception(_t('No records found'));
                }

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportPdf($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.pdf';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $this->datagrid->prepareForPrinting();
                $this->onReload();

                $html = clone $this->datagrid;
                $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();

                $dompdf = new \Dompdf\Dompdf;
                $dompdf->loadHtml($contents);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                file_put_contents($output, $dompdf->output());

                $window = TWindow::create('PDF', 0.8, 0.8);
                $object = new TElement('object');
                $object->data  = $output;
                $object->type  = 'application/pdf';
                $object->style = "width: 100%; height:calc(100% - 10px)";

                $window->add($object);
                $window->show();
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportXml($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.xml';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects)
                {
                    TTransaction::open(self::$database);

                    $dom = new DOMDocument('1.0', 'UTF-8');
                    $dom->{'formatOutput'} = true;
                    $dataset = $dom->appendChild( $dom->createElement('dataset') );

                    foreach ($objects as $object)
                    {
                        $row = $dataset->appendChild( $dom->createElement( self::$activeRecord ) );

                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();
                            $column_name_raw = str_replace(['(','{','->', '-','>','}',')', ' '], ['','','_','','','','','_'], $column_name);

                            if (isset($object->$column_name))
                            {
                                $value = is_scalar($object->$column_name) ? $object->$column_name : '';
                                $row->appendChild($dom->createElement($column_name_raw, $value)); 
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $column_name = (strpos($column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                $value = $object->render($column_name);
                                $row->appendChild($dom->createElement($column_name_raw, $value));
                            }
                        }
                    }

                    $dom->save($output);

                    TTransaction::close();
                }
                else
                {
                    throw new Exception(_t('No records found'));
                }

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
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

        if (isset($data->razao_social) AND ( (is_scalar($data->razao_social) AND $data->razao_social !== '') OR (is_array($data->razao_social) AND (!empty($data->razao_social)) )) )
        {

            $filters[] = new TFilter('razao_social', 'like', "%{$data->razao_social}%");// create the filter 
        }

        if (isset($data->natureza_juridica) AND ( (is_scalar($data->natureza_juridica) AND $data->natureza_juridica !== '') OR (is_array($data->natureza_juridica) AND (!empty($data->natureza_juridica)) )) )
        {

            $filters[] = new TFilter('natureza_juridica', '=', $data->natureza_juridica);// create the filter 
        }

        if (isset($data->qualificacao_responsavel) AND ( (is_scalar($data->qualificacao_responsavel) AND $data->qualificacao_responsavel !== '') OR (is_array($data->qualificacao_responsavel) AND (!empty($data->qualificacao_responsavel)) )) )
        {

            $filters[] = new TFilter('qualificacao_responsavel', '=', $data->qualificacao_responsavel);// create the filter 
        }

        if (isset($data->capital_social) AND ( (is_scalar($data->capital_social) AND $data->capital_social !== '') OR (is_array($data->capital_social) AND (!empty($data->capital_social)) )) )
        {

            $filters[] = new TFilter('capital_social', 'like', "%{$data->capital_social}%");// create the filter 
        }

        if (isset($data->porte_empresa) AND ( (is_scalar($data->porte_empresa) AND $data->porte_empresa !== '') OR (is_array($data->porte_empresa) AND (!empty($data->porte_empresa)) )) )
        {

            $filters[] = new TFilter('porte_empresa', 'like', "%{$data->porte_empresa}%");// create the filter 
        }

        if (isset($data->ente_federativo_responsavel) AND ( (is_scalar($data->ente_federativo_responsavel) AND $data->ente_federativo_responsavel !== '') OR (is_array($data->ente_federativo_responsavel) AND (!empty($data->ente_federativo_responsavel)) )) )
        {

            $filters[] = new TFilter('ente_federativo_responsavel', 'like', "%{$data->ente_federativo_responsavel}%");// create the filter 
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
            // open a transaction with database 'cnpjrfb'
            TTransaction::open(self::$database);

            // creates a repository for Empresa
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