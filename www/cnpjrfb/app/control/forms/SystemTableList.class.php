<?php
/**
 * SystemTableList
 *
 * @version    1.0
 * @package    control
 * @subpackage admin
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class SystemTableList extends TPage
{
    private $datagrid;
    
    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();
        
        // define the ID of target container
        $this->adianti_target_container = 'table_list_container';
        
        // create datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->datagrid->addQuickColumn('Table', 'table', 'left');
        $this->datagrid->id = 'datagrid_' . mt_rand(1000000000, 1999999999);
        
        $action1 = new TDataGridAction(array('SystemDataBrowser', 'onLoad'));
        $action1->setParameter('register_state', 'false');
        $action1->setImage('fa:table');
        $action1->setFields(['database', 'table']);
        $action1->setLabel('View');
        
        $action2 = new TDataGridAction(array($this, 'onExportCSV'));
        $action2->setParameter('register_state', 'false');
        $action2->setImage('fa:download');
        $action2->setFields(['database', 'table']);
        $action2->setLabel('CSV');
        
        $action3 = new TDataGridAction(array($this, 'onExportSQL'));
        $action3->setParameter('register_state', 'false');
        $action3->setImage('fa:code');
        $action3->setFields(['database', 'table']);
        $action3->setLabel('SQL');
        
        $agroup = new TDataGridActionGroup( null, 'fa:list');
        $agroup->addAction($action1);
        $agroup->addAction($action2);
        $agroup->addAction($action3);
        
        $this->datagrid->addActionGroup($agroup);
        
        $this->datagrid->createModel( false );
        
        $input_search = new TEntry('input_search');
        $input_search->placeholder = _t('Search');
        $input_search->setSize('100%');
        
        $hbox = new THBox;
        $hbox->style = 'display:block';
        $hbox->add( _t('Tables') )->style = 'float:left;width:50%';
        $hbox->add( $input_search )->style = 'float:right;width:50%;display:block;background:white';
        
        $this->datagrid->enableSearch($input_search, 'table');
        
        // panel group around datagrid
        $panel = new TPanelGroup( $hbox );
        $panel->style = 'padding-bottom:8px';
        $panel->getBody()->style = 'overflow-y:auto';
        $panel->add($this->datagrid);
        
        parent::add($panel);
    }
    
    /**
     * Load tables into datagrid
     */
    public function onLoad($param)
    {
        try
        {
            $tables = SystemDatabaseInformationService::getDatabaseTables( $param['database'] );
            if ($tables)
            {
                foreach ($tables as $table)
                {
                    $row = $this->datagrid->addItem( (object) ['table' => $table, 'database' => $param['database'] ]);
                    $row->id = 'table_' . mt_rand(1000000000, 1999999999);
                    $row->name = $table;
                }
            }
            
            // fix height
            TScript::create("$('#table_list_container .panel-body').height( ($(window).height()-260)/2 );");
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     *
     */
    public static function onExportCSV($param)
    {
        try
        {
            $database = $param['database'];
            $table    = $param['table'];
            
            if (!is_writable('tmp'))
            {
                throw new Exception( _t('Permission denied') . ': tmp');
            }
            
            // open transaction
            TTransaction::open( $database );
            $conn = TTransaction::get();
            
            // run the main query
            $sql = new TSqlSelect;
            $sql->setCriteria(new TCriteria);
            $sql->addColumn('*');
            $sql->setEntity($table);
            $result = $conn->query( $sql->getInstruction() );
            
            $file = 'tmp/' . $table . '.csv';
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
    
    /**
     *
     */
    public static function onExportSQL($param)
    {
        try
        {
            $database = $param['database'];
            $table    = $param['table'];
            
            if (!is_writable('tmp'))
            {
                throw new Exception( _t('Permission denied') . ': tmp');
            }
            
            // open transaction
            TTransaction::open( $database );
            $conn = TTransaction::get();
            
            // run the main query
            $sql = new TSqlSelect;
            $sql->setCriteria(new TCriteria);
            $sql->addColumn('*');
            $sql->setEntity($table);
            $result = $conn->query( $sql->getInstruction() );
            
            $file = 'tmp/' . $table . '.sql.txt';
            $handler = fopen($file, 'w');
            
            $addquotes = function($value) {
                            if(!is_numeric($value)) {
                                return "'{$value}'";
                            } else {
                                return $value;
                            }
                        };
                        
            $first_row = $result->fetch( PDO::FETCH_ASSOC );
            if ($first_row)
            {
                $columns = implode(',', array_keys($first_row));
                $values  = implode(',', array_map($addquotes, array_values($first_row)));
                fwrite($handler, "INSERT INTO {$table} ({$columns}) VALUES ({$values});\n");
                
                // add other rows
                while ($row = $result->fetch( PDO::FETCH_ASSOC ))
                {
                    $values  = implode(',', array_map($addquotes, array_values($row)));
                    fwrite($handler, "INSERT INTO {$table} ({$columns}) VALUES ({$values});\n");
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
