<?php
/**
 * SystemDatabaseExplorer
 *
 * @version    1.0
 * @package    control
 * @subpackage admin
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class SystemDatabaseExplorer extends TPage
{
    private $datagrid;
    
    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();
        
        $panel = new TPanelGroup(_t('Database'));
        $panel->style = 'padding-bottom:8px';
        $panel->getBody()->style = 'overflow-y:auto;';
        
        // create datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
        $db_col = $this->datagrid->addQuickColumn('Database', 'database', 'left');
        
        // create action
        $action1 = new TDataGridAction(array('SystemTableList', 'onLoad'));
        $action1->setParameter('register_state', 'false');
        $action1->setImage('fa:table');
        $action1->setField('database');
        $action1->setLabel('View');
        
        $action2 = new TDataGridAction(array($this, 'onExportCSV'));
        $action2->setParameter('register_state', 'false');
        $action2->setImage('fa:download');
        $action2->setField('database');
        $action2->setLabel('CSV');
        
        $action3 = new TDataGridAction(array($this, 'onExportSQL'));
        $action3->setParameter('register_state', 'false');
        $action3->setImage('fa:code');
        $action3->setField('database');
        $action3->setLabel('SQL');
        
        $agroup = new TDataGridActionGroup( null, 'fa:list');
        $agroup->addAction($action1);
        $agroup->addAction($action2);
        $agroup->addAction($action3);
        
        $this->datagrid->addActionGroup($agroup);
        
        $this->datagrid->createModel( false );
        $panel->add($this->datagrid);
        
        // transformer to format database name
        $db_col->setTransformer( function ($value, $object) {
            return $value . ' (<i>'.$object->type.'</i>)';
        });
        
        // load database connections into datagrid
        $list = scandir('app/config');
        
        $options = [];
        foreach ($list as $entry)
        {
            if ( (substr($entry, -4) == '.ini') || (substr($entry, -4) == '.php') )
            {
                $connector = str_replace(['.ini', '.php'], ['', ''], $entry);
                $ini = TConnection::getDatabaseInfo($connector);
                
                if (!empty($ini['type']) && in_array($ini['type'], ['pgsql', 'mysql', 'sqlite', 'oracle', 'mssql']))
                {
                    $options[ $connector ] = $connector;
                    $this->datagrid->addItem( (object) ['database' => $connector, 'type' => $ini['type']] );
                }
            }
        }
        
        // render html
        $replaces['database_browser'] = $panel;
        $html = new THtmlRenderer('app/resources/system_database_browser.html');
        $html->enableSection('main', $replaces);
        
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($html);
        
        // fix height
        TScript::create("$('#database_browser_container .panel-body').height( (($(window).height()-260)/2)-100);");
        parent::add($vbox);
    }
    
    /**
     * Export database
     */
    public static function onExportCSV($param)
    {
        try
        {
            $database = $param['database'];
            $files = [];
            
            if (!is_writable('tmp'))
            {
                throw new Exception( _t('Permission denied') . ': tmp');
            }
            
            if (!extension_loaded('zip'))
            {
                throw new Exception( AdiantiCoreTranslator::translate('PHP Module not found') . ': zip' );
            }
            
            $zip = new ZipArchive();
            $output = 'tmp/' . $database. '.zip';
            if (file_exists($output))
            {
                unlink($output);
            }
            
            if (!$zip->open($output, ZIPARCHIVE::CREATE))
            {
                throw new Exception( _t('Permission denied') . ': ' . $output);
            }
            
            // open transaction
            TTransaction::open( $database );
            $conn = TTransaction::get();
            
            $tables = SystemDatabaseInformationService::getDatabaseTables( $database );
            if ($tables)
            {
                foreach ($tables as $table)
                {
                    // run the main query
                    $sql = new TSqlSelect;
                    $sql->setCriteria(new TCriteria);
                    $sql->addColumn('*');
                    $sql->setEntity($table);
                    $result = $conn->query( $sql->getInstruction() );
                    
                    $file = 'tmp/' . $table . '.csv';
                    $files[] = $file;
                    
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
                        $zip->addFile($file);
                    }
                }
                $zip->close();
                parent::openFile($output);
            }
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Export database
     */
    public static function onExportSQL($param)
    {
        try
        {
            $database = $param['database'];
            $files = [];

            if (!is_writable('tmp'))
            {
                throw new Exception( _t('Permission denied') . ': tmp');
            }

            if (!extension_loaded('zip'))
            {
                throw new Exception( AdiantiCoreTranslator::translate('PHP Module not found') . ': zip' );
            }
            
            $zip = new ZipArchive();
            $output = 'tmp/' . $database. '.zip';
            if (file_exists($output))
            {
                unlink($output);
            }
            if (!$zip->open($output, ZIPARCHIVE::CREATE))
            {
                throw new Exception( _t('Permission denied') . ': ' . $output);
            }

            // open transaction
            TTransaction::open( $database );
            $conn = TTransaction::get();

            $tables = SystemDatabaseInformationService::getDatabaseTables( $database );
            if ($tables)
            {
                foreach ($tables as $table)
                {
                    // run the main query
                    $sql = new TSqlSelect;
                    $sql->setCriteria(new TCriteria);
                    $sql->addColumn('*');
                    $sql->setEntity($table);
                    $result = $conn->query( $sql->getInstruction() );
                    
                    $file = 'tmp/' . $table . '.sql';
                    $files[] = $file;
                    
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
                        $zip->addFile($file);
                    }
                }
                $zip->close();
                parent::openFile($output);
            }
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}
