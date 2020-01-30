<?php
/**
 * SystemSQLPanel
 *
 * @version    1.0
 * @package    control
 * @subpackage admin
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class SystemSQLPanel extends TPage
{
    private $form;
    private $container;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('sqlpanel');
        $this->form->setFormTitle('SQL Panel');
        
        $list = scandir('app/config');
        $options = SystemDatabaseInformationService::getConnections( true );
        
        $database = new TCombo('database');
        $table = new TCombo('table');
        $select = new TText('select');
        $select->style = 'font-family: Andale mono, DejaVu Sans Mono, Bitstream Vera Sans Mono, Lucida Console, Monaco, Consolas, Droid Sans monospace, Monospace; background: #2f2f2f; color: white';
        
        //$database->enableSearch();
        //$table->enableSearch();
        
        $this->form->addFields( [ $ld=new TLabel(_t('Database'))], [ $database], [$lt=new TLabel(_t('Table'))], [$table] );
        $this->form->addFields( [ $ls=new TLabel('SELECT')], [$select] );
        
        $btn = $this->form->addAction( _t('Generate'), new TAction(array($this, 'onGenerate')), 'fa:check-circle');
        $btn->class = 'btn btn-sm btn-primary';
        
        $this->form->addAction( 'CSV', new TAction(array($this, 'onExportCSV')), 'fa:table');
        
        $ld->setFontColor('red');
        $lt->setFontColor('red');
        $ls->setFontColor('red');
        
        $database->addItems($options);
        $database->setChangeAction(new TAction(array($this, 'onDatabaseChange')));
        $table->setChangeAction(new TAction(array($this, 'onTableChange')));
        $select->addValidation( 'SELECT', new TRequiredValidator );
        $database->addValidation(_t('Database'), new TRequiredValidator);
        $table->addValidation(_t('Table'), new TRequiredValidator);
        $database->setSize('100%');
        $table->setSize('100%');
        $select->setSize('100%', 150);
        
        $this->container = new TVBox;
        $this->container->style = 'width: 100%';
        $this->container->add(new TXMLBreadCrumb('menu.xml','SystemProgramList'));
        $this->container->add($this->form);
        
        parent::add($this->container);
    }
    
    /**
     * onDatabaseChange
     */
    public static function onDatabaseChange($param)
    {
        try
        {
            $tables = SystemDatabaseInformationService::getDatabaseTables( $param['database'] );
            if ($tables)
            {
                TCombo::reload('sqlpanel', 'table', $tables, true);
            }
            else
            {
                TCombo::clearField('sqlpanel', 'table');
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * onTableChange
     */
    public static function onTableChange($param)
    {
        if (!empty($param['table']))
        {
            $table = $param['table'];
            $obj = new stdClass;
            $obj->select = "SELECT * FROM {$table}";
            TForm::sendData('sqlpanel', $obj);
        }
    }
    
    public function onLoad($param)
    {
        $obj = new stdClass;
        $obj->database = $param['database'];
        $obj->table    = $param['table'];
        $obj->select = "SELECT * FROM {$obj->table}";
        TForm::sendData('sqlpanel', $obj);
    }
    
    /**
     * onGenerate
     */
    public function onGenerate($param)
    {
        try
        {
            self::onDatabaseChange($param);
            $obj = new stdClass;
            
            // keep table filled via javascript
            if (isset($param['table']))
            {
                $obj->table = $param['table'];
                $obj->select = $param['select'];
                TForm::sendData('sqlpanel', $obj, false, false);
            }
            
            $this->form->validate();
            $data = $this->form->getData();
            
            if (strtoupper(substr( $data->select, 0, 6)) !== 'SELECT')
            {
                throw new Exception(_t('Invalid command'));
            }
            // creates a DataGrid
            $datagrid = new BootstrapDatagridWrapper(new TDataGrid);
            $datagrid->datatable = 'true';
            $datagrid->width = '100%';
            
            $panel = new TPanelGroup( _t('Results') );
            $panel->add($datagrid);
            
            TTransaction::open( $data->database );
            $conn = TTransaction::get();
            $result = $conn->query( $data->select );
            $row = $result->fetch();
            
            $i = 0;
            if ($row)
            {
                foreach ($row as $key => $value)
                {
                    if (is_string($key))
                    {
                        $col = new TDataGridColumn($key, $key, 'left');
                        $datagrid->addColumn($col);
                    }
                }
                
                // create the datagrid model
                $datagrid->createModel();
                
                $datagrid->addItem( (object) $row );
                
                $i = 1;
                while ($row = $result->fetch() AND $i<= 1000)
                {
                    $datagrid->addItem( (object) $row );
                    $i ++;
                }
            }
            $panel->addFooter( _t('^1 records shown', "<b>{$i}</b>"));
            $this->container->add($panel);
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Export as CSV
     */
    public function onExportCSV($param)
    {
        try
        {
            self::onDatabaseChange($param);
            $obj = new stdClass;
            
            // keep table filled via javascript
            if (isset($param['table']))
            {
                $obj->table = $param['table'];
                $obj->select = $param['select'];
                TForm::sendData('sqlpanel', $obj, false, false);
            }
            
            $this->form->validate();
            $data = $this->form->getData();
            
            if (strtoupper(substr( $data->select, 0, 6)) !== 'SELECT')
            {
                throw new Exception(_t('Invalid command'));
            }
            
            if (!is_writable('tmp'))
            {
                throw new Exception( _t('Permission denied') . ': tmp');
            }
            
            TTransaction::open( $data->database );
            $conn = TTransaction::get();
            $result = $conn->query( $data->select );
            
            $file = 'tmp/sql' . mt_rand(1000000000, 1999999999) . '.csv';
            $handler = fopen($file, 'w');
            
            $first_row = $result->fetch( PDO::FETCH_ASSOC );
            if ($first_row)
            {
                // CSV headers
                fputcsv($handler, array_keys($first_row));
                fputcsv($handler, $first_row);
                
                // add other rows
                while ($row = $result->fetch( PDO::FETCH_ASSOC ))
                {
                    fputcsv($handler, $row);
                }
                
                fclose($handler);
                parent::openFile($file);
            }
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}
