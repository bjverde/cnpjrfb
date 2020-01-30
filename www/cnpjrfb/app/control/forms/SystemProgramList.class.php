<?php
/**
 * SystemProgramList
 *
 * @version    1.0
 * @package    control
 * @subpackage admin
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class SystemProgramList extends TStandardList
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    protected $transformCallback;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        parent::setDatabase('permission');            // defines the database
        parent::setActiveRecord('SystemProgram');   // defines the active record
        parent::setDefaultOrder('id', 'asc');         // defines the default order
        // parent::setCriteria($criteria) // define a standard filter

        parent::addFilterField('id', '=', 'id'); // filterField, operator, formField
        parent::addFilterField('name', 'like', 'name'); // filterField, operator, formField
        parent::addFilterField('controller', 'like', 'controller'); // filterField, operator, formField
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_SystemProgram');
        $this->form->setFormTitle(_t('Programs'));
        

        // create the form fields
        $name = new TEntry('name');
        $controller = new TEntry('controller');

        // add the fields
        $this->form->addFields( [new TLabel(_t('Name'))], [$name] );
        $this->form->addFields( [new TLabel(_t('Controller'))], [$controller] );
        $name->setSize('70%');
        $controller->setSize('70%');
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('SystemProgram_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('New'),  new TAction(array('SystemProgramForm', 'onEdit')), 'fa:plus green');
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        //$this->datagrid->datatable = 'true';
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        
        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'center', 50);
        $column_controller = new TDataGridColumn('controller', _t('Controller'), 'left');
        $column_name = new TDataGridColumn('name', _t('Name'), 'left');
        $column_menu = new TDataGridColumn('controller', _t('Menu path'), 'left');

        $column_name->enableAutoHide(500);
        $column_menu->enableAutoHide(500);
        
        $column_menu->setTransformer( function($value, $object, $row) {
            $menuparser = new TMenuParser('menu.xml');
            $paths = $menuparser->getPath($value);
            
            if ($paths)
            {
                return implode(' &raquo; ', $paths);
            }
        });

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_controller);
        $this->datagrid->addColumn($column_name);
        $this->datagrid->addColumn($column_menu);


        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_name = new TAction(array($this, 'onReload'));
        $order_name->setParameter('order', 'name');
        $column_name->setAction($order_name);
        
        $order_controller = new TAction(array($this, 'onReload'));
        $order_controller->setParameter('order', 'controller');
        $column_controller->setAction($order_controller);
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('SystemProgramForm', 'onEdit'));
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue ');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        $action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red ');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);
        
        // create EXECUTE action
        $action_ope = new TDataGridAction(array($this, 'onOpen'));
        $action_ope->setButtonClass('btn btn-default');
        $action_ope->setLabel(_t('Open'));
        $action_ope->setImage('far:folder-open green ');
        $action_ope->setField('controller');
        $this->datagrid->addAction($action_ope);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        $panel = new TPanelGroup;
        $panel->add($this->datagrid)->style='overflow-x:auto';
        $panel->addFooter($this->pageNavigation);
        
        // header actions
        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction( _t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table fa-fw blue' );
        $dropdown->addAction( _t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf fa-fw red' );
        $dropdown->addAction( _t('Save as XML'), new TAction([$this, 'onExportXML'], ['register_state' => 'false', 'static'=>'1']), 'fa:code fa-fw green' );
        $panel->addHeaderWidget( $dropdown );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
    }
    
    /**
     * Open controller
     */
    public function onOpen($param)
    {
        AdiantiCoreApplication::loadPage($param['controller']);
    }
    
    /**
     * Display condition
     */
    public function displayBuilderActions($object)
    {
        return ( (strpos($object->controller, 'System') === false) and !in_array($object->controller, ['CommonPage', 'WelcomeView']));
    }
}
