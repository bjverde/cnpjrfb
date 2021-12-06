<?php

class EstabelecimentoList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'maindatabase';
    private static $activeRecord = 'Estabelecimento';
    private static $primaryKey = 'id';
    private static $formName = 'form_EstabelecimentoList';
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
        $this->form->setFormTitle("Estabelicimento");
        $this->limit = 20;

        $cnpj_basico = new TEntry('cnpj_basico');
        $id = new TEntry('id');
        $cnpj_ordem = new TEntry('cnpj_ordem');
        $cnpj_dv = new TEntry('cnpj_dv');
        $identificador_matriz_filial = new TEntry('identificador_matriz_filial');
        $nome_fantasia = new TEntry('nome_fantasia');
        $situacao_cadastral = new TEntry('situacao_cadastral');
        $data_situacao_cadastral = new TDate('data_situacao_cadastral');
        $motivo_situacao_cadastral = new TEntry('motivo_situacao_cadastral');
        $nome_cidade_exterior = new TEntry('nome_cidade_exterior');
        $pais = new TEntry('pais');
        $data_inicio_atividade = new TDateTime('data_inicio_atividade');
        $cnae_fiscal_principal = new TEntry('cnae_fiscal_principal');
        $cnae_fiscal_secundaria = new TEntry('cnae_fiscal_secundaria');
        $tipo_logradouro = new TEntry('tipo_logradouro');
        $logradouro = new TEntry('logradouro');
        $numero = new TEntry('numero');
        $complemento = new TEntry('complemento');
        $bairro = new TEntry('bairro');
        $cep = new TEntry('cep');
        $uf = new TEntry('uf');
        $municipio = new TEntry('municipio');
        $ddd_1 = new TEntry('ddd_1');
        $telefone_1 = new TEntry('telefone_1');
        $ddd_2 = new TEntry('ddd_2');
        $telefone_2 = new TEntry('telefone_2');
        $ddd_fax = new TEntry('ddd_fax');
        $fax = new TEntry('fax');
        $correio_eletronico = new TEntry('correio_eletronico');
        $situacao_especial = new TEntry('situacao_especial');
        $data_situacao_especial = new TDate('data_situacao_especial');


        $data_situacao_especial->setMask('dd/mm/yyyy');
        $data_situacao_cadastral->setMask('dd/mm/yyyy');
        $data_inicio_atividade->setMask('dd/mm/yyyy hh:ii');

        $data_situacao_especial->setDatabaseMask('yyyy-mm-dd');
        $data_situacao_cadastral->setDatabaseMask('yyyy-mm-dd');
        $data_inicio_atividade->setDatabaseMask('yyyy-mm-dd hh:ii');

        $uf->setMaxLength(45);
        $cep->setMaxLength(45);
        $fax->setMaxLength(45);
        $ddd_2->setMaxLength(45);
        $ddd_1->setMaxLength(45);
        $cnpj_dv->setMaxLength(2);
        $numero->setMaxLength(45);
        $bairro->setMaxLength(45);
        $ddd_fax->setMaxLength(45);
        $cnpj_ordem->setMaxLength(4);
        $cnpj_basico->setMaxLength(8);
        $telefone_2->setMaxLength(45);
        $telefone_1->setMaxLength(45);
        $complemento->setMaxLength(100);
        $logradouro->setMaxLength(1000);
        $nome_fantasia->setMaxLength(1000);
        $tipo_logradouro->setMaxLength(500);
        $situacao_cadastral->setMaxLength(1);
        $situacao_especial->setMaxLength(45);
        $correio_eletronico->setMaxLength(45);
        $nome_cidade_exterior->setMaxLength(45);
        $cnae_fiscal_secundaria->setMaxLength(1000);
        $identificador_matriz_filial->setMaxLength(1);

        $id->setSize(100);
        $uf->setSize('100%');
        $fax->setSize('100%');
        $cep->setSize('100%');
        $pais->setSize('100%');
        $ddd_2->setSize('100%');
        $ddd_1->setSize('100%');
        $numero->setSize('100%');
        $bairro->setSize('100%');
        $cnpj_dv->setSize('100%');
        $ddd_fax->setSize('100%');
        $municipio->setSize('100%');
        $logradouro->setSize('100%');
        $cnpj_ordem->setSize('100%');
        $telefone_2->setSize('100%');
        $telefone_1->setSize('100%');
        $cnpj_basico->setSize('100%');
        $complemento->setSize('100%');
        $nome_fantasia->setSize('100%');
        $tipo_logradouro->setSize('100%');
        $situacao_especial->setSize('100%');
        $data_inicio_atividade->setSize(150);
        $situacao_cadastral->setSize('100%');
        $correio_eletronico->setSize('100%');
        $data_situacao_especial->setSize(110);
        $nome_cidade_exterior->setSize('100%');
        $data_situacao_cadastral->setSize(110);
        $cnae_fiscal_principal->setSize('100%');
        $cnae_fiscal_secundaria->setSize('100%');
        $motivo_situacao_cadastral->setSize('100%');
        $identificador_matriz_filial->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Cnpj basico:", null, '14px', null)],[$cnpj_basico],[new TLabel("Id:", null, '14px', null)],[$id]);
        $row2 = $this->form->addFields([new TLabel("Cnpj ordem:", null, '14px', null)],[$cnpj_ordem],[new TLabel("Cnpj dv:", null, '14px', null)],[$cnpj_dv]);
        $row3 = $this->form->addFields([new TLabel("Identificador matriz filial:", null, '14px', null)],[$identificador_matriz_filial],[new TLabel("Nome fantasia:", null, '14px', null)],[$nome_fantasia]);
        $row4 = $this->form->addFields([new TLabel("Situacao cadastral:", null, '14px', null)],[$situacao_cadastral],[new TLabel("Data situacao cadastral:", null, '14px', null)],[$data_situacao_cadastral]);
        $row5 = $this->form->addFields([new TLabel("Motivo situacao cadastral:", null, '14px', null)],[$motivo_situacao_cadastral],[new TLabel("Nome cidade exterior:", null, '14px', null)],[$nome_cidade_exterior]);
        $row6 = $this->form->addFields([new TLabel("Pais:", null, '14px', null)],[$pais],[new TLabel("Data inicio atividade:", null, '14px', null)],[$data_inicio_atividade]);
        $row7 = $this->form->addFields([new TLabel("Cnae fiscal principal:", null, '14px', null)],[$cnae_fiscal_principal],[new TLabel("Cnae fiscal secundaria:", null, '14px', null)],[$cnae_fiscal_secundaria]);
        $row8 = $this->form->addFields([new TLabel("Tipo logradouro:", null, '14px', null)],[$tipo_logradouro],[new TLabel("Logradouro:", null, '14px', null)],[$logradouro]);
        $row9 = $this->form->addFields([new TLabel("Numero:", null, '14px', null)],[$numero],[new TLabel("Complemento:", null, '14px', null)],[$complemento]);
        $row10 = $this->form->addFields([new TLabel("Bairro:", null, '14px', null)],[$bairro],[new TLabel("Cep:", null, '14px', null)],[$cep]);
        $row11 = $this->form->addFields([new TLabel("Uf:", null, '14px', null)],[$uf],[new TLabel("Municipio:", null, '14px', null)],[$municipio]);
        $row12 = $this->form->addFields([new TLabel("Ddd 1:", null, '14px', null)],[$ddd_1],[new TLabel("Telefone 1:", null, '14px', null)],[$telefone_1]);
        $row13 = $this->form->addFields([new TLabel("Ddd 2:", null, '14px', null)],[$ddd_2],[new TLabel("Telefone 2:", null, '14px', null)],[$telefone_2]);
        $row14 = $this->form->addFields([new TLabel("Ddd fax:", null, '14px', null)],[$ddd_fax],[new TLabel("Fax:", null, '14px', null)],[$fax]);
        $row15 = $this->form->addFields([new TLabel("Correio eletronico:", null, '14px', null)],[$correio_eletronico],[new TLabel("Situacao especial:", null, '14px', null)],[$situacao_especial]);
        $row16 = $this->form->addFields([new TLabel("Data situacao especial:", null, '14px', null)],[$data_situacao_especial],[],[]);

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
        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_cnpj_ordem = new TDataGridColumn('cnpj_ordem', "Cnpj ordem", 'left');
        $column_cnpj_dv = new TDataGridColumn('cnpj_dv', "Cnpj dv", 'left');
        $column_identificador_matriz_filial = new TDataGridColumn('identificador_matriz_filial', "Identificador matriz filial", 'left');
        $column_nome_fantasia = new TDataGridColumn('nome_fantasia', "Nome fantasia", 'left');
        $column_situacao_cadastral = new TDataGridColumn('situacao_cadastral', "Situacao cadastral", 'left');
        $column_data_situacao_cadastral = new TDataGridColumn('data_situacao_cadastral', "Data situacao cadastral", 'left');
        $column_motivo_situacao_cadastral = new TDataGridColumn('motivo_situacao_cadastral', "Motivo situacao cadastral", 'left');
        $column_nome_cidade_exterior = new TDataGridColumn('nome_cidade_exterior', "Nome cidade exterior", 'left');
        $column_pais = new TDataGridColumn('pais', "Pais", 'left');
        $column_data_inicio_atividade = new TDataGridColumn('data_inicio_atividade', "Data inicio atividade", 'left');
        $column_cnae_fiscal_principal = new TDataGridColumn('cnae_fiscal_principal', "Cnae fiscal principal", 'left');
        $column_cnae_fiscal_secundaria = new TDataGridColumn('cnae_fiscal_secundaria', "Cnae fiscal secundaria", 'left');
        $column_tipo_logradouro = new TDataGridColumn('tipo_logradouro', "Tipo logradouro", 'left');
        $column_logradouro = new TDataGridColumn('logradouro', "Logradouro", 'left');
        $column_numero = new TDataGridColumn('numero', "Numero", 'left');
        $column_complemento = new TDataGridColumn('complemento', "Complemento", 'left');
        $column_bairro = new TDataGridColumn('bairro', "Bairro", 'left');
        $column_cep = new TDataGridColumn('cep', "Cep", 'left');
        $column_uf = new TDataGridColumn('uf', "Uf", 'left');
        $column_municipio = new TDataGridColumn('municipio', "Municipio", 'left');
        $column_ddd_1 = new TDataGridColumn('ddd_1', "Ddd 1", 'left');
        $column_telefone_1 = new TDataGridColumn('telefone_1', "Telefone 1", 'left');
        $column_ddd_2 = new TDataGridColumn('ddd_2', "Ddd 2", 'left');
        $column_telefone_2 = new TDataGridColumn('telefone_2', "Telefone 2", 'left');
        $column_ddd_fax = new TDataGridColumn('ddd_fax', "Ddd fax", 'left');
        $column_fax = new TDataGridColumn('fax', "Fax", 'left');
        $column_correio_eletronico = new TDataGridColumn('correio_eletronico', "Correio eletronico", 'left');
        $column_situacao_especial = new TDataGridColumn('situacao_especial', "Situacao especial", 'left');
        $column_data_situacao_especial = new TDataGridColumn('data_situacao_especial', "Data situacao especial", 'left');

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_cnpj_basico);
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_cnpj_ordem);
        $this->datagrid->addColumn($column_cnpj_dv);
        $this->datagrid->addColumn($column_identificador_matriz_filial);
        $this->datagrid->addColumn($column_nome_fantasia);
        $this->datagrid->addColumn($column_situacao_cadastral);
        $this->datagrid->addColumn($column_data_situacao_cadastral);
        $this->datagrid->addColumn($column_motivo_situacao_cadastral);
        $this->datagrid->addColumn($column_nome_cidade_exterior);
        $this->datagrid->addColumn($column_pais);
        $this->datagrid->addColumn($column_data_inicio_atividade);
        $this->datagrid->addColumn($column_cnae_fiscal_principal);
        $this->datagrid->addColumn($column_cnae_fiscal_secundaria);
        $this->datagrid->addColumn($column_tipo_logradouro);
        $this->datagrid->addColumn($column_logradouro);
        $this->datagrid->addColumn($column_numero);
        $this->datagrid->addColumn($column_complemento);
        $this->datagrid->addColumn($column_bairro);
        $this->datagrid->addColumn($column_cep);
        $this->datagrid->addColumn($column_uf);
        $this->datagrid->addColumn($column_municipio);
        $this->datagrid->addColumn($column_ddd_1);
        $this->datagrid->addColumn($column_telefone_1);
        $this->datagrid->addColumn($column_ddd_2);
        $this->datagrid->addColumn($column_telefone_2);
        $this->datagrid->addColumn($column_ddd_fax);
        $this->datagrid->addColumn($column_fax);
        $this->datagrid->addColumn($column_correio_eletronico);
        $this->datagrid->addColumn($column_situacao_especial);
        $this->datagrid->addColumn($column_data_situacao_especial);

        $action_onDelete = new TDataGridAction(array('EstabelecimentoList', 'onDelete'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("Excluir");
        $action_onDelete->setImage('fas:trash-alt #dd5a43');
        $action_onDelete->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onDelete);

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
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['EstabelecimentoList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:table #00b894' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['EstabelecimentoList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['EstabelecimentoList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_right_actions->add($dropdown_button_exportar);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Tabelas","Estabelicimento"]));
        }
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);

    }

    public function onDelete($param = null) 
    { 
        if(isset($param['delete']) && $param['delete'] == 1)
        {
            try
            {
                // get the paramseter $key
                $key = $param['key'];
                // open a transaction with database
                TTransaction::open(self::$database);

                // instantiates object
                $object = new Estabelecimento($key, FALSE); 

                // deletes the object from the database
                $object->delete();

                // close the transaction
                TTransaction::close();

                // reload the listing
                $this->onReload( $param );
                // shows the success message
                new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
            }
            catch (Exception $e) // in case of exception
            {
                // shows the exception error message
                new TMessage('error', $e->getMessage());
                // undo all pending operations
                TTransaction::rollback();
            }
        }
        else
        {
            // define the delete action
            $action = new TAction(array($this, 'onDelete'));
            $action->setParameters($param); // pass the key paramseter ahead
            $action->setParameter('delete', 1);
            // shows a dialog to the user
            new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);   
        }
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

        if (isset($data->id) AND ( (is_scalar($data->id) AND $data->id !== '') OR (is_array($data->id) AND (!empty($data->id)) )) )
        {

            $filters[] = new TFilter('id', '=', $data->id);// create the filter 
        }

        if (isset($data->cnpj_ordem) AND ( (is_scalar($data->cnpj_ordem) AND $data->cnpj_ordem !== '') OR (is_array($data->cnpj_ordem) AND (!empty($data->cnpj_ordem)) )) )
        {

            $filters[] = new TFilter('cnpj_ordem', '=', $data->cnpj_ordem);// create the filter 
        }

        if (isset($data->cnpj_dv) AND ( (is_scalar($data->cnpj_dv) AND $data->cnpj_dv !== '') OR (is_array($data->cnpj_dv) AND (!empty($data->cnpj_dv)) )) )
        {

            $filters[] = new TFilter('cnpj_dv', '=', $data->cnpj_dv);// create the filter 
        }

        if (isset($data->identificador_matriz_filial) AND ( (is_scalar($data->identificador_matriz_filial) AND $data->identificador_matriz_filial !== '') OR (is_array($data->identificador_matriz_filial) AND (!empty($data->identificador_matriz_filial)) )) )
        {

            $filters[] = new TFilter('identificador_matriz_filial', '=', $data->identificador_matriz_filial);// create the filter 
        }

        if (isset($data->nome_fantasia) AND ( (is_scalar($data->nome_fantasia) AND $data->nome_fantasia !== '') OR (is_array($data->nome_fantasia) AND (!empty($data->nome_fantasia)) )) )
        {

            $filters[] = new TFilter('nome_fantasia', 'like', "%{$data->nome_fantasia}%");// create the filter 
        }

        if (isset($data->situacao_cadastral) AND ( (is_scalar($data->situacao_cadastral) AND $data->situacao_cadastral !== '') OR (is_array($data->situacao_cadastral) AND (!empty($data->situacao_cadastral)) )) )
        {

            $filters[] = new TFilter('situacao_cadastral', '=', $data->situacao_cadastral);// create the filter 
        }

        if (isset($data->data_situacao_cadastral) AND ( (is_scalar($data->data_situacao_cadastral) AND $data->data_situacao_cadastral !== '') OR (is_array($data->data_situacao_cadastral) AND (!empty($data->data_situacao_cadastral)) )) )
        {

            $filters[] = new TFilter('data_situacao_cadastral', '=', $data->data_situacao_cadastral);// create the filter 
        }

        if (isset($data->motivo_situacao_cadastral) AND ( (is_scalar($data->motivo_situacao_cadastral) AND $data->motivo_situacao_cadastral !== '') OR (is_array($data->motivo_situacao_cadastral) AND (!empty($data->motivo_situacao_cadastral)) )) )
        {

            $filters[] = new TFilter('motivo_situacao_cadastral', '=', $data->motivo_situacao_cadastral);// create the filter 
        }

        if (isset($data->nome_cidade_exterior) AND ( (is_scalar($data->nome_cidade_exterior) AND $data->nome_cidade_exterior !== '') OR (is_array($data->nome_cidade_exterior) AND (!empty($data->nome_cidade_exterior)) )) )
        {

            $filters[] = new TFilter('nome_cidade_exterior', 'like', "%{$data->nome_cidade_exterior}%");// create the filter 
        }

        if (isset($data->pais) AND ( (is_scalar($data->pais) AND $data->pais !== '') OR (is_array($data->pais) AND (!empty($data->pais)) )) )
        {

            $filters[] = new TFilter('pais', '=', $data->pais);// create the filter 
        }

        if (isset($data->data_inicio_atividade) AND ( (is_scalar($data->data_inicio_atividade) AND $data->data_inicio_atividade !== '') OR (is_array($data->data_inicio_atividade) AND (!empty($data->data_inicio_atividade)) )) )
        {

            $filters[] = new TFilter('data_inicio_atividade', '=', $data->data_inicio_atividade);// create the filter 
        }

        if (isset($data->cnae_fiscal_principal) AND ( (is_scalar($data->cnae_fiscal_principal) AND $data->cnae_fiscal_principal !== '') OR (is_array($data->cnae_fiscal_principal) AND (!empty($data->cnae_fiscal_principal)) )) )
        {

            $filters[] = new TFilter('cnae_fiscal_principal', '=', $data->cnae_fiscal_principal);// create the filter 
        }

        if (isset($data->cnae_fiscal_secundaria) AND ( (is_scalar($data->cnae_fiscal_secundaria) AND $data->cnae_fiscal_secundaria !== '') OR (is_array($data->cnae_fiscal_secundaria) AND (!empty($data->cnae_fiscal_secundaria)) )) )
        {

            $filters[] = new TFilter('cnae_fiscal_secundaria', 'like', "%{$data->cnae_fiscal_secundaria}%");// create the filter 
        }

        if (isset($data->tipo_logradouro) AND ( (is_scalar($data->tipo_logradouro) AND $data->tipo_logradouro !== '') OR (is_array($data->tipo_logradouro) AND (!empty($data->tipo_logradouro)) )) )
        {

            $filters[] = new TFilter('tipo_logradouro', 'like', "%{$data->tipo_logradouro}%");// create the filter 
        }

        if (isset($data->logradouro) AND ( (is_scalar($data->logradouro) AND $data->logradouro !== '') OR (is_array($data->logradouro) AND (!empty($data->logradouro)) )) )
        {

            $filters[] = new TFilter('logradouro', 'like', "%{$data->logradouro}%");// create the filter 
        }

        if (isset($data->numero) AND ( (is_scalar($data->numero) AND $data->numero !== '') OR (is_array($data->numero) AND (!empty($data->numero)) )) )
        {

            $filters[] = new TFilter('numero', 'like', "%{$data->numero}%");// create the filter 
        }

        if (isset($data->complemento) AND ( (is_scalar($data->complemento) AND $data->complemento !== '') OR (is_array($data->complemento) AND (!empty($data->complemento)) )) )
        {

            $filters[] = new TFilter('complemento', 'like', "%{$data->complemento}%");// create the filter 
        }

        if (isset($data->bairro) AND ( (is_scalar($data->bairro) AND $data->bairro !== '') OR (is_array($data->bairro) AND (!empty($data->bairro)) )) )
        {

            $filters[] = new TFilter('bairro', 'like', "%{$data->bairro}%");// create the filter 
        }

        if (isset($data->cep) AND ( (is_scalar($data->cep) AND $data->cep !== '') OR (is_array($data->cep) AND (!empty($data->cep)) )) )
        {

            $filters[] = new TFilter('cep', 'like', "%{$data->cep}%");// create the filter 
        }

        if (isset($data->uf) AND ( (is_scalar($data->uf) AND $data->uf !== '') OR (is_array($data->uf) AND (!empty($data->uf)) )) )
        {

            $filters[] = new TFilter('uf', 'like', "%{$data->uf}%");// create the filter 
        }

        if (isset($data->municipio) AND ( (is_scalar($data->municipio) AND $data->municipio !== '') OR (is_array($data->municipio) AND (!empty($data->municipio)) )) )
        {

            $filters[] = new TFilter('municipio', '=', $data->municipio);// create the filter 
        }

        if (isset($data->ddd_1) AND ( (is_scalar($data->ddd_1) AND $data->ddd_1 !== '') OR (is_array($data->ddd_1) AND (!empty($data->ddd_1)) )) )
        {

            $filters[] = new TFilter('ddd_1', 'like', "%{$data->ddd_1}%");// create the filter 
        }

        if (isset($data->telefone_1) AND ( (is_scalar($data->telefone_1) AND $data->telefone_1 !== '') OR (is_array($data->telefone_1) AND (!empty($data->telefone_1)) )) )
        {

            $filters[] = new TFilter('telefone_1', 'like', "%{$data->telefone_1}%");// create the filter 
        }

        if (isset($data->ddd_2) AND ( (is_scalar($data->ddd_2) AND $data->ddd_2 !== '') OR (is_array($data->ddd_2) AND (!empty($data->ddd_2)) )) )
        {

            $filters[] = new TFilter('ddd_2', 'like', "%{$data->ddd_2}%");// create the filter 
        }

        if (isset($data->telefone_2) AND ( (is_scalar($data->telefone_2) AND $data->telefone_2 !== '') OR (is_array($data->telefone_2) AND (!empty($data->telefone_2)) )) )
        {

            $filters[] = new TFilter('telefone_2', 'like', "%{$data->telefone_2}%");// create the filter 
        }

        if (isset($data->ddd_fax) AND ( (is_scalar($data->ddd_fax) AND $data->ddd_fax !== '') OR (is_array($data->ddd_fax) AND (!empty($data->ddd_fax)) )) )
        {

            $filters[] = new TFilter('ddd_fax', 'like', "%{$data->ddd_fax}%");// create the filter 
        }

        if (isset($data->fax) AND ( (is_scalar($data->fax) AND $data->fax !== '') OR (is_array($data->fax) AND (!empty($data->fax)) )) )
        {

            $filters[] = new TFilter('fax', 'like', "%{$data->fax}%");// create the filter 
        }

        if (isset($data->correio_eletronico) AND ( (is_scalar($data->correio_eletronico) AND $data->correio_eletronico !== '') OR (is_array($data->correio_eletronico) AND (!empty($data->correio_eletronico)) )) )
        {

            $filters[] = new TFilter('correio_eletronico', 'like', "%{$data->correio_eletronico}%");// create the filter 
        }

        if (isset($data->situacao_especial) AND ( (is_scalar($data->situacao_especial) AND $data->situacao_especial !== '') OR (is_array($data->situacao_especial) AND (!empty($data->situacao_especial)) )) )
        {

            $filters[] = new TFilter('situacao_especial', 'like', "%{$data->situacao_especial}%");// create the filter 
        }

        if (isset($data->data_situacao_especial) AND ( (is_scalar($data->data_situacao_especial) AND $data->data_situacao_especial !== '') OR (is_array($data->data_situacao_especial) AND (!empty($data->data_situacao_especial)) )) )
        {

            $filters[] = new TFilter('data_situacao_especial', '=', $data->data_situacao_especial);// create the filter 
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

            // creates a repository for Estabelecimento
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'id';    
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
                    $row->id = "row_{$object->id}";

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