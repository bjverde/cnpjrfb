<?php
/**
 * CnaesSecundarioList Listing
 * @author  <your name here>
 */
class CnaesSecundarioForm extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    
    use Adianti\base\AdiantiStandardListTrait;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('cnpj_full');            // defines the database
        $this->setActiveRecord('CnaesSecundario');   // defines the active record
        $this->setDefaultOrder('cnpj', 'asc');         // defines the default order
        $this->setLimit(20);
        // $this->setCriteria($criteria) // define a standard filter

        $this->addFilterField('cnpj', 'like', 'cnpj'); // filterField, operator, formField
        $this->addFilterField('cnae_ordem', 'like', 'cnae_ordem'); // filterField, operator, formField
        $this->addFilterField('cnae', 'like', 'cnae'); // filterField, operator, formField
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_CnaesSecundario');
        $this->form->setFormTitle('CnaesSecundario');
        

        // create the form fields
        $cnpj = new TEntry('cnpj');
        $cnae_ordem = new TEntry('cnae_ordem');
        $cnae = new TEntry('cnae');


        // add the fields
        $this->form->addFields( [ new TLabel('Cnpj') ], [ $cnpj ] );
        $this->form->addFields( [ new TLabel('Cnae Ordem') ], [ $cnae_ordem ] );
        $this->form->addFields( [ new TLabel('Cnae') ], [ $cnae ] );


        // set sizes
        $cnpj->setSize('100%');
        $cnae_ordem->setSize('100%');
        $cnae->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['CnaesSecundarioForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_cnpj = new TDataGridColumn('cnpj', 'Cnpj', 'left');
        $column_cnae_ordem = new TDataGridColumn('cnae_ordem', 'Cnae Ordem', 'left');
        $column_cnae = new TDataGridColumn('cnae', 'Cnae', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_cnpj);
        $this->datagrid->addColumn($column_cnae_ordem);
        $this->datagrid->addColumn($column_cnae);

        
        $action1 = new TDataGridAction(['CnaesSecundarioForm', 'onEdit'], ['cnpj'=>'{cnpj}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['cnpj'=>'{cnpj}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        $panel = new TPanelGroup('', 'white');
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        // header actions
        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction( _t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table blue' );
        $dropdown->addAction( _t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf red' );
        $panel->addHeaderWidget( $dropdown );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
    }
}
