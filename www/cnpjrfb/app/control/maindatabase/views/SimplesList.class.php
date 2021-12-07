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

        $row1 = $this->form->addFields([new TLabel("Cnpj basico:", null, '14px', null)],[$cnpj_basico]);
        $row2 = $this->form->addFields([new TLabel("Opcao pelo simples:", null, '14px', null)],[$opcao_pelo_simples]);
        $row3 = $this->form->addFields([new TLabel("Data opcao simples:", null, '14px', null)],[$data_opcao_simples],[new TLabel("Data exclusao simples:", null, '14px', null)],[$data_exclusao_simples]);
        $row4 = $this->form->addFields([new TLabel("Opcao mei:", null, '14px', null)],[$opcao_mei]);
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

        $column_cnpj_basico = new TDataGridColumn('cnpj_basico', "Cnpj basico", 'left');
        $column_opcao_pelo_simples_transformed = new TDataGridColumn('opcao_pelo_simples', "Simples", 'left');
        $column_data_opcao_simples_transformed = new TDataGridColumn('data_opcao_simples', "Data opcao simples", 'left');
        $column_data_exclusao_simples_transformed = new TDataGridColumn('data_exclusao_simples', "Data exclusao simples", 'left');
        $column_opcao_mei_transformed = new TDataGridColumn('opcao_mei', "MEI", 'left');
        $column_data_opcao_mei_transformed = new TDataGridColumn('data_opcao_mei', "Data MEI", 'left');
        $column_data_exclusao_mei_transformed = new TDataGridColumn('data_exclusao_mei', "Data exclusao mei", 'left');

        $column_opcao_pelo_simples_transformed->setTransformer(function($value, $object, $row) 
        {
            if($value === true || $value == 't' || $value === 1 || $value == '1' || $value == 's' || $value == 'S' || $value == 'T')
                return 'Sim';

            return 'Não';

        });

        $column_data_opcao_simples_transformed->setTransformer(function($value, $object, $row) 
        {
            return Transforme::date($value, $object, $row);
        });

        $column_data_exclusao_simples_transformed->setTransformer(function($value, $object, $row) 
        {
            return Transforme::date($value, $object, $row);
        });

        $column_opcao_mei_transformed->setTransformer(function($value, $object, $row) 
        {
            if($value === true || $value == 't' || $value === 1 || $value == '1' || $value == 's' || $value == 'S' || $value == 'T')
                return 'Sim';

            return 'Não';

        });

        $column_data_opcao_mei_transformed->setTransformer(function($value, $object, $row) 
        {
            return Transforme::date($value, $object, $row);
        });

        $column_data_exclusao_mei_transformed->setTransformer(function($value, $object, $row) 
        {
            return Transforme::date($value, $object, $row);
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

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['SimplesList', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('');
        $button_limpar_filtros->setImage('fas:eraser #f44336');
        $this->datagrid_form->addField($button_limpar_filtros);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['SimplesList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:table #00b894' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['SimplesList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['SimplesList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($button_limpar_filtros);

        $head_right_actions->add($dropdown_button_exportar);

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

    public function onClearFilters($param = null) 
    {
        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
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
            // open a transaction with database 'cnpjrfb'
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