<?php

class EstabelecimentoList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    
    private $filter_criteria;
    
    
    private static $primaryKey = 'cnpj_basico';
    private static $formName = 'form_EstabelecimentoList';
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
        $this->setActiveRecord('estabelecimento'); // define the Active Record
        $this->addFilterField('cnpj_basico', '=', 'cnpj_basico'); //campo, operador, campo do form
        $this->addFilterField('cnpj_ordem', '=', 'cnpj_ordem'); //campo, operador, campo do form
        $this->addFilterField('cnpj_dv', '=', 'cnpj_dv'); //campo, operador, campo do form
        $this->addFilterField('identificador_matriz_filial', '=', 'identificador_matriz_filial'); //campo, operador, campo do form
        $this->addFilterField('nome_fantasia', 'like', 'nome_fantasia'); //campo, operador, campo do form
        $this->addFilterField('situacao_cadastral', '=', 'situacao_cadastral'); //campo, operador, campo do form
        $this->addFilterField('data_situacao_cadastral', '=', 'data_situacao_cadastral'); //campo, operador, campo do form
        $this->addFilterField('motivo_situacao_cadastral', '=', 'motivo_situacao_cadastral'); //campo, operador, campo do form
        $this->addFilterField('nome_cidade_exterior', '=', 'nome_cidade_exterior'); //campo, operador, campo do form
        $this->addFilterField('pais', '=', 'pais'); //campo, operador, campo do form
        $this->addFilterField('data_inicio_atividade', '=', 'data_inicio_atividade'); //campo, operador, campo do form
        $this->addFilterField('cnae_fiscal_principal', 'like', 'cnae_fiscal_principal'); //campo, operador, campo do form
        $this->addFilterField('cnae_fiscal_secundaria', 'like', 'cnae_fiscal_secundaria'); //campo, operador, campo do form
        $this->addFilterField('tipo_logradouro', 'like', 'tipo_logradouro'); //campo, operador, campo do form
        $this->addFilterField('logradouro', 'like', 'logradouro'); //campo, operador, campo do form
        $this->addFilterField('numero', 'like', 'numero'); //campo, operador, campo do form
        $this->addFilterField('complemento', 'like', 'complemento'); //campo, operador, campo do form
        $this->addFilterField('bairro', 'like', 'bairro'); //campo, operador, campo do form
        $this->addFilterField('cep', 'like', 'cep'); //campo, operador, campo do form
        $this->addFilterField('uf', '=', 'uf'); //campo, operador, campo do form
        $this->addFilterField('bairro', 'like', 'bairro'); //campo, operador, campo do form
        $this->addFilterField('municipio', '=', 'municipio'); //campo, operador, campo do form
        $this->addFilterField('bairro', 'like', 'bairro'); //campo, operador, campo do form
        $this->addFilterField('ddd_1', 'like', 'ddd_1'); //campo, operador, campo do form
        $this->addFilterField('telefone_1', 'like', 'telefone_1'); //campo, operador, campo do form
        $this->addFilterField('ddd_2', 'like', 'ddd_2'); //campo, operador, campo do form
        $this->addFilterField('telefone_2', 'like', 'telefone_2'); //campo, operador, campo do form
        $this->addFilterField('ddd_fax', 'like', 'ddd_fax'); //campo, operador, campo do form
        $this->addFilterField('fax', 'like', 'fax'); //campo, operador, campo do form
        $this->addFilterField('correio_eletronico', 'like', 'correio_eletronico'); //campo, operador, campo do form
        $this->addFilterField('situacao_especial', 'like', 'situacao_especial'); //campo, operador, campo do form
        $this->addFilterField('data_situacao_especial', 'like', 'data_situacao_especial'); //campo, operador, campo do form        

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("Estabelicimento");


        $cnpj_basico = new TNumeric('cnpj_basico', '0', ',', '' );
        $cnpj_ordem = new TNumeric('cnpj_ordem', '0', ',', '' );
        $cnpj_dv = new TNumeric('cnpj_dv', '0', ',', '' );
        $identificador_matriz_filial = new TCombo('identificador_matriz_filial');
        $nome_fantasia = new TEntry('nome_fantasia');
        $situacao_cadastral = new TCombo('situacao_cadastral');
        $data_situacao_cadastral = new TDate('data_situacao_cadastral');
        $motivo_situacao_cadastral = new TDBCombo('motivo_situacao_cadastral', 'maindatabase', 'moti', 'codigo', '{descricao}','descricao asc'  );
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
        $municipio = new TDBCombo('municipio', 'maindatabase', 'munic', 'codigo', '{descricao}','descricao asc'  );
        $ddd_1 = new TEntry('ddd_1');
        $telefone_1 = new TEntry('telefone_1');
        $ddd_2 = new TEntry('ddd_2');
        $telefone_2 = new TEntry('telefone_2');
        $ddd_fax = new TEntry('ddd_fax');
        $fax = new TEntry('fax');
        $correio_eletronico = new TEntry('correio_eletronico');
        $situacao_especial = new TEntry('situacao_especial');
        $data_situacao_especial = new TDate('data_situacao_especial');


        $municipio->enableSearch();
        $motivo_situacao_cadastral->enableSearch();
        $identificador_matriz_filial->addItems(TipoMatrizFilial::getList());
        $situacao_cadastral->addItems(TipoEmpresaSituacao::getList());

        $data_situacao_especial->setMask('dd/mm/yyyy');
        $data_situacao_cadastral->setMask('dd/mm/yyyy');
        $data_inicio_atividade->setMask('dd/mm/yyyy hh:ii');

        $data_situacao_especial->setDatabaseMask('yyyy-mm-dd');
        $data_situacao_cadastral->setDatabaseMask('yyyy-mm-dd');
        $data_inicio_atividade->setDatabaseMask('yyyy-mm-dd hh:ii');

        $cnpj_basico->placeholder = "NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).";
        $cnpj_dv->placeholder = "DÍGITO VERIFICADOR DO NÚMERO DE INSCRIÇÃO NO CNPJ (DOIS  ÚLTIMOS DÍGITOS DO CNPJ).";
        $cnpj_ordem->placeholder = "NÚMERO DO ESTABELECIMENTO DE INSCRIÇÃO NO CNPJ (DO  NONO ATÉ O DÉCIMO SEGUNDO DÍGITO DO CNPJ)";

        $uf->setMaxLength(45);
        $fax->setMaxLength(45);
        $cep->setMaxLength(45);
        $ddd_2->setMaxLength(45);
        $ddd_1->setMaxLength(45);
        $bairro->setMaxLength(45);
        $cnpj_dv->setMaxLength(2);
        $numero->setMaxLength(45);
        $ddd_fax->setMaxLength(45);
        $cnpj_ordem->setMaxLength(4);
        $telefone_2->setMaxLength(45);
        $telefone_1->setMaxLength(45);
        $cnpj_basico->setMaxLength(8);
        $complemento->setMaxLength(100);
        $logradouro->setMaxLength(1000);
        $nome_fantasia->setMaxLength(1000);
        $tipo_logradouro->setMaxLength(500);
        $situacao_especial->setMaxLength(45);
        $correio_eletronico->setMaxLength(45);
        $nome_cidade_exterior->setMaxLength(45);
        $cnae_fiscal_secundaria->setMaxLength(1000);

        $row1 = $this->form->addFields([new TLabel("CNPJ Básico:", null, '14px', null),$cnpj_basico]
                                      ,[new TLabel("CNPJ ordem:", null, '14px', null),$cnpj_ordem]
                                      ,[new TLabel("CNPJ DV:", null, '14px', null),$cnpj_dv]
                                      );
        $row1->layout = [' col-md-4',' col-md-4',' col-md-4'];

        $row2 = $this->form->addFields([new TLabel("Matriz/Filial:", null, '14px', null)],[$identificador_matriz_filial],[new TLabel("Nome fantasia:", null, '14px', null)],[$nome_fantasia]);
        $row3 = $this->form->addFields([new TLabel("Situação cadastral:", null, '14px', null)],[$situacao_cadastral],[new TLabel("Data situacao cadastral:", null, '14px', null)],[$data_situacao_cadastral]);
        $row4 = $this->form->addFields([new TLabel("Motivo situação cadastral:", null, '14px', null)],[$motivo_situacao_cadastral],[new TLabel("Nome cidade exterior:", null, '14px', null)],[$nome_cidade_exterior]);
        $row5 = $this->form->addFields([new TLabel("Pais:", null, '14px', null)],[$pais],[new TLabel("Data inicio atividade:", null, '14px', null)],[$data_inicio_atividade]);
        $row6 = $this->form->addFields([new TLabel("Cnae fiscal principal:", null, '14px', null)],[$cnae_fiscal_principal],[new TLabel("Cnae fiscal secundaria:", null, '14px', null)],[$cnae_fiscal_secundaria]);
        $row7 = $this->form->addContent([new TFormSeparator("Endereço", '#333', '18', '#eee')]);
        $row8 = $this->form->addFields([new TLabel("Tipo logradouro:", null, '14px', null)],[$tipo_logradouro],[new TLabel("Logradouro:", null, '14px', null)],[$logradouro]);
        $row9 = $this->form->addFields([new TLabel("Número:", null, '14px', null)],[$numero],[new TLabel("Complemento:", null, '14px', null)],[$complemento]);
        $row10 = $this->form->addFields([new TLabel("Bairro:", null, '14px', null)],[$bairro],[new TLabel("Cep:", null, '14px', null)],[$cep]);
        $row11 = $this->form->addFields([new TLabel("Uf:", null, '14px', null)],[$uf],[new TLabel("Municipio:", null, '14px', null)],[$municipio]);
        $row12 = $this->form->addFields([new TLabel("Ddd 1:", null, '14px', null)],[$ddd_1],[new TLabel("Telefone 1:", null, '14px', null)],[$telefone_1]);
        $row13 = $this->form->addFields([new TLabel("Ddd 2:", null, '14px', null)],[$ddd_2],[new TLabel("Telefone 2:", null, '14px', null)],[$telefone_2]);
        $row14 = $this->form->addFields([new TLabel("Ddd fax:", null, '14px', null)],[$ddd_fax],[new TLabel("Fax:", null, '14px', null)],[$fax]);
        $row15 = $this->form->addFields([new TLabel("Correio eletronico:", null, '14px', null)],[$correio_eletronico],[new TLabel("Situacao especial:", null, '14px', null)],[$situacao_especial]);
        $row16 = $this->form->addFields([new TLabel("Data situação especial:", null, '14px', null)],[$data_situacao_especial],[],[]);

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

        $column_cnpj_basico = new TDataGridColumn('{cnpj_basico}{cnpj_ordem}{cnpj_dv}', "CNPJ", 'left');
        $column_cnpj_basico->setTransformer(function($value, $object, $row) 
        {
            return StringHelper::formatCnpjCpf($value);
        });        
        //$column_cnpj_basico = new TDataGridColumn('cnpj_basico', "CNPJ Básico", 'left');
        //$column_cnpj_ordem = new TDataGridColumn('cnpj_ordem', "CNPJ Ordem", 'left');
        //$column_cnpj_dv = new TDataGridColumn('cnpj_dv', "CNPJ Dv", 'left');
        $column_identificador_matriz_filial = new TDataGridColumn('identificador_matriz_filial', "Matriz / Filial", 'left');
        $column_nome_fantasia = new TDataGridColumn('nome_fantasia', "Nome fantasia", 'left');
        $column_situacao_cadastral = new TDataGridColumn('situacao_cadastral', "Situação cadastral", 'left');
        $column_data_situacao_cadastral_transformed = new TDataGridColumn('data_situacao_cadastral', "Data situação cadastral", 'left');
        $column_data_inicio_atividade_transformed = new TDataGridColumn('data_inicio_atividade', "Data inicio atividade", 'left');
        $column_cnae_fiscal_principal = new TDataGridColumn('cnae_fiscal_principal', "CNAE principal", 'left');
        $column_uf = new TDataGridColumn('uf', "Uf", 'left');
        $column_municipio = new TDataGridColumn('fk_municipio->descricao', "Municipio", 'left');
        $column_situacao_especial = new TDataGridColumn('situacao_especial', "Situacao especial", 'left');
        $column_data_situacao_especial_transformed = new TDataGridColumn('data_situacao_especial', "Data situação especial", 'left');

        $column_identificador_matriz_filial->setTransformer(function($value, $object, $row) 
        {
            return TipoMatrizFilial::getByid($value);
        });

        $column_data_situacao_cadastral_transformed->setTransformer(function($value, $object, $row) 
        {
            return Transforme::gridDate($value, $object, $row);
        });

        $column_data_inicio_atividade_transformed->setTransformer(function($value, $object, $row) 
        {
            return Transforme::gridDate($value, $object, $row);
        });

        $column_data_situacao_especial_transformed->setTransformer(function($value, $object, $row) 
        {
            return Transforme::gridDate($value, $object, $row);
        });        

        $order_cnpj_basico = new TAction(array($this, 'onReload'));
        $order_cnpj_basico->setParameter('order', 'cnpj_basico');
        $column_cnpj_basico->setAction($order_cnpj_basico);

        $this->datagrid->addColumn($column_cnpj_basico);
        //$this->datagrid->addColumn($column_cnpj_ordem);
        //$this->datagrid->addColumn($column_cnpj_dv);
        $this->datagrid->addColumn($column_identificador_matriz_filial);
        $this->datagrid->addColumn($column_nome_fantasia);
        $this->datagrid->addColumn($column_situacao_cadastral);
        $this->datagrid->addColumn($column_data_situacao_cadastral_transformed);
        $this->datagrid->addColumn($column_data_inicio_atividade_transformed);
        $this->datagrid->addColumn($column_cnae_fiscal_principal);
        $this->datagrid->addColumn($column_uf);
        $this->datagrid->addColumn($column_municipio);
        $this->datagrid->addColumn($column_situacao_especial);
        $this->datagrid->addColumn($column_data_situacao_especial_transformed);


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
            $container->add(TBreadCrumb::create(["Tabelas","Estabelicimento"]));
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