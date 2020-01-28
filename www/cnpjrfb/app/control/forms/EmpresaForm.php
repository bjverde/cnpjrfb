<?php

class EmpresaForm extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    function __construct()
    {
        parent::__construct();

        $this->setDatabase('cnpj_full'); // define the database
        $this->setActiveRecord('Empresa'); // define the Active Record
        $this->addFilterField('cnpj', 'razao_social', 'nome_fantasia', 'situacao'); //campo, operador, campo do form
        $this->setDefaultOrder('cnpj', 'asc'); // define the default order

        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Empresas');
        $this->form->generateAria(); // automatic aria-label

        $situacaoCadastralControler = new SituacaoCadastralEmpresa();
        $listSituacaoCadastral = $situacaoCadastralControler->getList();
        
        $cnpj = new TEntry('cnpj');
        $comboSituacao  = new TCombo('motivo_situacao');
        $comboSituacao->addItems($listSituacaoCadastral);

        $this->form->addFields( [new TLabel('CNPJ')],[$cnpj]);
        $this->form->addFields( [new TLabel('Situação')], [$comboSituacao] );

        // add form actions
        $this->form->addAction('Find', new TAction([$this, 'onSearch']), 'fa:search blue');        
        $this->form->addActionLink('Clear',  new TAction([$this, 'clear']), 'fa:eraser red');

        // keep the form filled with the search data
        $this->form->setData( TSession::getValue('StandardDataGridView_filter_data') );

        // create the datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';
        
        // add the columns
        $col_cnpj           = new TDataGridColumn('cnpj', 'CNPJ', 'right');
        $col_tipo_socio     = new TDataGridColumn('razao_social', 'Razão Social', 'left');
        $col_nome_fantasia  = new TDataGridColumn('nome_fantasia', 'Nome Fantasia', 'left');
        $col_situacao       = new TDataGridColumn('situacao', 'Situacao', 'left');
        $col_uf             = new TDataGridColumn('uf', 'UF', 'left');
        $col_municipio      = new TDataGridColumn('municipio', 'Municipio', 'left');
        
        $this->datagrid->addColumn($col_cnpj);
        $this->datagrid->addColumn($col_tipo_socio);
        $this->datagrid->addColumn($col_nome_fantasia);
        $this->datagrid->addColumn($col_situacao);
        $this->datagrid->addColumn($col_uf);
        $this->datagrid->addColumn($col_municipio);

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
